<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class TransformationService extends BaseService
{
    private const FRAGMENT = 'id name description code enabled failOpen timeoutMs createdAt updatedAt';

    public function list(array $options = []): array
    {
        $query = 'query($enabled: Boolean, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            transformations(enabled: $enabled, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['enabled', 'search', 'limit', 'offset', 'after', 'first']))['transformations'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { transformation(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['transformation'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateTransformationInput!) { createTransformation(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createTransformation'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateTransformationInput!) { updateTransformation(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateTransformation'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteTransformation(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteTransformation'];
    }

    public function test(array $input): array
    {
        $query = 'mutation($input: TestTransformationInput!) { testTransformation(input: $input) { success output error durationMs } }';
        return $this->transport->execute($query, ['input' => $input])['testTransformation'];
    }
}
