<?php

namespace App\Dto\Output;

readonly class CaptureTypeOutputDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $createdAt,
    ) {}
}
