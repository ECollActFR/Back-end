<?php

namespace App\Dto\Output;

use Symfony\Component\Serializer\Annotation\Groups;

class RoomWithLastCapturesDto
{
    #[Groups(['room:last_captures'])]
    public int $id;

    #[Groups(['room:last_captures'])]
    public string $name;

    #[Groups(['room:last_captures'])]
    public ?string $description = null;

    #[Groups(['room:last_captures'])]
    public \DateTime $createdAt;

    /**
     * @var array<array{type: array{id: int, name: string, description: string}, capture: array{id: int, value: string, description: string, createdAt: string, dateCaptured: string}}>
     */
    #[Groups(['room:last_captures'])]
    public array $lastCapturesByType = [];
}