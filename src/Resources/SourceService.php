<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class SourceService extends BaseService
{
    private const FRAGMENT = 'id name slug providerType verifyConfig status rateLimitRps spikeProtection maxIngestRps brokerConfig responseConfig { statusCode body contentType } dedupConfig { strategy fields window } createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($status: SourceStatus, $providerType: String, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            sources(status: $status, providerType: $providerType, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        $data = $this->transport->execute($query, $this->buildVariables($options, ['status', 'providerType', 'search', 'limit', 'offset', 'after', 'first']));
        return $data['sources'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { source(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['source'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateSourceInput!) { createSource(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createSource'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateSourceInput!) { updateSource(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateSource'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteSource(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteSource'];
    }

    public function rotateSecret(string $id): array
    {
        $query = 'mutation($id: UUID!) { rotateSourceSecret(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['rotateSourceSecret'];
    }

    public function clearSecondarySecret(string $id): array
    {
        $query = 'mutation($id: UUID!) { clearSourceSecondarySecret(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['clearSourceSecondarySecret'];
    }
}
