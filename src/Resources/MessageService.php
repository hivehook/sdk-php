<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class MessageService extends BaseService
{
    private const FRAGMENT = 'id applicationId eventType payload idempotencyKey status createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($applicationId: UUID, $eventType: String, $status: MessageStatus, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            messages(applicationId: $applicationId, eventType: $eventType, status: $status, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['applicationId', 'eventType', 'status', 'search', 'limit', 'offset', 'after', 'first']))['messages'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { message(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['message'];
    }

    public function send(array $input): array
    {
        $query = 'mutation($input: SendMessageInput!) { sendMessage(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['sendMessage'];
    }

    public function broadcast(array $input): array
    {
        $query = 'mutation($input: BroadcastMessageInput!) { broadcastMessage(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['broadcastMessage'];
    }

    public function sendDynamic(array $input): array
    {
        $query = 'mutation($input: SendDynamicMessageInput!) { sendDynamicMessage(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['sendDynamicMessage'];
    }
}
