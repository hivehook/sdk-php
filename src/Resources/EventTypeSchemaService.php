<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class EventTypeSchemaService extends BaseService
{
    private const FRAGMENT = 'id eventType description schema example createdAt updatedAt';

    public function list(array $options = []): array
    {
        $query = 'query($search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            eventTypeSchemas(search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['search', 'limit', 'offset', 'after', 'first']))['eventTypeSchemas'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { eventTypeSchema(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['eventTypeSchema'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateEventTypeSchemaInput!) { createEventTypeSchema(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createEventTypeSchema'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateEventTypeSchemaInput!) { updateEventTypeSchema(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateEventTypeSchema'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteEventTypeSchema(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteEventTypeSchema'];
    }
}
