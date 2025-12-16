<?php

namespace App\Dto\Mapper;

use App\Dto\Input\RoomInputDto;
use App\Dto\Output\RoomOutputDto;
use App\Entity\Room;

class RoomMapper
{
    public function mapInputDtoToEntity(RoomInputDto $data, Room $room): void
    {
        $room->setName($data->name);
        $room->setDescription($data->description);
    }

    public function mapEntityToOutputDto(Room $room): RoomOutputDto
    {
        $captureTypeIds = [];
        foreach ($room->getCaptureTypes() as $captureType) {
            $captureTypeIds[] = $captureType->getId();
        }

        $equipmentIds = [];
        foreach ($room->getEquipment() as $equipment) {
            $equipmentIds[] = $equipment->getId();
        }

        return new RoomOutputDto(
            id: $room->getId(),
            name: $room->getName(),
            description: $room->getDescription(),
            buildingId: $room->getBuilding()?->getId(),
            captureTypeIds: $captureTypeIds,
            equipmentIds: $equipmentIds,
            createdAt: $room->getCreatedAt(),

        );
    }
}