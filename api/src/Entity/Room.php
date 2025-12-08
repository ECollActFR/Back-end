<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\RoomRepository;
use App\State\Provider\RoomProvider;
use App\State\Provider\RoomWithLastCapturesProvider;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ApiResource(
    provider: RoomProvider::class,
    normalizationContext: ['groups' => ['room:read', 'capture:room']],
    denormalizationContext: ['groups' => ['room:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Get(security: "is_granted('view', object)"),
        new Get(
            uriTemplate: '/rooms/{id}/lastCaptures',
            provider: RoomWithLastCapturesProvider::class,
            normalizationContext: ['groups' => ['room:last_captures']],
            security: "is_granted('view', object)"
        ),
        new Post(security: "is_granted('create', object)"),
        new Put(security: "is_granted('edit', object)"),
        new Patch(security: "is_granted('edit', object)"),
        new Delete(security: "is_granted('delete', object)")
    ]
)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['capture:room', 'room:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    #[Groups(['capture:room', 'room:read', 'room:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['capture:room', 'room:read', 'room:write'])]
    private ?string $description = null;

    /**
     * @var Collection<int, Capture>
     */
    #[ORM\OneToMany(targetEntity: Capture::class, mappedBy: 'room', cascade: ['remove'])]
    private Collection $captures;

    /**
     * @var Collection<int, CaptureType>
     */
    #[ORM\ManyToMany(targetEntity: CaptureType::class)]
    #[ORM\JoinTable(name: 'room_capture_type')]
    #[Groups(['capture:room', 'room:read'])]
    private Collection $captureTypes;

    #[ORM\Column]
    #[Groups(['capture:room', 'room:read'])]
    private ?\DateTime $createdAt = null;

    /**
     * @var Collection<int, AcquisitionSystem>
     */
    #[ORM\OneToMany(targetEntity: AcquisitionSystem::class, mappedBy: 'room', cascade: ['persist', 'remove'])]
    #[Groups(['capture:room', 'room:read'])]
    private Collection $acquisitionSystems;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'rooms')]
    #[Groups(['capture:room', 'room:read'])]
    private Collection $equipment;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['room:read', 'room:write'])]
    private ?Building $building = null;

    public function __construct()
    {
        $this->captures = new ArrayCollection();
        $this->captureTypes = new ArrayCollection();
        $this->createdAt = Carbon::now();
        $this->acquisitionSystems = new ArrayCollection();
        $this->equipment = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Capture>
     */
    public function getCaptures(): Collection
    {
        return $this->captures;
    }

    public function addCapture(Capture $capture): static
    {
        if (!$this->captures->contains($capture)) {
            $this->captures->add($capture);
            $capture->setRoom($this);
        }

        return $this;
    }

    public function removeCapture(Capture $capture): static
    {
        if ($this->captures->removeElement($capture)) {
            // set the owning side to null (unless already changed)
            if ($capture->getRoom() === $this) {
                $capture->setRoom(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, CaptureType>
     */
    public function getCaptureTypes(): Collection
    {
        return $this->captureTypes;
    }

    public function addCaptureType(CaptureType $captureType): static
    {
        if (!$this->captureTypes->contains($captureType)) {
            $this->captureTypes->add($captureType);
        }

        return $this;
    }

    public function removeCaptureType(CaptureType $captureType): static
    {
        $this->captureTypes->removeElement($captureType);

        return $this;
    }

    /**
     * @return Collection<int, AcquisitionSystem>
     */
    public function getAcquisitionSystems(): Collection
    {
        return $this->acquisitionSystems;
    }

    public function addAcquisitionSystem(AcquisitionSystem $acquisitionSystem): static
    {
        if (!$this->acquisitionSystems->contains($acquisitionSystem)) {
            $this->acquisitionSystems->add($acquisitionSystem);
            $acquisitionSystem->setRoom($this);
        }

        return $this;
    }

    public function removeAcquisitionSystem(AcquisitionSystem $acquisitionSystem): static
    {
        if ($this->acquisitionSystems->removeElement($acquisitionSystem)) {
            // set the owning side to null (unless already changed)
            if ($acquisitionSystem->getRoom() === $this) {
                $acquisitionSystem->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        $this->equipment->removeElement($equipment);

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }
}
