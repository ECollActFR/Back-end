<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class DeviceNetworkConfigInputDto
{
    public function __construct(
        #[Assert\Length(max: 32, maxMessage: 'Le SSID ne peut pas dépasser {{ limit }} caractères')]
        public ?string $wifiSSID = null,

        #[Assert\Length(max: 64, maxMessage: 'Le mot de passe WiFi ne peut pas dépasser {{ limit }} caractères')]
        public ?string $wifiPassword = null,

        #[Assert\Length(max: 64, maxMessage: 'Le serveur NTP ne peut pas dépasser {{ limit }} caractères')]
        public ?string $ntpServer = null,

        #[Assert\Length(max: 32, maxMessage: 'Le fuseau horaire ne peut pas dépasser {{ limit }} caractères')]
        public ?string $timezone = null,

        #[Assert\NotBlank(message: 'Le système d\'acquisition est obligatoire')]
        #[Assert\Type('integer', message: 'L\'ID du système d\'acquisition doit être un entier')]
        public int $acquisitionSystemId,

        #[Assert\Type('integer', message: 'L\'ID du client account doit être un entier')]
        public ?int $clientAccountId = null,
    ) {}
}