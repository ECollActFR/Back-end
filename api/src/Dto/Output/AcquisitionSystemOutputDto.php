<?php

namespace App\Dto\Output;

readonly class AcquisitionSystemOutputDto
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?int $roomId = null,
        public ?int $clientAccountId = null,
    ) {}
}