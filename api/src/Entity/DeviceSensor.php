<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DeviceSensorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeviceSensorRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['deviceSensor:read']],
    denormalizationContext: ['groups' => ['deviceSensor:write']]
)]
class DeviceSensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['deviceSensor:read', 'acquisitionSystem:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sensors')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['deviceSensor:read', 'deviceSensor:write'])]
    private ?AcquisitionSystem $acquisitionSystem = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['deviceSensor:read', 'deviceSensor:write', 'acquisitionSystem:read'])]
    private ?CaptureType $captureType = null;

    #[ORM\Column(length: 50)]
    #[Groups(['deviceSensor:read', 'deviceSensor:write', 'acquisitionSystem:read'])]
    #[Assert\NotBlank(message: 'Le type de capteur est obligatoire')]
    #[Assert\Length(max: 50)]
    #[Assert\Choice(
        choices: ['aht20', 'mq135', 'bh1750', 'sound_sensor', 'generic'],
        message: 'Le type de capteur doit être l\'un des suivants : {{ choices }}'
    )]
    private ?string $sensorType = null;

    #[ORM\Column]
    #[Groups(['deviceSensor:read', 'deviceSensor:write', 'acquisitionSystem:read'])]
    private bool $enabled = true;

    #[ORM\Column]
    #[Groups(['deviceSensor:read', 'deviceSensor:write', 'acquisitionSystem:read'])]
    #[Assert\Positive(message: 'L\'intervalle de lecture doit être positif')]
    #[Assert\Range(
        min: 100,
        max: 3600000,
        notInRangeMessage: 'L\'intervalle de lecture doit être entre {{ min }}ms et {{ max }}ms (1 heure)'
    )]
    private ?int $readIntervalMs = null;

    // I2C Pins (for sensors like AHT20)
    #[ORM\Column(nullable: true)]
    #[Groups(['deviceSensor:read', 'deviceSensor:write', 'acquisitionSystem:read'])]
    #[Assert\Range(
        min: 0,
        max: 39,
        notInRangeMessage: 'Le pin I2C SDA doit être entre {{ min }} et {{ max }} (ESP32)'
    )]
    private ?int $i2cSdaPin = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['deviceSensor:read', 'deviceSensor:write', 'acquisitionSystem:read'])]
    #[Assert\Range(
        min: 0,
        max: 39,
        notInRangeMessage: 'Le pin I2C SCL doit être entre {{ min }} et {{ max }} (ESP32)'
    )]
    private ?int $i2cSclPin = null;

    // ADC Pin (for analog sensors like MQ135)
    #[ORM\Column(nullable: true)]
    #[Groups(['deviceSensor:read', 'deviceSensor:write', 'acquisitionSystem:read'])]
    #[Assert\Range(
        min: 32,
        max: 39,
        notInRangeMessage: 'Le pin ADC doit être entre {{ min }} et {{ max }} (ESP32 ADC1)'
    )]
    private ?int $adcPin = null;

    // Warmup duration for sensors like MQ135 (gas sensors)
    #[ORM\Column(nullable: true)]
    #[Groups(['deviceSensor:read', 'deviceSensor:write', 'acquisitionSystem:read'])]
    #[Assert\Range(
        min: 0,
        max: 3600,
        notInRangeMessage: 'La durée de préchauffage doit être entre {{ min }} et {{ max }} secondes'
    )]
    private ?int $warmupDurationSec = null;

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

    public function getCaptureType(): ?CaptureType
    {
        return $this->captureType;
    }

    public function setCaptureType(?CaptureType $captureType): static
    {
        $this->captureType = $captureType;
        return $this;
    }

    public function getSensorType(): ?string
    {
        return $this->sensorType;
    }

    public function setSensorType(string $sensorType): static
    {
        $this->sensorType = $sensorType;
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

    public function getReadIntervalMs(): ?int
    {
        return $this->readIntervalMs;
    }

    public function setReadIntervalMs(int $readIntervalMs): static
    {
        $this->readIntervalMs = $readIntervalMs;
        return $this;
    }

    public function getI2cSdaPin(): ?int
    {
        return $this->i2cSdaPin;
    }

    public function setI2cSdaPin(?int $i2cSdaPin): static
    {
        $this->i2cSdaPin = $i2cSdaPin;
        return $this;
    }

    public function getI2cSclPin(): ?int
    {
        return $this->i2cSclPin;
    }

    public function setI2cSclPin(?int $i2cSclPin): static
    {
        $this->i2cSclPin = $i2cSclPin;
        return $this;
    }

    public function getAdcPin(): ?int
    {
        return $this->adcPin;
    }

    public function setAdcPin(?int $adcPin): static
    {
        $this->adcPin = $adcPin;
        return $this;
    }

    public function getWarmupDurationSec(): ?int
    {
        return $this->warmupDurationSec;
    }

    public function setWarmupDurationSec(?int $warmupDurationSec): static
    {
        $this->warmupDurationSec = $warmupDurationSec;
        return $this;
    }
}
