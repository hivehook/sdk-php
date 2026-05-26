<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class ApplicationService extends BaseService
{
    private const FRAGMENT = 'id name uid createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            applications(search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['search', 'limit', 'offset', 'after', 'first']))['applications'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { application(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['application'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateApplicationInput!) { createApplication(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createApplication'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateApplicationInput!) { updateApplication(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateApplication'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteApplication(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteApplication'];
    }
}
