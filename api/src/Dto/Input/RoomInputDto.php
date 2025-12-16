<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RoomInputDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le nom est obligatoire')]
        #[Assert\Length(max: 15, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
        public string $name,

        #[Assert\Length(max: 255, maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères')]
        public ?string $description = null,

        #[Assert\NotBlank(message: 'Le building est obligatoire')]
        #[Assert\Type('integer', message: 'L\'ID du building doit être un entier')]
        public int $buildingId,

        #[Assert\Type('array', message: 'Les IDs des types de capture doivent être un tableau')]
        public array $captureTypeIds = [],

        #[Assert\Type('array', message: 'Les IDs des équipements doivent être un tableau')]
        public array $equipmentIds = []
    ) {}
}