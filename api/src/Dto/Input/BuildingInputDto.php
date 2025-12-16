<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class BuildingInputDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le nom est obligatoire')]
        #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
        public string $name,

        #[Assert\NotBlank(message: 'L\'propriétaire est obligatoire')]
        #[Assert\Type('integer', message: 'L\'ID de l\'propriétaire doit être un entier')]
        public int $ownerId,

        #[Assert\Type('integer', message: 'L\'ID du client account doit être un entier')]
        public ?int $clientAccountId = null,
    ) {}
}