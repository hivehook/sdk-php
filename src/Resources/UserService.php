<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class UserService extends BaseService
{
    private const FRAGMENT = 'id organizationId email name role lastLoginAt createdAt updatedAt';

    public function list(array $options = []): array
    {
        $query = 'query($organizationId: UUID, $search: String, $limit: Int, $offset: Int) {
            users(organizationId: $organizationId, search: $search, limit: $limit, offset: $offset) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['organizationId', 'search', 'limit', 'offset']))['users'];
    }

    public function me(): ?array
    {
        $query = 'query { me { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query)['me'];
    }

    public function invite(string $organizationId, array $input): array
    {
        $query = 'mutation($organizationId: UUID!, $input: InviteUserInput!) { inviteUser(organizationId: $organizationId, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['organizationId' => $organizationId, 'input' => $input])['inviteUser'];
    }

    public function remove(string $id): bool
    {
        $query = 'mutation($id: UUID!) { removeUser(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['removeUser'];
    }

    public function updateRole(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateUserRoleInput!) { updateUserRole(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateUserRole'];
    }
}
