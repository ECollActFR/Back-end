<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class DeviceSensorInputDto
{
    public function __construct(
        #[Assert\Type('integer', message: 'Le pin doit être un entier')]
        #[Assert\Range(min: 0, max: 39, minMessage: 'Le pin doit être au moins {{ min }}', maxMessage: 'Le pin ne peut pas dépasser {{ max }}')]
        public ?int $pin = null,

        #[Assert\Type('integer', message: 'L\'intervalle de lecture doit être un entier')]
        #[Assert\Positive(message: 'L\'intervalle de lecture doit être positif')]
        public ?int $readInterval = null,

        #[Assert\Type('bool', message: 'L\'activation doit être un booléen')]
        public ?bool $enabled = null,

        #[Assert\NotBlank(message: 'Le système d\'acquisition est obligatoire')]
        #[Assert\Type('integer', message: 'L\'ID du système d\'acquisition doit être un entier')]
        public int $acquisitionSystemId,

        #[Assert\NotBlank(message: 'Le type de capture est obligatoire')]
        #[Assert\Type('integer', message: 'L\'ID du type de capture doit être un entier')]
        public int $captureTypeId,

        #[Assert\Type('integer', message: 'L\'ID du client account doit être un entier')]
        public ?int $clientAccountId = null,
    ) {}
}