<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\CaptureTypeInputDto;
use App\Dto\Mapper\CaptureTypeMapper;
use App\Entity\CaptureType;
use Doctrine\ORM\EntityManagerInterface;

final class CaptureTypeProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CaptureTypeMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // $data est un CaptureTypeInputDto (ou null pour Delete)

        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        // POST ou PATCH
        $captureType = isset($context['previous_data'])
            ? $context['previous_data']  // PATCH: entité existante
            : new CaptureType();         // POST: nouvelle entité

        $this->mapper->mapInputDtoToEntity($data, $captureType);

        $this->entityManager->persist($captureType);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($captureType);
    }
}
