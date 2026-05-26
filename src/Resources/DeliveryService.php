<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class DeliveryService extends BaseService
{
    private const FRAGMENT = 'id eventId subscriptionId destinationId status attempts maxAttempts nextAttemptAt createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($eventId: UUID, $destinationId: UUID, $subscriptionId: UUID, $status: DeliveryStatus, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            deliveries(eventId: $eventId, destinationId: $destinationId, subscriptionId: $subscriptionId, status: $status, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['eventId', 'destinationId', 'subscriptionId', 'status', 'search', 'limit', 'offset', 'after', 'first']))['deliveries'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { delivery(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['delivery'];
    }
}
