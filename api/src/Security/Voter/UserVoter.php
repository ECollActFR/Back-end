<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Service\ClientAccountAccessService;

class UserVoter extends AbstractResourceVoter
{
    public function __construct(
        private ClientAccountAccessService $clientAccountAccessService
    ) {}

    protected function getResourceClass(): string
    {
        return User::class;
    }

    protected function canView(mixed $subject, User $user): bool
    {
        // Utilisateur peut voir son propre profil ou celui d'un utilisateur du même compte client
        return $subject->getId() === $user->getId() || 
               $this->clientAccountAccessService->isSameClientAccount($subject, $user);
    }

    protected function canEdit(mixed $subject, User $user): bool
    {
        // Utilisateur peut modifier son propre profil ou celui d'un utilisateur du même compte client
        return $subject->getId() === $user->getId() || 
               $this->clientAccountAccessService->isSameClientAccount($subject, $user);
    }

    protected function canDelete(mixed $subject, User $user): bool
    {
        // Utilisateur peut supprimer son propre compte ou celui d'un utilisateur du même compte client
        return $subject->getId() === $user->getId() || 
               $this->clientAccountAccessService->isSameClientAccount($subject, $user);
    }

    protected function canCreate(mixed $subject, User $user): bool
    {
        // Tout utilisateur authentifié peut créer (inscription)
        return true;
    }
}
