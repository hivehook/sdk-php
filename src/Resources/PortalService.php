<?php

declare(strict_types=1);

namespace Hivehook\Resources;

class PortalService extends BaseService
{
    public function generateToken(string $applicationId): array
    {
        $query = 'mutation($applicationId: UUID!) { generatePortalToken(applicationId: $applicationId) { token expiresAt } }';
        return $this->transport->execute($query, ['applicationId' => $applicationId])['generatePortalToken'];
    }
}
