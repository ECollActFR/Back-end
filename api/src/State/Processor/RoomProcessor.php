<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\RoomInputDto;
use App\Dto\Mapper\RoomMapper;
use App\Entity\Room;
use App\Entity\Building;
use App\Entity\CaptureType;
use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;

final class RoomProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RoomMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        $room = isset($context['previous_data'])
            ? $context['previous_data']
            : new Room();

        $this->mapper->mapInputDtoToEntity($data, $room);

        // Resolve building relationship
        if ($data->buildingId) {
            $building = $this->entityManager->getRepository(Building::class)->find($data->buildingId);
            if (!$building) {
                throw new \InvalidArgumentException('Building not found');
            }
            $room->setBuilding($building);
        }

        // Clear and set capture types
        $room->getCaptureTypes()->clear();
        foreach ($data->captureTypeIds as $captureTypeId) {
            $captureType = $this->entityManager->getRepository(CaptureType::class)->find($captureTypeId);
            if ($captureType) {
                $room->addCaptureType($captureType);
            }
        }

        // Clear and set equipment
        $room->getEquipment()->clear();
        foreach ($data->equipmentIds as $equipmentId) {
            $equipment = $this->entityManager->getRepository(Equipment::class)->find($equipmentId);
            if ($equipment) {
                $room->addEquipment($equipment);
            }
        }

        $this->entityManager->persist($room);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($room);
    }
}