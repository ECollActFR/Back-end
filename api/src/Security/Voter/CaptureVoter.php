<?php

namespace App\Security\Voter;

use App\Entity\Capture;
use App\Entity\User;

class CaptureVoter extends AbstractResourceVoter
{
    protected function getResourceClass(): string
    {
        return Capture::class;
    }

    protected function canView(mixed $subject, User $user): bool
    {
        $room = $subject->getRoom();
        return $room && $room->getBuilding()->getOwner() === $user;
    }

    protected function canEdit(mixed $subject, User $user): bool
    {
        $room = $subject->getRoom();
        return $room && $room->getBuilding()->getOwner() === $user;
    }

    protected function canDelete(mixed $subject, User $user): bool
    {
        $room = $subject->getRoom();
        return $room && $room->getBuilding()->getOwner() === $user;
    }

    protected function canCreate(mixed $subject, User $user): bool
    {
        // Les users peuvent créer des captures dans leurs rooms
        return true;
    }
}