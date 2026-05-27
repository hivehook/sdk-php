<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class StreamSinkService extends BaseService
{
    private const FRAGMENT = 'id streamId name sinkType config batchSize flushInterval cursorSequence status lastFlushedAt createdAt';

    public function list(string $streamId, array $options = []): array
    {
        $query = 'query($streamId: UUID!, $status: SinkStatus, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            streamSinks(streamId: $streamId, status: $status, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        $vars = array_merge(['streamId' => $streamId], $this->buildVariables($options, ['status', 'search', 'limit', 'offset', 'after', 'first']));
        return $this->transport->execute($query, $vars)['streamSinks'];
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
        $query = 'query($id: UUID!) { streamSink(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['streamSink'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateStreamSinkInput!) { createStreamSink(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createStreamSink'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateStreamSinkInput!) { updateStreamSink(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateStreamSink'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteStreamSink(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteStreamSink'];
    }
}
