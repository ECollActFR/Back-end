<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Mapper\CaptureTypeMapper;

final class CaptureTypeProvider implements ProviderInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
        private ItemProvider $itemProvider,
        private CaptureTypeMapper $mapper,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $captureTypes = $this->collectionProvider->provide($operation, $uriVariables, $context);
            
            // Mapper les résultats en DTOs
            if (is_array($captureTypes)) {
                return array_map(
                    fn($captureType) => $this->mapper->mapEntityToOutputDto($captureType),
                    $captureTypes
                );
            }
            
            return $captureTypes;
        }

        // Get single
        $captureType = $this->itemProvider->provide($operation, $uriVariables, $context);
        return $captureType ? $this->mapper->mapEntityToOutputDto($captureType) : null;
    }
}