<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class MetaEventConfigService extends BaseService
{
    private const FRAGMENT = 'id name url signingSecret eventTypes enabled createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            metaEventConfigs(search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['search', 'limit', 'offset', 'after', 'first']))['metaEventConfigs'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { metaEventConfig(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['metaEventConfig'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateMetaEventConfigInput!) { createMetaEventConfig(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createMetaEventConfig'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateMetaEventConfigInput!) { updateMetaEventConfig(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateMetaEventConfig'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteMetaEventConfig(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteMetaEventConfig'];
    }
}
