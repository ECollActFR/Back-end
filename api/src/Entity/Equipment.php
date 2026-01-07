<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\Input\EquipmentInputDto;
use App\Dto\Output\EquipmentOutputDto;
use App\Repository\EquipmentRepository;
use App\State\Processor\EquipmentProcessor;
use App\State\Provider\EquipmentProvider;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ApiResource(
    input: EquipmentInputDto::class,
    output: EquipmentOutputDto::class,
    processor: EquipmentProcessor::class,
    provider: EquipmentProvider::class,
    operations: [
        new Get(security: "is_granted('view', object)"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('create', object)"),
        new Patch(security: "is_granted('edit', object)"),
        new Delete(security: "is_granted('delete', object)"),
    ]
)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipment:read', 'capture:room'])]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Groups(['equipment:read', 'equipment:write', 'capture:room'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['equipment:read'])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['equipment:read', 'equipment:write', 'capture:room'])]
    private ?int $capacity = null;

    /**
     * @var Collection<int, Room>
     */
    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'equipment')]
    #[Groups(['equipment:read'])]
    private Collection $rooms;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->createdAt = Carbon::now();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->addEquipment($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            $room->removeEquipment($this);
        }

        return $this;
    }
}
