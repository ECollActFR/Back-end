<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CaptureRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CaptureRepository::class)]
#[ApiResource(
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    normalizationContext: ['groups' => ['capture:read']],
    denormalizationContext: ['groups' => ['capture:write']],
    operations: [
        new Get(normalizationContext: ['groups' => ['capture:read', 'capture:room']]),
        new GetCollection(normalizationContext: ['groups' => ['capture:read', 'capture:room']]),
        new Post(normalizationContext: ['groups' => ['capture:write']])
    ]
)]
class Capture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['capture:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Groups(['capture:read', 'capture:write'])]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    #[Groups(['capture:read', 'capture:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'captures')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['capture:room', 'capture:write'])]
    private ?Room $room = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['capture:read', 'capture:write'])]
    private ?CaptureType $type = null;

    #[ORM\Column]
    #[Groups(['capture:read'])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    #[Groups(['capture:read', 'capture:room', 'capture:write'])]
    private ?\DateTime $dateCaptured = null;

    public function __construct()
    {
        $this->createdAt = Carbon::now();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getType(): ?CaptureType
    {
        return $this->type;
    }

    public function setType(?CaptureType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateCaptured(): ?\DateTime
    {
        return $this->dateCaptured;
    }

    public function setDateCaptured(\DateTime $dateCaptured): static
    {
        $this->dateCaptured = $dateCaptured;

        return $this;
    }
}
