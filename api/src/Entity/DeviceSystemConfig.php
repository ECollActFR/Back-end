<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DeviceSystemConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeviceSystemConfigRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['deviceSystemConfig:read']],
    denormalizationContext: ['groups' => ['deviceSystemConfig:write']]
)]
class DeviceSystemConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['deviceSystemConfig:read', 'acquisitionSystem:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'systemConfig', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcquisitionSystem $acquisitionSystem = null;

    #[ORM\Column]
    #[Groups(['deviceSystemConfig:read', 'deviceSystemConfig:write', 'acquisitionSystem:read'])]
    private bool $debug = false;

    #[ORM\Column]
    #[Groups(['deviceSystemConfig:read', 'deviceSystemConfig:write', 'acquisitionSystem:read'])]
    #[Assert\Positive(message: 'La taille du buffer doit être positive')]
    #[Assert\Range(
        min: 1,
        max: 1000,
        notInRangeMessage: 'La taille du buffer doit être entre {{ min }} et {{ max }}'
    )]
    private ?int $bufferSize = null;

    #[ORM\Column]
    #[Groups(['deviceSystemConfig:read', 'deviceSystemConfig:write', 'acquisitionSystem:read'])]
    private bool $deepSleepEnabled = false;

    #[ORM\Column]
    #[Groups(['deviceSystemConfig:read', 'deviceSystemConfig:write', 'acquisitionSystem:read'])]
    private bool $webServerEnabled = false;

    #[ORM\Column]
    #[Groups(['deviceSystemConfig:read', 'deviceSystemConfig:write', 'acquisitionSystem:read'])]
    #[Assert\Range(
        min: 1,
        max: 65535,
        notInRangeMessage: 'Le port du serveur web doit être entre {{ min }} et {{ max }}'
    )]
    private ?int $webServerPort = 80;

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

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): static
    {
        $this->debug = $debug;
        return $this;
    }

    public function getBufferSize(): ?int
    {
        return $this->bufferSize;
    }

    public function setBufferSize(int $bufferSize): static
    {
        $this->bufferSize = $bufferSize;
        return $this;
    }

    public function isDeepSleepEnabled(): bool
    {
        return $this->deepSleepEnabled;
    }

    public function setDeepSleepEnabled(bool $deepSleepEnabled): static
    {
        $this->deepSleepEnabled = $deepSleepEnabled;
        return $this;
    }

    public function isWebServerEnabled(): bool
    {
        return $this->webServerEnabled;
    }

    public function setWebServerEnabled(bool $webServerEnabled): static
    {
        $this->webServerEnabled = $webServerEnabled;
        return $this;
    }

    public function getWebServerPort(): ?int
    {
        return $this->webServerPort;
    }

    public function setWebServerPort(int $webServerPort): static
    {
        $this->webServerPort = $webServerPort;
        return $this;
    }
}
