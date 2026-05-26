<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class OutboundDlqService extends BaseService
{
    private const FRAGMENT = 'id deliveryId messageId lastError replayedAt createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($messageId: UUID, $replayed: Boolean, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            outboundDlqEntries(messageId: $messageId, replayed: $replayed, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['messageId', 'replayed', 'search', 'limit', 'offset', 'after', 'first']))['outboundDlqEntries'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { outboundDlqEntry(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['outboundDlqEntry'];
    }

    public function replay(string $id): bool
    {
        $query = 'mutation($id: UUID!) { replayOutboundDlqEntry(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['replayOutboundDlqEntry'];
    }

    public function replayAll(): array
    {
        $query = 'mutation { replayAllOutboundDlq { deliveries } }';
        return $this->transport->execute($query)['replayAllOutboundDlq'];
    }

    public function purge(string $olderThan): array
    {
        $query = 'mutation($olderThan: String!) { purgeOutboundDlq(olderThan: $olderThan) { purged } }';
        return $this->transport->execute($query, ['olderThan' => $olderThan])['purgeOutboundDlq'];
    }
}
