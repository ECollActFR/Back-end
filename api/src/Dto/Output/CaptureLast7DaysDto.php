<?php

namespace App\Dto\Output;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
class CaptureLast7DaysDto
{
    #[Groups(['capture:last7days'])]
    public int $roomId;

    #[Groups(['capture:last7days'])]
    public string $roomName;

    #[Groups(['capture:last7days'])]
    public ?string $roomDescription = null;

    #[Groups(['capture:last7days'])]
    public array $dataByType = [];

    #[Groups(['capture:last7days'])]
    public \DateTime $startDate;

    #[Groups(['capture:last7days'])]
    public \DateTime $endDate;

    #[Groups(['capture:last7days'])]
    public int $totalCount;

    #[Groups(['capture:last7days'])]
    public int $typeCount;
}