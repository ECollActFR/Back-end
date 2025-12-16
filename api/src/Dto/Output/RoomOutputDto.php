<?php

namespace App\Dto\Output;

readonly class RoomOutputDto
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?int $buildingId = null,
        public array $captureTypeIds = [],
        public array $equipmentIds = [],
        public ?\DateTime $createdAt = null,
    ) {}
}