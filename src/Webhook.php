<?php

declare(strict_types=1);

namespace Hivehook;

class Webhook
{
    public const HEADER_SIGNATURE = 'X-Hivehook-Signature';
    public const HEADER_TIMESTAMP = 'X-Hivehook-Timestamp';
    public const HEADER_MESSAGE_ID = 'X-Hivehook-Message-ID';

    public static function sign(string $payload, string $secret, int $timestamp): string
    {
        $message = "{$timestamp}.{$payload}";
        $hash = hash_hmac('sha256', $message, $secret);
        return "v1={$hash}";
    }

    /**
     * Verify an HMAC signature.
     *
     * Tolerance semantics:
     *  - null: skip timestamp check entirely
     *  - 0: strict, timestamp must match the current time exactly
     *  - >0: allow timestamps within +/- tolerance seconds
     */
    public static function verify(
        string $payload,
        string $secret,
        string $signature,
        int $timestamp,
        ?int $toleranceSeconds = null,
    ): bool {
        if ($toleranceSeconds !== null) {
            $age = abs(time() - $timestamp);
            if ($toleranceSeconds === 0) {
                if ($age !== 0) {
                    return false;
                }
            } elseif ($age > $toleranceSeconds) {
                return false;
            }
        }

        $v1 = self::extractV1($signature);
        if ($v1 === null) {
            return false;
        }

        $expected = self::sign($payload, $secret, $timestamp);
        return hash_equals($expected, "v1={$v1}");
    }

    public static function verifyWithRotation(
        string $payload,
        string $primary,
        ?string $secondary,
        string $signature,
        int $timestamp,
        ?int $toleranceSeconds = null,
    ): bool {
        if (self::verify($payload, $primary, $signature, $timestamp, $toleranceSeconds)) {
            return true;
        }
        if ($secondary !== null) {
            return self::verify($payload, $secondary, $signature, $timestamp, $toleranceSeconds);
        }
        return false;
    }

    /**
     * Extract the v1=… element from a (possibly multi-scheme) signature header,
     * e.g. "t=123,v1=abc,v0=xyz".
     */
    private static function extractV1(string $signature): ?string
    {
        foreach (explode(',', $signature) as $part) {
            $part = trim($part);
            if (str_starts_with($part, 'v1=')) {
                return substr($part, 3);
            }
        }
        return null;
    }
}
