<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class OutboundDeliveryService extends BaseService
{
    private const FRAGMENT = 'id messageId endpointId status attempts maxAttempts nextAttemptAt createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($messageId: UUID, $endpointId: UUID, $status: DeliveryStatus, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            outboundDeliveries(messageId: $messageId, endpointId: $endpointId, status: $status, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['messageId', 'endpointId', 'status', 'search', 'limit', 'offset', 'after', 'first']))['outboundDeliveries'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { outboundDelivery(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['outboundDelivery'];
    }
}
