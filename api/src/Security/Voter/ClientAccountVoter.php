<?php

namespace App\Security\Voter;

use App\Entity\ClientAccount;
use App\Entity\User;

class ClientAccountVoter extends AbstractResourceVoter
{
    protected function getResourceClass(): string
    {
        return ClientAccount::class;
    }

    protected function canView(mixed $subject, User $user): bool
    {
        // Un utilisateur peut voir son propre compte client
        return $subject->getUsers()->contains($user);
    }

    protected function canEdit(mixed $subject, User $user): bool
    {
        // Un utilisateur peut modifier son propre compte client
        return $subject->getUsers()->contains($user);
    }

    protected function canDelete(mixed $subject, User $user): bool
    {
        // Seul un admin peut supprimer un compte client
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    protected function canCreate(mixed $subject, User $user): bool
    {
        // Les utilisateurs peuvent créer un compte client s'ils n'en ont pas déjà un
        return $user->getClientAccount() === null;
    }
}