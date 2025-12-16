<?php

namespace App\Dto\Output;

readonly class DeviceTaskOutputDto
{
    public function __construct(
        public ?int $id = null,
        public ?string $taskName = null,
        public ?string $taskType = null,
        public ?array $parameters = null,
        public ?bool $enabled = null,
        public ?int $executionInterval = null,
        public ?int $priority = null,
        public ?int $acquisitionSystemId = null,
        public ?int $clientAccountId = null,
    ) {}
}