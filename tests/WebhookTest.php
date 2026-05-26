<?php

declare(strict_types=1);

namespace Hivehook\Tests;

use PHPUnit\Framework\TestCase;
use Hivehook\Webhook;

class WebhookTest extends TestCase
{
    private string $secret = 'whsec_test123';
    private string $payload = '{"event":"test"}';

    public function testHeaderConstants(): void
    {
        $this->assertSame('X-Hivehook-Signature', Webhook::HEADER_SIGNATURE);
        $this->assertSame('X-Hivehook-Timestamp', Webhook::HEADER_TIMESTAMP);
        $this->assertSame('X-Hivehook-Message-ID', Webhook::HEADER_MESSAGE_ID);
    }

    public function testSign(): void
    {
        $sig = Webhook::sign($this->payload, $this->secret, 1700000000);
        $this->assertMatchesRegularExpression('/^v1=[a-f0-9]{64}$/', $sig);
    }

    public function testDeterministicSignatures(): void
    {
        $sig1 = Webhook::sign($this->payload, $this->secret, 1700000000);
        $sig2 = Webhook::sign($this->payload, $this->secret, 1700000000);
        $this->assertSame($sig1, $sig2);
    }

    public function testDifferentSecrets(): void
    {
        $sig1 = Webhook::sign($this->payload, $this->secret, 1700000000);
        $sig2 = Webhook::sign($this->payload, 'different', 1700000000);
        $this->assertNotSame($sig1, $sig2);
    }

    public function testVerifyValid(): void
    {
        $ts = time();
        $sig = Webhook::sign($this->payload, $this->secret, $ts);
        $this->assertTrue(Webhook::verify($this->payload, $this->secret, $sig, $ts, 300));
    }

    public function testRejectWrongSecret(): void
    {
        $ts = time();
        $sig = Webhook::sign($this->payload, $this->secret, $ts);
        $this->assertFalse(Webhook::verify($this->payload, 'wrong', $sig, $ts, 300));
    }

    public function testRejectExpired(): void
    {
        $ts = time() - 600;
        $sig = Webhook::sign($this->payload, $this->secret, $ts);
        $this->assertFalse(Webhook::verify($this->payload, $this->secret, $sig, $ts, 300));
    }

    public function testSkipTimestampCheck(): void
    {
        $ts = time() - 600;
        $sig = Webhook::sign($this->payload, $this->secret, $ts);
        $this->assertTrue(Webhook::verify($this->payload, $this->secret, $sig, $ts));
    }

    public function testVerifyWithRotationPrimary(): void
    {
        $ts = time();
        $sig = Webhook::sign($this->payload, 'primary', $ts);
        $this->assertTrue(Webhook::verifyWithRotation($this->payload, 'primary', 'secondary', $sig, $ts, 300));
    }

    public function testVerifyWithRotationSecondary(): void
    {
        $ts = time();
        $sig = Webhook::sign($this->payload, 'secondary', $ts);
        $this->assertTrue(Webhook::verifyWithRotation($this->payload, 'primary', 'secondary', $sig, $ts, 300));
    }

    public function testVerifyWithRotationReject(): void
    {
        $ts = time();
        $this->assertFalse(Webhook::verifyWithRotation($this->payload, 'primary', 'secondary', 'v1=bad', $ts, 300));
    }

    public function testMultiSchemeSignatureHeader(): void
    {
        $ts = time();
        $sig = Webhook::sign($this->payload, $this->secret, $ts);
        $hash = substr($sig, 3);
        $multi = "t={$ts}, v1={$hash}, v0=ignored";
        $this->assertTrue(Webhook::verify($this->payload, $this->secret, $multi, $ts, 300));
    }

    public function testSignatureWithoutV1Rejected(): void
    {
        $ts = time();
        $this->assertFalse(Webhook::verify($this->payload, $this->secret, 't=123,v0=abc', $ts, 300));
    }

    public function testZeroToleranceIsStrict(): void
    {
        $ts = time() - 5;
        $sig = Webhook::sign($this->payload, $this->secret, $ts);
        $this->assertFalse(Webhook::verify($this->payload, $this->secret, $sig, $ts, 0));
    }

    public function testZeroToleranceAcceptsCurrent(): void
    {
        $ts = time();
        $sig = Webhook::sign($this->payload, $this->secret, $ts);
        $this->assertTrue(Webhook::verify($this->payload, $this->secret, $sig, $ts, 0));
    }
}
