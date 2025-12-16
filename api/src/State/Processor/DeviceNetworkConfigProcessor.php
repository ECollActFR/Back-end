<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\DeviceNetworkConfigInputDto;
use App\Dto\Mapper\DeviceNetworkConfigMapper;
use App\Entity\DeviceNetworkConfig;
use App\Entity\AcquisitionSystem;
use App\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

final class DeviceNetworkConfigProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DeviceNetworkConfigMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        $deviceNetworkConfig = isset($context['previous_data'])
            ? $context['previous_data']
            : new DeviceNetworkConfig();

        $this->mapper->mapInputDtoToEntity($data, $deviceNetworkConfig);

        // Resolve acquisition system relationship
        if ($data->acquisitionSystemId) {
            $acquisitionSystem = $this->entityManager->getRepository(AcquisitionSystem::class)->find($data->acquisitionSystemId);
            if (!$acquisitionSystem) {
                throw new \InvalidArgumentException('AcquisitionSystem not found');
            }
            $deviceNetworkConfig->setAcquisitionSystem($acquisitionSystem);
        }

        // Set client account
        if ($data->clientAccountId && method_exists($deviceNetworkConfig, 'setClientAccount')) {
            $clientAccount = $this->entityManager->getRepository(ClientAccount::class)->find($data->clientAccountId);
            if ($clientAccount) {
                $deviceNetworkConfig->setClientAccount($clientAccount);
            }
        }

        $this->entityManager->persist($deviceNetworkConfig);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($deviceNetworkConfig);
    }
}