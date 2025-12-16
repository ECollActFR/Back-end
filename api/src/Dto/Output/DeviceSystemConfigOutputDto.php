<?php

namespace App\Dto\Output;

readonly class DeviceSystemConfigOutputDto
{
    public function __construct(
        public ?int $id = null,
        public ?bool $debugMode = null,
        public ?int $bufferSize = null,
        public ?bool $deepSleepEnabled = null,
        public ?int $deepSleepInterval = null,
        public ?bool $webServerEnabled = null,
        public ?int $webServerPort = null,
        public ?int $acquisitionSystemId = null,
        public ?int $clientAccountId = null,
    ) {}
}