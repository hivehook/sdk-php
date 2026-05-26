<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class AuditLogService extends BaseService
{
    private const FRAGMENT = 'id actorType actorId actorName action resourceType resourceId orgId ipAddress userAgent details createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($actorType: String, $resourceType: String, $resourceId: UUID, $action: String, $since: Time, $until: Time, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            auditLogs(actorType: $actorType, resourceType: $resourceType, resourceId: $resourceId, action: $action, since: $since, until: $until, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['actorType', 'resourceType', 'resourceId', 'action', 'since', 'until', 'search', 'limit', 'offset', 'after', 'first']))['auditLogs'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { auditLog(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['auditLog'];
    }
}
