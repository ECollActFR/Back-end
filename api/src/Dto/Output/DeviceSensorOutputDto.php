<?php

namespace App\Dto\Output;

readonly class DeviceSensorOutputDto
{
    public function __construct(
        public ?int $id = null,
        public ?int $pin = null,
        public ?int $readInterval = null,
        public ?bool $enabled = null,
        public ?int $acquisitionSystemId = null,
        public ?int $captureTypeId = null,
        public ?int $clientAccountId = null,
    ) {}
}