<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class BookmarkService extends BaseService
{
    private const FRAGMENT = 'id eventId name notes createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($eventId: UUID, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            bookmarks(eventId: $eventId, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['eventId', 'search', 'limit', 'offset', 'after', 'first']))['bookmarks'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateBookmarkInput!) { createBookmark(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createBookmark'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteBookmark(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteBookmark'];
    }
}
