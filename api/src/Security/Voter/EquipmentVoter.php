<?php

namespace App\Security\Voter;

use App\Entity\Equipment;
use App\Entity\User;

class EquipmentVoter extends AbstractResourceVoter
{
    protected function getResourceClass(): string
    {
        return Equipment::class;
    }

    protected function canView(mixed $subject, User $user): bool
    {
        // Autoriser si l'équipement est dans au moins une salle de l'utilisateur
        foreach ($subject->getRooms() as $room) {
            if ($room->getBuilding()->getOwner() === $user) {
                return true;
            }
        }
        return false;
    }

    protected function canEdit(mixed $subject, User $user): bool
    {
        // Seuls les admins peuvent modifier les équipements partagés
        return false;
    }

    protected function canDelete(mixed $subject, User $user): bool
    {
        // Seuls les admins peuvent supprimer les équipements partagés
        return false;
    }

    protected function canCreate(mixed $subject, User $user): bool
    {
        // Seuls les admins peuvent créer des équipements partagés
        return false;
    }
}
