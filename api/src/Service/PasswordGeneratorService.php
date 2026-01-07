<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PasswordGeneratorService
{
    public function generateSecurePassword(int $length = 12): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        $password = '';
        $maxIndex = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $maxIndex)];
        }

        return $password;
    }

    public function generateAndHashPassword(UserPasswordHasherInterface $passwordHasher, User $user): array
    {
        $plainPassword = $this->generateSecurePassword();
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

        return [
            'plain' => $plainPassword,
            'hashed' => $hashedPassword
        ];
    }
}