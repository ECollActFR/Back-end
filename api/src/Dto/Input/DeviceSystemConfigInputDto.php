<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class DeviceSystemConfigInputDto
{
    public function __construct(
        #[Assert\Type('bool', message: 'Le mode debug doit être un booléen')]
        public ?bool $debugMode = null,

        #[Assert\Type('integer', message: 'La taille du buffer doit être un entier')]
        #[Assert\PositiveOrZero(message: 'La taille du buffer doit être positive ou zéro')]
        public ?int $bufferSize = null,

        #[Assert\Type('bool', message: 'Le deep sleep doit être un booléen')]
        public ?bool $deepSleepEnabled = null,

        #[Assert\Type('integer', message: 'L\'intervalle de deep sleep doit être un entier')]
        #[Assert\Positive(message: 'L\'intervalle de deep sleep doit être positif')]
        public ?int $deepSleepInterval = null,

        #[Assert\Type('bool', message: 'Le serveur web doit être un booléen')]
        public ?bool $webServerEnabled = null,

        #[Assert\Type('integer', message: 'Le port du serveur web doit être un entier')]
        #[Assert\Range(min: 1, max: 65535, minMessage: 'Le port doit être au moins {{ min }}', maxMessage: 'Le port ne peut pas dépasser {{ max }}')]
        public ?int $webServerPort = null,

        #[Assert\NotBlank(message: 'Le système d\'acquisition est obligatoire')]
        #[Assert\Type('integer', message: 'L\'ID du système d\'acquisition doit être un entier')]
        public int $acquisitionSystemId,

        #[Assert\Type('integer', message: 'L\'ID du client account doit être un entier')]
        public ?int $clientAccountId = null,
    ) {}
}