<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CaptureInputDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'La valeur est obligatoire')]
        #[Assert\Type('float', message: 'La valeur doit être un nombre décimal')]
        public float $value,

        #[Assert\Type('integer', message: 'L\'ID de la room doit être un entier')]
        public ?int $roomId = null,

        #[Assert\NotBlank(message: 'Le type de capture est obligatoire')]
        #[Assert\Type('integer', message: 'L\'ID du type de capture doit être un entier')]
        public int $captureTypeId,

        #[Assert\Type('integer', message: 'L\'ID du client account doit être un entier')]
        public ?int $clientAccountId = null,
    ) {}
}