<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\DeviceTaskInputDto;
use App\Dto\Mapper\DeviceTaskMapper;
use App\Entity\DeviceTask;
use App\Entity\AcquisitionSystem;
use App\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

final class DeviceTaskProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DeviceTaskMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        $deviceTask = isset($context['previous_data'])
            ? $context['previous_data']
            : new DeviceTask();

        $this->mapper->mapInputDtoToEntity($data, $deviceTask);

        // Resolve acquisition system relationship
        if ($data->acquisitionSystemId) {
            $acquisitionSystem = $this->entityManager->getRepository(AcquisitionSystem::class)->find($data->acquisitionSystemId);
            if (!$acquisitionSystem) {
                throw new \InvalidArgumentException('AcquisitionSystem not found');
            }
            $deviceTask->setAcquisitionSystem($acquisitionSystem);
        }

        // Set client account
        if ($data->clientAccountId && method_exists($deviceTask, 'setClientAccount')) {
            $clientAccount = $this->entityManager->getRepository(ClientAccount::class)->find($data->clientAccountId);
            if ($clientAccount) {
                $deviceTask->setClientAccount($clientAccount);
            }
        }

        $this->entityManager->persist($deviceTask);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($deviceTask);
    }
}