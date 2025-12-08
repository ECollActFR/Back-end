<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SuperAdminVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        // This voter supports all attributes
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        // If the user is not authenticated, abstain
        if (!$user) {
            return false;
        }

        // Check if user has ROLE_SUPER_ADMIN
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }

        // Otherwise, abstain to let other voters decide
        return false;
    }
}