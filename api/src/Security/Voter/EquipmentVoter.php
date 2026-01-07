<?php

namespace App\Security\Voter;

use App\Entity\Equipment;
use App\Entity\User;
use App\Service\ClientAccountAccessService;

class EquipmentVoter extends AbstractResourceVoter
{
    public function __construct(
        private ClientAccountAccessService $clientAccountAccessService
    ) {}

    protected function getResourceClass(): string
    {
        return Equipment::class;
    }

    protected function canView(mixed $subject, User $user): bool
    {
        return $this->clientAccountAccessService->canAccessResource($subject, $user);
    }

    protected function canEdit(mixed $subject, User $user): bool
    {
        // Seuls les super admins peuvent modifier les équipements partagés
        return false;
    }

    protected function canDelete(mixed $subject, User $user): bool
    {
        // Seuls les super admins peuvent supprimer les équipements partagés
        return false;
    }

    protected function canCreate(mixed $subject, User $user): bool
    {
        // Seuls les super admins peuvent créer des équipements partagés
        return false;
    }
}
