<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class EventService extends BaseService
{
    private const FRAGMENT = 'id sourceId idempotencyKey eventType rawBody status receivedAt';

    public function list(array $options = []): array
    {
        $query = 'query($sourceId: UUID, $eventType: String, $status: EventStatus, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            events(sourceId: $sourceId, eventType: $eventType, status: $status, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['sourceId', 'eventType', 'status', 'search', 'limit', 'offset', 'after', 'first']))['events'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { event(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['event'];
    }
}
