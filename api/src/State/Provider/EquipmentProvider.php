<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Mapper\EquipmentMapper;
use App\Repository\EquipmentRepository;

final class EquipmentProvider implements ProviderInterface
{
    public function __construct(
        private EquipmentRepository $repository,
        private EquipmentMapper $mapper,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $equipment = $this->repository->findAll();
            return array_map(
                fn($item) => $this->mapper->mapEntityToOutputDto($item),
                $equipment
            );
        }

        // Get single
        $equipment = $this->repository->find($uriVariables['id']);
        return $equipment ? $this->mapper->mapEntityToOutputDto($equipment) : null;
    }
}
