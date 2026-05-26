<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class StreamService extends BaseService
{
    private const FRAGMENT = 'id applicationId name status retentionDays createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($applicationId: UUID, $status: StreamStatus, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            streams(applicationId: $applicationId, status: $status, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['applicationId', 'status', 'search', 'limit', 'offset', 'after', 'first']))['streams'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { stream(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['stream'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateStreamInput!) { createStream(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createStream'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateStreamInput!) { updateStream(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateStream'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteStream(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteStream'];
    }
}
