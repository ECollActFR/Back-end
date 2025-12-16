<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class EquipmentInputDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le nom est obligatoire')]
        #[Assert\Length(max: 40, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
        public string $name,

        #[Assert\Type('integer', message: 'La capacité doit être un nombre entier')]
        #[Assert\PositiveOrZero(message: 'La capacité doit être positive ou zéro')]
        public ?int $capacity = null,

        #[Assert\Type('array', message: 'Les IDs des rooms doivent être un tableau')]
        public array $roomIds = [],

        #[Assert\Type('integer', message: 'L\'ID du client account doit être un entier')]
        public ?int $clientAccountId = null,
    ) {}
}