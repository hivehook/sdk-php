<?php

declare(strict_types=1);

namespace Hivehook;

use Hivehook\Resources\SourceService;
use Hivehook\Resources\DestinationService;
use Hivehook\Resources\SubscriptionService;
use Hivehook\Resources\EventService;
use Hivehook\Resources\DeliveryService;
use Hivehook\Resources\DlqService;
use Hivehook\Resources\ApiKeyService;
use Hivehook\Resources\AlertRuleService;
use Hivehook\Resources\BookmarkService;
use Hivehook\Resources\EventTypeSchemaService;
use Hivehook\Resources\ApplicationService;
use Hivehook\Resources\EndpointService;
use Hivehook\Resources\MessageService;
use Hivehook\Resources\OutboundDeliveryService;
use Hivehook\Resources\OutboundDlqService;
use Hivehook\Resources\StatusService;
use Hivehook\Resources\TransformationService;
use Hivehook\Resources\PortalService;
use Hivehook\Resources\StreamService;
use Hivehook\Resources\StreamConsumerService;
use Hivehook\Resources\StreamSinkService;
use Hivehook\Resources\OrganizationService;
use Hivehook\Resources\UserService;
use Hivehook\Resources\AuditLogService;
use Hivehook\Resources\MetaEventConfigService;

class HivehookClient
{
    public readonly SourceService $sources;
    public readonly DestinationService $destinations;
    public readonly SubscriptionService $subscriptions;
    public readonly EventService $events;
    public readonly DeliveryService $deliveries;
    public readonly DlqService $dlq;
    public readonly ApiKeyService $apiKeys;
    public readonly AlertRuleService $alertRules;
    public readonly BookmarkService $bookmarks;
    public readonly EventTypeSchemaService $eventTypeSchemas;
    public readonly ApplicationService $applications;
    public readonly EndpointService $endpoints;
    public readonly MessageService $messages;
    public readonly OutboundDeliveryService $outboundDeliveries;
    public readonly OutboundDlqService $outboundDlq;
    public readonly StatusService $status;
    public readonly TransformationService $transformations;
    public readonly PortalService $portal;
    public readonly StreamService $streams;
    public readonly StreamConsumerService $streamConsumers;
    public readonly StreamSinkService $streamSinks;
    public readonly OrganizationService $organizations;
    public readonly UserService $users;
    public readonly AuditLogService $auditLogs;
    public readonly MetaEventConfigService $metaEventConfigs;

    public function __construct(
        string $baseUrl = 'http://localhost:8080',
        ?string $apiKey = null,
        int $maxRetries = 2,
        int $timeoutSeconds = 30,
    ) {
        $transport = new GraphQLTransport($baseUrl, $apiKey, $maxRetries, $timeoutSeconds);
        $this->sources = new SourceService($transport);
        $this->destinations = new DestinationService($transport);
        $this->subscriptions = new SubscriptionService($transport);
        $this->events = new EventService($transport);
        $this->deliveries = new DeliveryService($transport);
        $this->dlq = new DlqService($transport);
        $this->apiKeys = new ApiKeyService($transport);
        $this->alertRules = new AlertRuleService($transport);
        $this->bookmarks = new BookmarkService($transport);
        $this->eventTypeSchemas = new EventTypeSchemaService($transport);
        $this->applications = new ApplicationService($transport);
        $this->endpoints = new EndpointService($transport);
        $this->messages = new MessageService($transport);
        $this->outboundDeliveries = new OutboundDeliveryService($transport);
        $this->outboundDlq = new OutboundDlqService($transport);
        $this->status = new StatusService($transport);
        $this->transformations = new TransformationService($transport);
        $this->portal = new PortalService($transport);
        $this->streams = new StreamService($transport);
        $this->streamConsumers = new StreamConsumerService($transport);
        $this->streamSinks = new StreamSinkService($transport);
        $this->organizations = new OrganizationService($transport);
        $this->users = new UserService($transport);
        $this->auditLogs = new AuditLogService($transport);
        $this->metaEventConfigs = new MetaEventConfigService($transport);
    }
}
