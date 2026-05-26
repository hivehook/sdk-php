<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class StatusService extends BaseService
{
    public function get(): array
    {
        $query = 'query { status { status dlqSize outboundDlqSize queueDepth activeWorkers totalWorkers uptime version sourcesTotal destinationsTotal subscriptionsTotal eventsTotal eventsFailed deliveriesTotal deliveriesPending deliveriesDelivered messagesTotal outboundDeliveriesTotal outboundDeliveriesPending outboundDeliveriesFailed } }';
        return $this->transport->execute($query)['status'];
    }
}
