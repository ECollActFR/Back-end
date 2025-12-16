<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\DeviceSystemConfigInputDto;
use App\Dto\Mapper\DeviceSystemConfigMapper;
use App\Entity\DeviceSystemConfig;
use App\Entity\AcquisitionSystem;
use App\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

final class DeviceSystemConfigProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DeviceSystemConfigMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        $deviceSystemConfig = isset($context['previous_data'])
            ? $context['previous_data']
            : new DeviceSystemConfig();

        $this->mapper->mapInputDtoToEntity($data, $deviceSystemConfig);

        // Resolve acquisition system relationship
        if ($data->acquisitionSystemId) {
            $acquisitionSystem = $this->entityManager->getRepository(AcquisitionSystem::class)->find($data->acquisitionSystemId);
            if (!$acquisitionSystem) {
                throw new \InvalidArgumentException('AcquisitionSystem not found');
            }
            $deviceSystemConfig->setAcquisitionSystem($acquisitionSystem);
        }

        // Set client account
        if ($data->clientAccountId && method_exists($deviceSystemConfig, 'setClientAccount')) {
            $clientAccount = $this->entityManager->getRepository(ClientAccount::class)->find($data->clientAccountId);
            if ($clientAccount) {
                $deviceSystemConfig->setClientAccount($clientAccount);
            }
        }

        $this->entityManager->persist($deviceSystemConfig);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($deviceSystemConfig);
    }
}