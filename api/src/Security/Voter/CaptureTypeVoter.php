<?php

namespace App\Security\Voter;

use App\Entity\CaptureType;
use App\Entity\User;

class CaptureTypeVoter extends AbstractResourceVoter
{
    protected function getResourceClass(): string
    {
        return CaptureType::class;
    }

    protected function canView(mixed $subject, User $user): bool
    {
        // Tous les utilisateurs authentifiés peuvent voir les CaptureType
        return true;
    }

    protected function canEdit(mixed $subject, User $user): bool
    {
        // Seuls les admins peuvent modifier (déjà géré par AbstractResourceVoter)
        return false;
    }

    protected function canDelete(mixed $subject, User $user): bool
    {
        // Seuls les admins peuvent supprimer (déjà géré par AbstractResourceVoter)
        return false;
    }

    protected function canCreate(mixed $subject, User $user): bool
    {
        // Seuls les admins peuvent créer (déjà géré par AbstractResourceVoter)
        return false;
    }
}
