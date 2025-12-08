<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\EquipmentInputDto;
use App\Dto\Mapper\EquipmentMapper;
use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;

final class EquipmentProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EquipmentMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // $data est un EquipmentInputDto (ou null pour Delete)

        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        // POST ou PATCH
        $equipment = isset($context['previous_data'])
            ? $context['previous_data']  // PATCH: entité existante
            : new Equipment();           // POST: nouvelle entité

        $this->mapper->mapInputDtoToEntity($data, $equipment);

        $this->entityManager->persist($equipment);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($equipment);
    }
}
