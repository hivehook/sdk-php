<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class OrganizationService extends BaseService
{
    private const FRAGMENT = 'id name slug ssoEnabled ssoProvider retentionEvents retentionMessages otlpConfig { endpoint headers insecure sampleRate } createdAt updatedAt';

    public function list(array $options = []): array
    {
        $query = 'query($search: String, $limit: Int, $offset: Int) {
            organizations(search: $search, limit: $limit, offset: $offset) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['search', 'limit', 'offset']))['organizations'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { organization(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['organization'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateOrganizationInput!) { createOrganization(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createOrganization'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateOrganizationInput!) { updateOrganization(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateOrganization'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteOrganization(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteOrganization'];
    }

    public function configureSSO(string $organizationId, array $input): array
    {
        $query = 'mutation($organizationId: UUID!, $input: SSOConfigInput!) { configureSSO(organizationId: $organizationId, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['organizationId' => $organizationId, 'input' => $input])['configureSSO'];
    }

    public function disableSSO(string $organizationId): array
    {
        $query = 'mutation($organizationId: UUID!) { disableSSO(organizationId: $organizationId) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['organizationId' => $organizationId])['disableSSO'];
    }

    public function updateRetention(string $organizationId, array $input): array
    {
        $query = 'mutation($organizationId: UUID!, $input: RetentionInput!) { updateOrganizationRetention(organizationId: $organizationId, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['organizationId' => $organizationId, 'input' => $input])['updateOrganizationRetention'];
    }

    public function deleteData(string $organizationId): bool
    {
        $query = 'mutation($organizationId: UUID!) { deleteOrganizationData(organizationId: $organizationId) }';
        return $this->transport->execute($query, ['organizationId' => $organizationId])['deleteOrganizationData'];
    }

    public function exportData(string $organizationId): array
    {
        $query = 'mutation($organizationId: UUID!) { exportOrganizationData(organizationId: $organizationId) }';
        return $this->transport->execute($query, ['organizationId' => $organizationId])['exportOrganizationData'];
    }

    public function configureOTLP(string $organizationId, array $input): array
    {
        $query = 'mutation($organizationId: UUID!, $input: OTLPConfigInput!) { configureOTLP(organizationId: $organizationId, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['organizationId' => $organizationId, 'input' => $input])['configureOTLP'];
    }

    public function disableOTLP(string $organizationId): array
    {
        $query = 'mutation($organizationId: UUID!) { disableOTLP(organizationId: $organizationId) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['organizationId' => $organizationId])['disableOTLP'];
    }
}
