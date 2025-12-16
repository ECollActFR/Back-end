<?php

namespace App\Dto\Output;

readonly class DeviceNetworkConfigOutputDto
{
    public function __construct(
        public ?int $id = null,
        public ?string $wifiSSID = null,
        public ?string $wifiPassword = null,
        public ?string $ntpServer = null,
        public ?string $timezone = null,
        public ?int $acquisitionSystemId = null,
        public ?int $clientAccountId = null,
    ) {}
}