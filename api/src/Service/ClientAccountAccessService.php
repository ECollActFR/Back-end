<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Building;
use App\Entity\Room;
use App\Entity\ClientAccount;
use App\Entity\AcquisitionSystem;
use App\Entity\Capture;
use App\Entity\Equipment;

class ClientAccountAccessService
{
    /**
     * Vérifie si deux utilisateurs appartiennent au même compte client
     */
    public function isSameClientAccount(User $user1, User $user2): bool
    {
        if (!$user1->getClientAccount() || !$user2->getClientAccount()) {
            return false;
        }

        return $user1->getClientAccount()->getId() === $user2->getClientAccount()->getId();
    }

    /**
     * Vérifie si un utilisateur peut accéder à une ressource en fonction du compte client
     */
    public function canAccessResource(mixed $resource, User $user): bool
    {
        // Les admins ont accès à tout
        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }

        // Si l'utilisateur n'a pas de compte client, pas d'accès
        if (!$user->getClientAccount()) {
            return false;
        }

        $userClientAccountId = $user->getClientAccount()->getId();

        // Gérer les différents types de ressources
        if ($resource instanceof User) {
            return $this->canAccessUser($resource, $userClientAccountId);
        }

        if ($resource instanceof Building) {
            return $this->canAccessBuilding($resource, $userClientAccountId);
        }

        if ($resource instanceof Room) {
            return $this->canAccessRoom($resource, $userClientAccountId);
        }

        if ($resource instanceof ClientAccount) {
            return $resource->getId() === $userClientAccountId;
        }

        if ($resource instanceof AcquisitionSystem) {
            return $this->canAccessAcquisitionSystem($resource, $userClientAccountId);
        }

        if ($resource instanceof Capture) {
            return $this->canAccessCapture($resource, $userClientAccountId);
        }

        if ($resource instanceof Equipment) {
            return $this->canAccessEquipment($resource, $userClientAccountId);
        }

        return false;
    }

    /**
     * Vérifie si un utilisateur peut accéder à un autre utilisateur
     */
    private function canAccessUser(User $targetUser, int $userClientAccountId): bool
    {
        $targetClientAccount = $targetUser->getClientAccount();
        return $targetClientAccount && $targetClientAccount->getId() === $userClientAccountId;
    }

    /**
     * Vérifie si un utilisateur peut accéder à un building
     */
    private function canAccessBuilding(Building $building, int $userClientAccountId): bool
    {
        $owner = $building->getOwner();
        if (!$owner) {
            return false;
        }

        $ownerClientAccount = $owner->getClientAccount();
        return $ownerClientAccount && $ownerClientAccount->getId() === $userClientAccountId;
    }

    /**
     * Vérifie si un utilisateur peut accéder à une room
     */
    private function canAccessRoom(Room $room, int $userClientAccountId): bool
    {
        $building = $room->getBuilding();
        if (!$building) {
            return false;
        }

        return $this->canAccessBuilding($building, $userClientAccountId);
    }

    /**
     * Vérifie si un utilisateur peut accéder à un système d'acquisition
     */
    private function canAccessAcquisitionSystem(AcquisitionSystem $acquisitionSystem, int $userClientAccountId): bool
    {
        $room = $acquisitionSystem->getRoom();
        if (!$room) {
            return false;
        }

        return $this->canAccessRoom($room, $userClientAccountId);
    }

    /**
     * Vérifie si un utilisateur peut accéder à une capture
     */
    private function canAccessCapture(Capture $capture, int $userClientAccountId): bool
    {
        $room = $capture->getRoom();
        if (!$room) {
            return false;
        }

        return $this->canAccessRoom($room, $userClientAccountId);
    }

    /**
     * Vérifie si un utilisateur peut accéder à un équipement
     */
    private function canAccessEquipment(Equipment $equipment, int $userClientAccountId): bool
    {
        // Vérifier si l'équipement est dans au moins une salle accessible
        foreach ($equipment->getRooms() as $room) {
            if ($this->canAccessRoom($room, $userClientAccountId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Récupère l'ID du compte client d'un utilisateur
     */
    public function getClientAccountId(User $user): ?int
    {
        $clientAccount = $user->getClientAccount();
        return $clientAccount ? $clientAccount->getId() : null;
    }
}