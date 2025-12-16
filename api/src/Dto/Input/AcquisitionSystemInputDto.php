<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class AcquisitionSystemInputDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le nom est obligatoire')]
        #[Assert\Length(max: 30, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
        public string $name,

        #[Assert\Type('integer', message: 'L\'ID de la room doit être un entier')]
        public ?int $roomId = null,

        #[Assert\Type('integer', message: 'L\'ID du client account doit être un entier')]
        public ?int $clientAccountId = null,
    ) {}
}