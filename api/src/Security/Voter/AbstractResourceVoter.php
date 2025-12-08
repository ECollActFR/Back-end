<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;

abstract class AbstractResourceVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const CREATE = 'create';

    abstract protected function getResourceClass(): string;

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::CREATE])) {
            return false;
        }

        $resourceClass = $this->getResourceClass();
        return $subject instanceof $resourceClass || $subject === $resourceClass;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Admin bypass
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return match($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::CREATE => $this->canCreate($subject, $user),
            default => false,
        };
    }

    abstract protected function canView(mixed $subject, User $user): bool;
    abstract protected function canEdit(mixed $subject, User $user): bool;
    abstract protected function canDelete(mixed $subject, User $user): bool;
    abstract protected function canCreate(mixed $subject, User $user): bool;
}
