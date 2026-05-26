<?php

declare(strict_types=1);

namespace Hivehook\Exceptions;

class RateLimitException extends ApiException
{
    public function __construct(
        string $message = 'rate limited',
        ?int $statusCode = 429,
        ?array $extensions = null,
        public readonly ?int $retryAfter = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $extensions, $previous);
    }
}
