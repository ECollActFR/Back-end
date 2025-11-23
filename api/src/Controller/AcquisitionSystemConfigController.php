<?php

namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Entity\DeviceNetworkConfig;
use App\Entity\DeviceSensor;
use App\Entity\DeviceTask;
use App\Entity\DeviceSystemConfig;
use App\Repository\AcquisitionSystemRepository;
use App\Repository\CaptureTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/acquisition_systems')]
class AcquisitionSystemConfigController extends AbstractController
{
    public function __construct(
        private AcquisitionSystemRepository $acquisitionSystemRepository,
        private CaptureTypeRepository $captureTypeRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('/{id}/configuration', name: 'acquisition_system_get_configuration', methods: ['GET'])]
    public function getConfiguration(int $id): JsonResponse
    {
        $acquisitionSystem = $this->acquisitionSystemRepository->find($id);

        if (!$acquisitionSystem) {
            return $this->json(['error' => 'Système d\'acquisition non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $config = $this->buildConfigurationArray($acquisitionSystem);

        return $this->json($config);
    }

    #[Route('/{id}/configuration', name: 'acquisition_system_post_configuration', methods: ['POST'])]
    public function postConfiguration(int $id, Request $request): JsonResponse
    {
        $acquisitionSystem = $this->acquisitionSystemRepository->find($id);

        if (!$acquisitionSystem) {
            return $this->json(['error' => 'Système d\'acquisition non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->updateConfigurationFromArray($acquisitionSystem, $data);

            $errors = $this->validator->validate($acquisitionSystem);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $this->entityManager->flush();

            $config = $this->buildConfigurationArray($acquisitionSystem);

            return $this->json($config, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/configuration', name: 'acquisition_system_patch_configuration', methods: ['PATCH'])]
    public function patchConfiguration(int $id, Request $request): JsonResponse
    {
        $acquisitionSystem = $this->acquisitionSystemRepository->find($id);

        if (!$acquisitionSystem) {
            return $this->json(['error' => 'Système d\'acquisition non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->updateConfigurationFromArray($acquisitionSystem, $data, true);

            $errors = $this->validator->validate($acquisitionSystem);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $this->entityManager->flush();

            $config = $this->buildConfigurationArray($acquisitionSystem);

            return $this->json($config, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function buildConfigurationArray(AcquisitionSystem $acquisitionSystem): array
    {
        $config = [
            'device' => [
                'id' => $acquisitionSystem->getId(),
                'name' => $acquisitionSystem->getName(),
                'type' => $acquisitionSystem->getDeviceType(),
                'firmware_version' => $acquisitionSystem->getFirmwareVersion(),
                'mac_address' => $acquisitionSystem->getMacAddress(),
                'is_active' => $acquisitionSystem->isActive(),
                'last_seen' => $acquisitionSystem->getLastSeen()?->format('c'),
                'room_id' => $acquisitionSystem->getRoom()?->getId()
            ]
        ];

        // Network configuration
        if ($networkConfig = $acquisitionSystem->getNetworkConfig()) {
            $config['network'] = [
                'wifi' => [
                    'ssid' => $networkConfig->getWifiSsid(),
                    'signal_strength' => $networkConfig->getSignalStrength(),
                    'ip_address' => $networkConfig->getIpAddress(),
                    'mac_address' => $networkConfig->getWifiMacAddress()
                ],
                'ntp' => [
                    'server' => $networkConfig->getNtpServer(),
                    'timezone' => $networkConfig->getTimezone(),
                    'gmt_offset_sec' => $networkConfig->getGmtOffsetSec(),
                    'daylight_offset_sec' => $networkConfig->getDaylightOffsetSec()
                ]
            ];
        }

        // Sensors configuration
        $config['sensors'] = [];
        foreach ($acquisitionSystem->getSensors() as $sensor) {
            $sensorData = [
                'enabled' => $sensor->isEnabled(),
                'type' => $sensor->getCaptureType()->getName(),
                'sensor_type' => $sensor->getSensorType(),
                'read_interval_ms' => $sensor->getReadIntervalMs()
            ];

            if ($sensor->getI2cSdaPin() !== null) {
                $sensorData['i2c_sda_pin'] = $sensor->getI2cSdaPin();
            }
            if ($sensor->getI2cSclPin() !== null) {
                $sensorData['i2c_scl_pin'] = $sensor->getI2cSclPin();
            }
            if ($sensor->getAdcPin() !== null) {
                $sensorData['adc_pin'] = $sensor->getAdcPin();
            }
            if ($sensor->getWarmupDurationSec() !== null) {
                $sensorData['warmup_duration_sec'] = $sensor->getWarmupDurationSec();
            }

            $config['sensors'][$sensor->getSensorType()] = $sensorData;
        }

        // Tasks configuration
        $config['tasks'] = [];
        foreach ($acquisitionSystem->getTasks() as $task) {
            $taskData = [
                'enabled' => $task->isEnabled(),
                'interval_ms' => $task->getIntervalMs(),
                'priority' => $task->getPriority()
            ];

            if ($taskConfig = $task->getTaskConfig()) {
                $taskData = array_merge($taskData, $taskConfig);
            }

            $config['tasks'][$task->getTaskName()] = $taskData;
        }

        // System configuration
        if ($systemConfig = $acquisitionSystem->getSystemConfig()) {
            $config['system'] = [
                'debug' => $systemConfig->isDebug(),
                'buffer_size' => $systemConfig->getBufferSize(),
                'deep_sleep_enabled' => $systemConfig->isDeepSleepEnabled(),
                'web_server_enabled' => $systemConfig->isWebServerEnabled(),
                'web_server_port' => $systemConfig->getWebServerPort()
            ];
        }

        return $config;
    }

    private function updateConfigurationFromArray(AcquisitionSystem $acquisitionSystem, array $data, bool $partial = false): void
    {
        // Update device info
        if (isset($data['device'])) {
            if (isset($data['device']['type'])) {
                $acquisitionSystem->setDeviceType($data['device']['type']);
            }
            if (isset($data['device']['firmware_version'])) {
                $acquisitionSystem->setFirmwareVersion($data['device']['firmware_version']);
            }
            if (isset($data['device']['mac_address'])) {
                $acquisitionSystem->setMacAddress($data['device']['mac_address']);
            }
            if (isset($data['device']['is_active'])) {
                $acquisitionSystem->setIsActive($data['device']['is_active']);
            }
        }

        // Update network config
        if (isset($data['network'])) {
            $networkConfig = $acquisitionSystem->getNetworkConfig();
            if (!$networkConfig) {
                $networkConfig = new DeviceNetworkConfig();
                $networkConfig->setAcquisitionSystem($acquisitionSystem);
                $acquisitionSystem->setNetworkConfig($networkConfig);
            }

            if (isset($data['network']['wifi']['ssid'])) {
                $networkConfig->setWifiSsid($data['network']['wifi']['ssid']);
            }
            if (isset($data['network']['wifi']['signal_strength'])) {
                $networkConfig->setSignalStrength($data['network']['wifi']['signal_strength']);
            }
            if (isset($data['network']['wifi']['ip_address'])) {
                $networkConfig->setIpAddress($data['network']['wifi']['ip_address']);
            }
            if (isset($data['network']['wifi']['mac_address'])) {
                $networkConfig->setWifiMacAddress($data['network']['wifi']['mac_address']);
            }
            if (isset($data['network']['ntp']['server'])) {
                $networkConfig->setNtpServer($data['network']['ntp']['server']);
            }
            if (isset($data['network']['ntp']['timezone'])) {
                $networkConfig->setTimezone($data['network']['ntp']['timezone']);
            }
            if (isset($data['network']['ntp']['gmt_offset_sec'])) {
                $networkConfig->setGmtOffsetSec($data['network']['ntp']['gmt_offset_sec']);
            }
            if (isset($data['network']['ntp']['daylight_offset_sec'])) {
                $networkConfig->setDaylightOffsetSec($data['network']['ntp']['daylight_offset_sec']);
            }
        }

        // Update sensors
        if (isset($data['sensors']) && !$partial) {
            // Remove existing sensors
            foreach ($acquisitionSystem->getSensors() as $sensor) {
                $this->entityManager->remove($sensor);
            }
            $acquisitionSystem->getSensors()->clear();
        }

        if (isset($data['sensors'])) {
            foreach ($data['sensors'] as $sensorKey => $sensorData) {
                $sensor = new DeviceSensor();
                $sensor->setAcquisitionSystem($acquisitionSystem);
                $sensor->setSensorType($sensorData['sensor_type'] ?? $sensorKey);

                // Find capture type by name or ID
                if (isset($sensorData['type'])) {
                    $captureType = $this->captureTypeRepository->findOneBy(['name' => $sensorData['type']]);
                    if ($captureType) {
                        $sensor->setCaptureType($captureType);
                    }
                }

                $sensor->setEnabled($sensorData['enabled'] ?? true);
                $sensor->setReadIntervalMs($sensorData['read_interval_ms']);

                if (isset($sensorData['i2c_sda_pin'])) {
                    $sensor->setI2cSdaPin($sensorData['i2c_sda_pin']);
                }
                if (isset($sensorData['i2c_scl_pin'])) {
                    $sensor->setI2cSclPin($sensorData['i2c_scl_pin']);
                }
                if (isset($sensorData['adc_pin'])) {
                    $sensor->setAdcPin($sensorData['adc_pin']);
                }
                if (isset($sensorData['warmup_duration_sec'])) {
                    $sensor->setWarmupDurationSec($sensorData['warmup_duration_sec']);
                }

                $acquisitionSystem->addSensor($sensor);
                $this->entityManager->persist($sensor);
            }
        }

        // Update tasks
        if (isset($data['tasks']) && !$partial) {
            // Remove existing tasks
            foreach ($acquisitionSystem->getTasks() as $task) {
                $this->entityManager->remove($task);
            }
            $acquisitionSystem->getTasks()->clear();
        }

        if (isset($data['tasks'])) {
            foreach ($data['tasks'] as $taskKey => $taskData) {
                $task = new DeviceTask();
                $task->setAcquisitionSystem($acquisitionSystem);
                $task->setTaskName($taskKey);
                $task->setEnabled($taskData['enabled'] ?? true);
                $task->setIntervalMs($taskData['interval_ms']);
                $task->setPriority($taskData['priority']);

                // Extract task-specific config
                $taskConfig = array_diff_key($taskData, array_flip(['enabled', 'interval_ms', 'priority']));
                if (!empty($taskConfig)) {
                    $task->setTaskConfig($taskConfig);
                }

                $acquisitionSystem->addTask($task);
                $this->entityManager->persist($task);
            }
        }

        // Update system config
        if (isset($data['system'])) {
            $systemConfig = $acquisitionSystem->getSystemConfig();
            if (!$systemConfig) {
                $systemConfig = new DeviceSystemConfig();
                $systemConfig->setAcquisitionSystem($acquisitionSystem);
                $acquisitionSystem->setSystemConfig($systemConfig);
            }

            if (isset($data['system']['debug'])) {
                $systemConfig->setDebug($data['system']['debug']);
            }
            if (isset($data['system']['buffer_size'])) {
                $systemConfig->setBufferSize($data['system']['buffer_size']);
            }
            if (isset($data['system']['deep_sleep_enabled'])) {
                $systemConfig->setDeepSleepEnabled($data['system']['deep_sleep_enabled']);
            }
            if (isset($data['system']['web_server_enabled'])) {
                $systemConfig->setWebServerEnabled($data['system']['web_server_enabled']);
            }
            if (isset($data['system']['web_server_port'])) {
                $systemConfig->setWebServerPort($data['system']['web_server_port']);
            }
        }

        $this->entityManager->persist($acquisitionSystem);
    }
}
