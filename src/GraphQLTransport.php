<?php

declare(strict_types=1);

namespace Hivehook;

use Hivehook\Exceptions\ApiException;
use Hivehook\Exceptions\AuthException;
use Hivehook\Exceptions\ConflictException;
use Hivehook\Exceptions\NotFoundException;
use Hivehook\Exceptions\RateLimitException;
use Hivehook\Exceptions\ServerException;
use Hivehook\Exceptions\ValidationException;

class GraphQLTransport
{
    public const VERSION = '0.1.0';

    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $apiKey = null,
        private readonly int $maxRetries = 2,
        private readonly int $timeoutSeconds = 30,
    ) {}

    public function execute(string $query, array $variables = []): array
    {
        $payload = json_encode(['query' => $query, 'variables' => $variables]);

        $attempt = 0;
        while (true) {
            [$status, $body, $headers, $curlError] = $this->performRequest($payload);

            if ($body === false || $body === null) {
                if ($attempt < $this->maxRetries) {
                    $attempt++;
                    $this->sleepForRetry($attempt, null);
                    continue;
                }
                throw new ApiException("request failed: {$curlError}");
            }

            if ($status === 401) {
                throw new AuthException();
            }

            if ($status === 429) {
                $retryAfter = $this->parseRetryAfter($headers);
                if ($attempt < $this->maxRetries) {
                    $attempt++;
                    $this->sleepForRetry($attempt, $retryAfter);
                    continue;
                }
                $msg = $this->extractMessage($body) ?? 'rate limited';
                throw new RateLimitException($msg, $status, null, $retryAfter);
            }

            if ($status >= 500 && $status < 600) {
                if ($attempt < $this->maxRetries) {
                    $attempt++;
                    $this->sleepForRetry($attempt, null);
                    continue;
                }
                $msg = $this->extractMessage($body) ?? "server error: {$status}";
                throw new ServerException($msg, $status);
            }

            if ($status >= 400) {
                try {
                    $json = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    throw new ApiException("invalid JSON: " . $e->getMessage(), $status, null, $e);
                }
                $msg = $json['errors'][0]['message'] ?? $json['message'] ?? $body;
                $extensions = $json['errors'][0]['extensions'] ?? null;
                throw new ApiException($msg, $status, $extensions);
            }

            try {
                $json = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new ApiException("invalid JSON: " . $e->getMessage(), $status, null, $e);
            }

            if (!empty($json['errors'])) {
                $err = $json['errors'][0];
                $extensions = $err['extensions'] ?? null;
                $code = $extensions['code'] ?? null;
                $msg = $err['message'] ?? 'graphql error';

                match ($code) {
                    'NOT_FOUND' => throw new NotFoundException($msg, $status, $extensions),
                    'CONFLICT' => throw new ConflictException($msg, $status, $extensions),
                    'VALIDATION' => throw new ValidationException($msg, $status, $extensions),
                    default => throw new ApiException($msg, $status, $extensions),
                };
            }

            if (!isset($json['data'])) {
                throw new ApiException('empty response data', $status);
            }

            return $json['data'];
        }
    }

    /**
     * Executes the HTTP request.
     *
     * @return array{0:int,1:string|false|null,2:array<string,string>,3:string} [status, body, headers, curlError]
     */
    protected function performRequest(string $payload): array
    {
        $ch = curl_init("{$this->baseUrl}/graphql");
        $headers = [
            'Content-Type: application/json',
            'User-Agent: hivehook-php/' . self::VERSION,
        ];
        if ($this->apiKey !== null) {
            $headers[] = "Authorization: Bearer {$this->apiKey}";
        }

        $responseHeaders = [];
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_HEADERFUNCTION => function ($curl, $header) use (&$responseHeaders) {
                $len = strlen($header);
                $parts = explode(':', $header, 2);
                if (count($parts) === 2) {
                    $name = strtolower(trim($parts[0]));
                    $value = trim($parts[1]);
                    $responseHeaders[$name] = $value;
                }
                return $len;
            },
        ]);

        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        return [$status, $body, $responseHeaders, $curlError];
    }

    /**
     * @param array<string,string> $headers
     */
    private function parseRetryAfter(array $headers): ?int
    {
        $value = $headers['retry-after'] ?? null;
        if ($value === null || $value === '') {
            return null;
        }
        if (ctype_digit($value)) {
            return (int) $value;
        }
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }
        $delta = $timestamp - time();
        return $delta > 0 ? $delta : 0;
    }

    private function sleepForRetry(int $attempt, ?int $retryAfter): void
    {
        if ($retryAfter !== null) {
            if ($retryAfter > 0) {
                sleep($retryAfter);
            }
            return;
        }
        $delayMs = (int) min(5000, 100 * (1 << max(0, $attempt - 1)));
        usleep($delayMs * 1000);
    }

    private function extractMessage(string $body): ?string
    {
        try {
            $json = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
        if (!is_array($json)) {
            return null;
        }
        return $json['errors'][0]['message'] ?? $json['message'] ?? null;
    }
}
