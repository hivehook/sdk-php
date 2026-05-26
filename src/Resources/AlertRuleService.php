<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class AlertRuleService extends BaseService
{
    private const FRAGMENT = 'id name conditionType threshold webhookUrl channel emailConfig { to subjectTemplate } slackConfig { webhookUrl channel } cooldown enabled createdAt';

    public function list(array $options = []): array
    {
        $query = 'query($enabled: Boolean, $search: String, $limit: Int, $offset: Int, $after: String, $first: Int) {
            alertRules(enabled: $enabled, search: $search, limit: $limit, offset: $offset, after: $after, first: $first) {
                nodes { ' . self::FRAGMENT . ' }
                pageInfo { total limit offset endCursor hasNextPage }
            }
        }';
        return $this->transport->execute($query, $this->buildVariables($options, ['enabled', 'search', 'limit', 'offset', 'after', 'first']))['alertRules'];
    }

    public function get(string $id): ?array
    {
        $query = 'query($id: UUID!) { alertRule(id: $id) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id])['alertRule'];
    }

    public function create(array $input): array
    {
        $query = 'mutation($input: CreateAlertRuleInput!) { createAlertRule(input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['input' => $input])['createAlertRule'];
    }

    public function update(string $id, array $input): array
    {
        $query = 'mutation($id: UUID!, $input: UpdateAlertRuleInput!) { updateAlertRule(id: $id, input: $input) { ' . self::FRAGMENT . ' } }';
        return $this->transport->execute($query, ['id' => $id, 'input' => $input])['updateAlertRule'];
    }

    public function delete(string $id): bool
    {
        $query = 'mutation($id: UUID!) { deleteAlertRule(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['deleteAlertRule'];
    }

    public function test(string $id): bool
    {
        $query = 'mutation($id: UUID!) { testAlertRule(id: $id) }';
        return $this->transport->execute($query, ['id' => $id])['testAlertRule'];
    }
}
