<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DeviceTaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeviceTaskRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['deviceTask:read']],
    denormalizationContext: ['groups' => ['deviceTask:write']]
)]
class DeviceTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['deviceTask:read', 'acquisitionSystem:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['deviceTask:read', 'deviceTask:write'])]
    private ?AcquisitionSystem $acquisitionSystem = null;

    #[ORM\Column(length: 50)]
    #[Groups(['deviceTask:read', 'deviceTask:write', 'acquisitionSystem:read'])]
    #[Assert\NotBlank(message: 'Le nom de la tâche est obligatoire')]
    #[Assert\Length(max: 50)]
    #[Assert\Choice(
        choices: ['sensor_read', 'api_post', 'display', 'blink', 'custom'],
        message: 'Le nom de la tâche doit être l\'un des suivants : {{ choices }}'
    )]
    private ?string $taskName = null;

    #[ORM\Column]
    #[Groups(['deviceTask:read', 'deviceTask:write', 'acquisitionSystem:read'])]
    private bool $enabled = true;

    #[ORM\Column]
    #[Groups(['deviceTask:read', 'deviceTask:write', 'acquisitionSystem:read'])]
    #[Assert\Positive(message: 'L\'intervalle doit être positif')]
    #[Assert\Range(
        min: 100,
        max: 3600000,
        notInRangeMessage: 'L\'intervalle doit être entre {{ min }}ms et {{ max }}ms (1 heure)'
    )]
    private ?int $intervalMs = null;

    #[ORM\Column]
    #[Groups(['deviceTask:read', 'deviceTask:write', 'acquisitionSystem:read'])]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'La priorité doit être entre {{ min }} et {{ max }}'
    )]
    private ?int $priority = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['deviceTask:read', 'deviceTask:write', 'acquisitionSystem:read'])]
    private ?array $taskConfig = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAcquisitionSystem(): ?AcquisitionSystem
    {
        return $this->acquisitionSystem;
    }

    public function setAcquisitionSystem(?AcquisitionSystem $acquisitionSystem): static
    {
        $this->acquisitionSystem = $acquisitionSystem;
        return $this;
    }

    public function getTaskName(): ?string
    {
        return $this->taskName;
    }

    public function setTaskName(string $taskName): static
    {
        $this->taskName = $taskName;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getIntervalMs(): ?int
    {
        return $this->intervalMs;
    }

    public function setIntervalMs(int $intervalMs): static
    {
        $this->intervalMs = $intervalMs;
        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    public function getTaskConfig(): ?array
    {
        return $this->taskConfig;
    }

    public function setTaskConfig(?array $taskConfig): static
    {
        $this->taskConfig = $taskConfig;
        return $this;
    }
}
