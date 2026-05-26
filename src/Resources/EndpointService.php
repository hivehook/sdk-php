<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class EndpointService extends BaseService
{
    private const FRAGMENT = 'id applicationId url signingSecret status type typeConfig rateLimitRps timeoutMs headers authType deliveryMode ordered blockedDeliveryId healthScore disabledReason healthConfig { windowHours disableBelow } outputFormat createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($applicationId: UUID, $status: EndpointStatus, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            endpoints(applicationId: $applicationId, status: $status, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['applicationId', 'status', 'search', 'limit', 'offset', 'after', 'first']))['endpoints'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { endpoint(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['endpoint'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateEndpointInput!) { createEndpoint(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createEndpoint'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateEndpointInput!) { updateEndpoint(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateEndpoint'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteEndpoint(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteEndpoint'];
    }

    public function rotateSecret(string $id): array
    {
        $query = 'mutation($id: UUID!) { rotateEndpointSecret(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['rotateEndpointSecret'];
    }
}
