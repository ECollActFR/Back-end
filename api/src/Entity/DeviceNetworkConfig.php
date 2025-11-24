<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DeviceNetworkConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeviceNetworkConfigRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['deviceNetworkConfig:read']],
    denormalizationContext: ['groups' => ['deviceNetworkConfig:write']]
)]
class DeviceNetworkConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['deviceNetworkConfig:read', 'acquisitionSystem:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'networkConfig', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcquisitionSystem $acquisitionSystem = null;

    // WiFi Configuration
    #[ORM\Column(length: 100)]
    #[Groups(['deviceNetworkConfig:read', 'deviceNetworkConfig:write', 'acquisitionSystem:read'])]
    #[Assert\NotBlank(message: 'Le SSID WiFi est obligatoire')]
    #[Assert\Length(max: 100)]
    private ?string $wifiSsid = null;

    // NTP Configuration
    #[ORM\Column(length: 100)]
    #[Groups(['deviceNetworkConfig:read', 'deviceNetworkConfig:write', 'acquisitionSystem:read'])]
    #[Assert\NotBlank(message: 'Le serveur NTP est obligatoire')]
    #[Assert\Length(max: 100)]
    private ?string $ntpServer = null;

    #[ORM\Column(length: 50)]
    #[Groups(['deviceNetworkConfig:read', 'deviceNetworkConfig:write', 'acquisitionSystem:read'])]
    #[Assert\NotBlank(message: 'Le timezone est obligatoire')]
    #[Assert\Length(max: 50)]
    private ?string $timezone = null;

    #[ORM\Column]
    #[Groups(['deviceNetworkConfig:read', 'deviceNetworkConfig:write', 'acquisitionSystem:read'])]
    #[Assert\Range(
        min: -43200,
        max: 50400,
        notInRangeMessage: 'Le décalage GMT doit être entre {{ min }} et {{ max }} secondes'
    )]
    private ?int $gmtOffsetSec = null;

    #[ORM\Column]
    #[Groups(['deviceNetworkConfig:read', 'deviceNetworkConfig:write', 'acquisitionSystem:read'])]
    #[Assert\Range(
        min: 0,
        max: 7200,
        notInRangeMessage: 'Le décalage heure d\'été doit être entre {{ min }} et {{ max }} secondes'
    )]
    private ?int $daylightOffsetSec = null;

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

    public function getWifiSsid(): ?string
    {
        return $this->wifiSsid;
    }

    public function setWifiSsid(string $wifiSsid): static
    {
        $this->wifiSsid = $wifiSsid;
        return $this;
    }

    public function getNtpServer(): ?string
    {
        return $this->ntpServer;
    }

    public function setNtpServer(string $ntpServer): static
    {
        $this->ntpServer = $ntpServer;
        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getGmtOffsetSec(): ?int
    {
        return $this->gmtOffsetSec;
    }

    public function setGmtOffsetSec(int $gmtOffsetSec): static
    {
        $this->gmtOffsetSec = $gmtOffsetSec;
        return $this;
    }

    public function getDaylightOffsetSec(): ?int
    {
        return $this->daylightOffsetSec;
    }

    public function setDaylightOffsetSec(int $daylightOffsetSec): static
    {
        $this->daylightOffsetSec = $daylightOffsetSec;
        return $this;
    }
}
