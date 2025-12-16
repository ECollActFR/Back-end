<?php

namespace App\Dto\Output;

readonly class BuildingOutputDto
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?int $ownerId = null,
        public ?int $clientAccountId = null,
    ) {}
}