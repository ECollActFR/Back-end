<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AcquisitionSystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AcquisitionSystemRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['acquisitionSystem:read']],
    denormalizationContext: ['groups' => ['acquisitionSystem:write']]
)]
#[UniqueEntity(fields: ['macAddress'], message: 'Cette adresse MAC est déjà utilisée par un autre système d\'acquisition.')]
class AcquisitionSystem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['acquisitionSystem:read', 'capture:room'])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write', 'capture:room'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'acquisitionSystems')]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    private ?Room $room = null;

    #[ORM\Column]
    #[Groups(['acquisitionSystem:read'])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(length: 50)]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    #[Assert\NotBlank(message: 'Le type de device est obligatoire')]
    #[Assert\Length(max: 50)]
    private ?string $deviceType = null;

    #[ORM\Column(length: 20)]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    #[Assert\NotBlank(message: 'La version du firmware est obligatoire')]
    #[Assert\Length(max: 20)]
    private ?string $firmwareVersion = null;

    #[ORM\Column(length: 17, unique: true)]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    #[Assert\NotBlank(message: 'L\'adresse MAC est obligatoire')]
    #[Assert\Regex(
        pattern: '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
        message: 'L\'adresse MAC doit être au format AA:BB:CC:DD:EE:FF ou AA-BB-CC-DD-EE-FF'
    )]
    private ?string $macAddress = null;

    #[ORM\Column]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    private bool $isActive = true;

    #[ORM\Column(nullable: true)]
    #[Groups(['acquisitionSystem:read'])]
    private ?\DateTime $lastSeen = null;

    #[ORM\OneToOne(mappedBy: 'acquisitionSystem', cascade: ['persist', 'remove'])]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    private ?DeviceNetworkConfig $networkConfig = null;

    #[ORM\OneToMany(mappedBy: 'acquisitionSystem', targetEntity: DeviceSensor::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    private Collection $sensors;

    #[ORM\OneToMany(mappedBy: 'acquisitionSystem', targetEntity: DeviceTask::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    private Collection $tasks;

    #[ORM\OneToOne(mappedBy: 'acquisitionSystem', cascade: ['persist', 'remove'])]
    #[Groups(['acquisitionSystem:read', 'acquisitionSystem:write'])]
    private ?DeviceSystemConfig $systemConfig = null;

    public function __construct(){
        $this->createdAt = Carbon::now();
        $this->sensors = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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

    public function getDeviceType(): ?string
    {
        return $this->deviceType;
    }

    public function setDeviceType(string $deviceType): static
    {
        $this->deviceType = $deviceType;
        return $this;
    }

    public function getFirmwareVersion(): ?string
    {
        return $this->firmwareVersion;
    }

    public function setFirmwareVersion(string $firmwareVersion): static
    {
        $this->firmwareVersion = $firmwareVersion;
        return $this;
    }

    public function getMacAddress(): ?string
    {
        return $this->macAddress;
    }

    public function setMacAddress(string $macAddress): static
    {
        $this->macAddress = $macAddress;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getLastSeen(): ?\DateTime
    {
        return $this->lastSeen;
    }

    public function setLastSeen(?\DateTime $lastSeen): static
    {
        $this->lastSeen = $lastSeen;
        return $this;
    }

    public function getNetworkConfig(): ?DeviceNetworkConfig
    {
        return $this->networkConfig;
    }

    public function setNetworkConfig(?DeviceNetworkConfig $networkConfig): static
    {
        // Unset the owning side of the relation if necessary
        if ($networkConfig === null && $this->networkConfig !== null) {
            $this->networkConfig->setAcquisitionSystem(null);
        }

        // Set the owning side of the relation if necessary
        if ($networkConfig !== null && $networkConfig->getAcquisitionSystem() !== $this) {
            $networkConfig->setAcquisitionSystem($this);
        }

        $this->networkConfig = $networkConfig;
        return $this;
    }

    /**
     * @return Collection<int, DeviceSensor>
     */
    public function getSensors(): Collection
    {
        return $this->sensors;
    }

    public function addSensor(DeviceSensor $sensor): static
    {
        if (!$this->sensors->contains($sensor)) {
            $this->sensors->add($sensor);
            $sensor->setAcquisitionSystem($this);
        }

        return $this;
    }

    public function removeSensor(DeviceSensor $sensor): static
    {
        if ($this->sensors->removeElement($sensor)) {
            // Set the owning side to null (unless already changed)
            if ($sensor->getAcquisitionSystem() === $this) {
                $sensor->setAcquisitionSystem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DeviceTask>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(DeviceTask $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setAcquisitionSystem($this);
        }

        return $this;
    }

    public function removeTask(DeviceTask $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // Set the owning side to null (unless already changed)
            if ($task->getAcquisitionSystem() === $this) {
                $task->setAcquisitionSystem(null);
            }
        }

        return $this;
    }

    public function getSystemConfig(): ?DeviceSystemConfig
    {
        return $this->systemConfig;
    }

    public function setSystemConfig(?DeviceSystemConfig $systemConfig): static
    {
        // Unset the owning side of the relation if necessary
        if ($systemConfig === null && $this->systemConfig !== null) {
            $this->systemConfig->setAcquisitionSystem(null);
        }

        // Set the owning side of the relation if necessary
        if ($systemConfig !== null && $systemConfig->getAcquisitionSystem() !== $this) {
            $systemConfig->setAcquisitionSystem($this);
        }

        $this->systemConfig = $systemConfig;
        return $this;
    }
}
