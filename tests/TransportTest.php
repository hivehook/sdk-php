<?php

declare(strict_types=1);

namespace Hivehook\Tests;

use Hivehook\Exceptions\ApiException;
use Hivehook\Exceptions\AuthException;
use Hivehook\Exceptions\ConflictException;
use Hivehook\Exceptions\NotFoundException;
use Hivehook\Exceptions\RateLimitException;
use Hivehook\Exceptions\ServerException;
use Hivehook\Exceptions\ValidationException;
use Hivehook\GraphQLTransport;
use PHPUnit\Framework\TestCase;

/**
 * Test double that swaps out the network call.
 *
 * @internal
 */
final class TestableGraphQLTransport extends GraphQLTransport
{
    /** @var list<array{0:int,1:string|false|null,2:array<string,string>,3:string}> */
    public array $responses = [];
    public int $callCount = 0;
    /** @var list<int> */
    public array $sleeps = [];

    /**
     * @param array{0:int,1:string|false|null,2:array<string,string>,3:string} ...$responses
     */
    public function enqueue(array ...$responses): void
    {
        foreach ($responses as $r) {
            $this->responses[] = $r;
        }
    }

    protected function performRequest(string $payload): array
    {
        $this->callCount++;
        if (empty($this->responses)) {
            throw new \LogicException('no more queued responses');
        }
        return array_shift($this->responses);
    }
}

class TransportTest extends TestCase
{
    private function makeTransport(int $maxRetries = 2): TestableGraphQLTransport
    {
        return new TestableGraphQLTransport('http://localhost:8080', null, $maxRetries, 30);
    }

    private function ok(array $data): array
    {
        return [200, json_encode(['data' => $data]), [], ''];
    }

    public function test401RaisesAuthException(): void
    {
        $t = $this->makeTransport();
        $t->enqueue([401, json_encode(['message' => 'unauthorized']), [], '']);

        $this->expectException(AuthException::class);
        $t->execute('{ ping }');
    }

    public function test404RaisesNotFound(): void
    {
        $t = $this->makeTransport();
        $body = json_encode([
            'data' => null,
            'errors' => [[
                'message' => 'source not found',
                'extensions' => ['code' => 'NOT_FOUND', 'id' => 'abc'],
            ]],
        ]);
        $t->enqueue([200, $body, [], '']);

        try {
            $t->execute('{ source(id: "abc") }');
            $this->fail('expected NotFoundException');
        } catch (NotFoundException $e) {
            $this->assertSame('source not found', $e->getMessage());
            $this->assertSame(['code' => 'NOT_FOUND', 'id' => 'abc'], $e->extensions);
        }
    }

    public function testHttp404RaisesApiException(): void
    {
        $t = $this->makeTransport();
        $t->enqueue([404, json_encode(['message' => 'not found']), [], '']);

        try {
            $t->execute('{ ping }');
            $this->fail('expected ApiException');
        } catch (ApiException $e) {
            $this->assertSame(404, $e->statusCode);
        }
    }

    public function test429WithRetryAfterRetriesThenThrowsRateLimit(): void
    {
        $t = $this->makeTransport(maxRetries: 1);
        $headers = ['retry-after' => '0']; // 0 to avoid actually sleeping
        $body = json_encode(['message' => 'too many']);
        $t->enqueue(
            [429, $body, $headers, ''],
            [429, $body, $headers, ''],
        );

        try {
            $t->execute('{ ping }');
            $this->fail('expected RateLimitException');
        } catch (RateLimitException $e) {
            $this->assertSame(429, $e->statusCode);
            $this->assertSame(0, $e->retryAfter);
            $this->assertSame(2, $t->callCount);
        }
    }

    public function test429RetrySucceeds(): void
    {
        $t = $this->makeTransport(maxRetries: 2);
        $t->enqueue(
            [429, json_encode(['message' => 'slow down']), ['retry-after' => '0'], ''],
            $this->ok(['ping' => 'pong']),
        );

        $data = $t->execute('{ ping }');
        $this->assertSame(['ping' => 'pong'], $data);
        $this->assertSame(2, $t->callCount);
    }

    public function test5xxRetriesThenThrowsServerException(): void
    {
        $t = $this->makeTransport(maxRetries: 1);
        $t->enqueue(
            [503, '{"message":"down"}', [], ''],
            [503, '{"message":"down"}', [], ''],
        );

        try {
            $t->execute('{ ping }');
            $this->fail('expected ServerException');
        } catch (ServerException $e) {
            $this->assertSame(503, $e->statusCode);
            $this->assertSame(2, $t->callCount);
        }
    }

    public function test5xxRetrySucceeds(): void
    {
        $t = $this->makeTransport(maxRetries: 2);
        $t->enqueue(
            [500, '{"message":"oops"}', [], ''],
            $this->ok(['ping' => 'ok']),
        );

        $data = $t->execute('{ ping }');
        $this->assertSame(['ping' => 'ok'], $data);
        $this->assertSame(2, $t->callCount);
    }

    public function testConflictNotRetried(): void
    {
        $t = $this->makeTransport(maxRetries: 3);
        $body = json_encode([
            'errors' => [[
                'message' => 'already exists',
                'extensions' => ['code' => 'CONFLICT'],
            ]],
        ]);
        $t->enqueue([200, $body, [], '']);

        try {
            $t->execute('mutation { createSource { id } }');
            $this->fail('expected ConflictException');
        } catch (ConflictException $e) {
            $this->assertSame('already exists', $e->getMessage());
            $this->assertSame(['code' => 'CONFLICT'], $e->extensions);
            $this->assertSame(1, $t->callCount);
        }
    }

    public function testValidationNotRetried(): void
    {
        $t = $this->makeTransport(maxRetries: 3);
        $body = json_encode([
            'errors' => [[
                'message' => 'bad input',
                'extensions' => ['code' => 'VALIDATION', 'field' => 'url'],
            ]],
        ]);
        $t->enqueue([200, $body, [], '']);

        try {
            $t->execute('mutation { x }');
            $this->fail('expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertSame('bad input', $e->getMessage());
            $this->assertSame('url', $e->extensions['field']);
            $this->assertSame(1, $t->callCount);
        }
    }

    public function testMalformedJsonRaisesApiException(): void
    {
        $t = $this->makeTransport();
        $t->enqueue([200, 'not json{', [], '']);

        try {
            $t->execute('{ ping }');
            $this->fail('expected ApiException');
        } catch (ApiException $e) {
            $this->assertStringContainsString('invalid JSON', $e->getMessage());
            $this->assertNotNull($e->getPrevious());
        }
    }

    public function testRetryAfterHttpDate(): void
    {
        $t = $this->makeTransport(maxRetries: 1);
        // Past date → delta clamped to 0 (no actual sleep).
        $t->enqueue(
            [429, '{"message":"x"}', ['retry-after' => 'Wed, 01 Jan 2020 00:00:00 GMT'], ''],
            $this->ok(['ok' => true]),
        );

        $data = $t->execute('{ ping }');
        $this->assertSame(['ok' => true], $data);
    }
}
