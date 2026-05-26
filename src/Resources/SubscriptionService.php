<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class SubscriptionService extends BaseService
{
    private const FRAGMENT = 'id name sourceId destinationId filterConfig enabled createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($sourceId: UUID, $destinationId: UUID, $enabled: Boolean, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            subscriptions(sourceId: $sourceId, destinationId: $destinationId, enabled: $enabled, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['sourceId', 'destinationId', 'enabled', 'search', 'limit', 'offset', 'after', 'first']))['subscriptions'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { subscription(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['subscription'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateSubscriptionInput!) { createSubscription(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createSubscription'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateSubscriptionInput!) { updateSubscription(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateSubscription'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteSubscription(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteSubscription'];
    }
}
