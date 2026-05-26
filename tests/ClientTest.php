<?php

declare(strict_types=1);

namespace Hivehook\Tests;

use PHPUnit\Framework\TestCase;
use Hivehook\HivehookClient;

class ClientTest extends TestCase
{
    public function testClientHasAllServices(): void
    {
        $client = new HivehookClient();
        $this->assertInstanceOf(\Hivehook\Resources\SourceService::class, $client->sources);
        $this->assertInstanceOf(\Hivehook\Resources\DestinationService::class, $client->destinations);
        $this->assertInstanceOf(\Hivehook\Resources\SubscriptionService::class, $client->subscriptions);
        $this->assertInstanceOf(\Hivehook\Resources\EventService::class, $client->events);
        $this->assertInstanceOf(\Hivehook\Resources\DeliveryService::class, $client->deliveries);
        $this->assertInstanceOf(\Hivehook\Resources\DlqService::class, $client->dlq);
        $this->assertInstanceOf(\Hivehook\Resources\ApiKeyService::class, $client->apiKeys);
        $this->assertInstanceOf(\Hivehook\Resources\AlertRuleService::class, $client->alertRules);
        $this->assertInstanceOf(\Hivehook\Resources\BookmarkService::class, $client->bookmarks);
        $this->assertInstanceOf(\Hivehook\Resources\EventTypeSchemaService::class, $client->eventTypeSchemas);
        $this->assertInstanceOf(\Hivehook\Resources\ApplicationService::class, $client->applications);
        $this->assertInstanceOf(\Hivehook\Resources\EndpointService::class, $client->endpoints);
        $this->assertInstanceOf(\Hivehook\Resources\MessageService::class, $client->messages);
        $this->assertInstanceOf(\Hivehook\Resources\OutboundDeliveryService::class, $client->outboundDeliveries);
        $this->assertInstanceOf(\Hivehook\Resources\OutboundDlqService::class, $client->outboundDlq);
        $this->assertInstanceOf(\Hivehook\Resources\StatusService::class, $client->status);
        $this->assertInstanceOf(\Hivehook\Resources\TransformationService::class, $client->transformations);
        $this->assertInstanceOf(\Hivehook\Resources\PortalService::class, $client->portal);
        $this->assertInstanceOf(\Hivehook\Resources\StreamService::class, $client->streams);
        $this->assertInstanceOf(\Hivehook\Resources\StreamConsumerService::class, $client->streamConsumers);
        $this->assertInstanceOf(\Hivehook\Resources\StreamSinkService::class, $client->streamSinks);
        $this->assertInstanceOf(\Hivehook\Resources\OrganizationService::class, $client->organizations);
        $this->assertInstanceOf(\Hivehook\Resources\UserService::class, $client->users);
        $this->assertInstanceOf(\Hivehook\Resources\AuditLogService::class, $client->auditLogs);
    }
}
