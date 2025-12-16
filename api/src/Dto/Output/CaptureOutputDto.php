<?php

namespace App\Dto\Output;

readonly class CaptureOutputDto
{
    public function __construct(
        public ?int $id = null,
        public ?float $value = null,
        public ?int $roomId = null,
        public ?int $captureTypeId = null,
        public ?\DateTime $createdAt = null,
        public ?int $clientAccountId = null,
    ) {}
}