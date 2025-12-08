<?php

namespace App\Dto\Output;

readonly class EquipmentOutputDto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $capacity,
        public string $createdAt,
        public array $roomIds = [],
    ) {}
}
