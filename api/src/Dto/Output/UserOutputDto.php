<?php

namespace App\Dto\Output;

readonly class UserOutputDto
{
    public function __construct(
        public int $id,
        public string $email,
        public array $roles,
        public string $firstname,
        public string $lastname,
        public ?string $phone,
        public ?string $profilePicture,
        public bool $isActive,
        public bool $emailVerified,
        public string $createdAt,
        public ?string $lastLogin,
        public array $buildingIds = [],
        public ?array $clientAccount = null,
    ) {}
}
