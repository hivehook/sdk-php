<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class StreamConsumerService extends BaseService
{
    private const FRAGMENT = 'id streamId name cursorSequence createdAt updatedAt';

    public function list(string $streamId, array $options = []): array
    {
        $query = 'query($streamId: UUID!, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            streamConsumers(streamId: $streamId, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        $vars = array_merge(['streamId' => $streamId], $this->buildVariables($options, ['search', 'limit', 'offset', 'after', 'first']));
        return $this->transport->execute($query, $vars)['streamConsumers'];
    }

    public function iterate(array $options = []): \Generator
    {
        $streamId = $options['streamId'] ?? '';
        unset($options['streamId']);
        $offset = $options['offset'] ?? 0;
        while (true) {
            $options['offset'] = $offset;
            $conn = $this->list($streamId, $options);
            $nodes = $conn['nodes'] ?? [];
            foreach ($nodes as $node) {
                yield $node;
            }
            $pageInfo = $conn['pageInfo'] ?? [];
            if (empty($pageInfo['hasNextPage']) || count($nodes) === 0) {
                break;
            }
            $offset += count($nodes);
        }
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { streamConsumer(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['streamConsumer'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateStreamConsumerInput!) { createStreamConsumer(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createStreamConsumer'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteStreamConsumer(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteStreamConsumer'];
    }

    public function advanceCursor(string $id, int $sequence): array
    {
        $query = 'mutation($id: UUID!, $sequence: Int!) { advanceConsumerCursor(id: $id, sequence: $sequence) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'sequence' => $sequence])['advanceConsumerCursor'];
    }
}
