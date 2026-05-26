<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class DlqService extends BaseService
{
    private const FRAGMENT = 'id deliveryId eventId lastError replayedAt createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($eventId: UUID, $replayed: Boolean, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            dlqEntries(eventId: $eventId, replayed: $replayed, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['eventId', 'replayed', 'search', 'limit', 'offset', 'after', 'first']))['dlqEntries'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { dlqEntry(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['dlqEntry'];
    }

    public function replay(string $id): bool
    {
        $query = 'mutation($id: UUID!) { replayDLQEntry(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['replayDLQEntry'];
    }

    public function replayAll(): array
    {
        $query = 'mutation { replayAllDLQ { deliveries } }';
        return $this->transport->execute($query)['replayAllDLQ'];
    }

    public function purge(string $olderThan): array
    {
        $query = 'mutation($olderThan: String!) { purgeDLQ(olderThan: $olderThan) { purged } }';
        return $this->transport->execute($query, ['olderThan' => $olderThan])['purgeDLQ'];
    }
}
