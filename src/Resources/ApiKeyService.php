<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class ApiKeyService extends BaseService
{
    private const FRAGMENT = 'id name keyPrefix scopes sourceIds createdAt expiresAt revokedAt lastUsedAt';

    public function list(array $options = []): array
    {
        $query = 'query($search: String, $limit: Int, $offset: Int) {
            apiKeys(search: $search, limit: $limit, offset: $offset) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['search', 'limit', 'offset']))['apiKeys'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { apiKey(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['apiKey'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateAPIKeyInput!) { createAPIKey(input: $input) { apiKey { ' . self::FRAGMENT . ' } rawKey } }';
        return $this->transport->execute($query, ['input' => $input])['createAPIKey'];
    }

    public function revoke(string $id): bool
    {
        $query = 'mutation($id: UUID!) { revokeAPIKey(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['revokeAPIKey'];
    }
}
