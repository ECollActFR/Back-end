<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Mapper\CaptureTypeMapper;
use App\Repository\CaptureTypeRepository;

final class CaptureTypeProvider implements ProviderInterface
{
    public function __construct(
        private CaptureTypeRepository $repository,
        private CaptureTypeMapper $mapper,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $captureTypes = $this->repository->findAll();
            return array_map(
                fn($captureType) => $this->mapper->mapEntityToOutputDto($captureType),
                $captureTypes
            );
        }

        // Get single
        $captureType = $this->repository->find($uriVariables['id']);
        return $captureType ? $this->mapper->mapEntityToOutputDto($captureType) : null;
    }
}
