<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class DestinationService extends BaseService
{
    private const FRAGMENT = 'id name url signingSecret status type typeConfig timeoutMs rateLimitRps headers authType deliveryMode ordered blockedDeliveryId healthScore disabledReason healthConfig { windowHours disableBelow } outputFormat createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($status: DestinationStatus, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            destinations(status: $status, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['status', 'search', 'limit', 'offset', 'after', 'first']))['destinations'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { destination(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['destination'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateDestinationInput!) { createDestination(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createDestination'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateDestinationInput!) { updateDestination(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateDestination'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteDestination(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteDestination'];
    }

    public function rotateSecret(string $id): array
    {
        $query = 'mutation($id: UUID!) { rotateDestinationSecret(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['rotateDestinationSecret'];
    }
}
