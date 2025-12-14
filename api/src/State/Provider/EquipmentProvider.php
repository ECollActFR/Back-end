<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Mapper\EquipmentMapper;

final class EquipmentProvider implements ProviderInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
        private ItemProvider $itemProvider,
        private EquipmentMapper $mapper,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $equipment = $this->collectionProvider->provide($operation, $uriVariables, $context);
            
            // Mapper les résultats en DTOs
            if (is_array($equipment)) {
                return array_map(
                    fn($item) => $this->mapper->mapEntityToOutputDto($item),
                    $equipment
                );
            }
            
            return $equipment;
        }

        // Get single
        $equipment = $this->itemProvider->provide($operation, $uriVariables, $context);
        return $equipment ? $this->mapper->mapEntityToOutputDto($equipment) : null;
    }
}