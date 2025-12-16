<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\DeviceSensorInputDto;
use App\Dto\Mapper\DeviceSensorMapper;
use App\Entity\DeviceSensor;
use App\Entity\AcquisitionSystem;
use App\Entity\CaptureType;
use App\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

final class DeviceSensorProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DeviceSensorMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        $deviceSensor = isset($context['previous_data'])
            ? $context['previous_data']
            : new DeviceSensor();

        $this->mapper->mapInputDtoToEntity($data, $deviceSensor);

        // Resolve acquisition system relationship
        if ($data->acquisitionSystemId) {
            $acquisitionSystem = $this->entityManager->getRepository(AcquisitionSystem::class)->find($data->acquisitionSystemId);
            if (!$acquisitionSystem) {
                throw new \InvalidArgumentException('AcquisitionSystem not found');
            }
            $deviceSensor->setAcquisitionSystem($acquisitionSystem);
        }

        // Resolve capture type relationship
        if ($data->captureTypeId) {
            $captureType = $this->entityManager->getRepository(CaptureType::class)->find($data->captureTypeId);
            if (!$captureType) {
                throw new \InvalidArgumentException('CaptureType not found');
            }
            $deviceSensor->setCaptureType($captureType);
        }

        // Set client account
        if ($data->clientAccountId && method_exists($deviceSensor, 'setClientAccount')) {
            $clientAccount = $this->entityManager->getRepository(ClientAccount::class)->find($data->clientAccountId);
            if ($clientAccount) {
                $deviceSensor->setClientAccount($clientAccount);
            }
        }

        $this->entityManager->persist($deviceSensor);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($deviceSensor);
    }
}