<?php

namespace App\Security\Voter;

use App\Entity\Room;
use App\Entity\User;
use App\Service\ClientAccountAccessService;

class RoomVoter extends AbstractResourceVoter
{
    public function __construct(
        private ClientAccountAccessService $clientAccountAccessService
    ) {}

    protected function getResourceClass(): string
    {
        return Room::class;
    }

    protected function canView(mixed $subject, User $user): bool
    {
        return $this->clientAccountAccessService->canAccessResource($subject, $user);
    }

    protected function canEdit(mixed $subject, User $user): bool
    {
        return $this->clientAccountAccessService->canAccessResource($subject, $user);
    }

    protected function canDelete(mixed $subject, User $user): bool
    {
        return $this->clientAccountAccessService->canAccessResource($subject, $user);
    }

    protected function canCreate(mixed $subject, User $user): bool
    {
        // Les users peuvent créer des rooms dans leurs buildings
        return true;
    }
}