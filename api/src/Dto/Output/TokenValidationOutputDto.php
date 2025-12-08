<?php

namespace App\Dto\Output;

use Symfony\Component\Serializer\Annotation\Groups;

readonly class TokenValidationOutputDto
{
    public function __construct(
        #[Groups(['token:read'])]
        public bool $success,

        #[Groups(['token:read'])]
        public ?string $message = null,
    ) {}
}
