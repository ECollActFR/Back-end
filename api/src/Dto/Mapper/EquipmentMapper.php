<?php

namespace App\Dto\Mapper;

use App\Dto\Input\EquipmentInputDto;
use App\Dto\Output\EquipmentOutputDto;
use App\Entity\Equipment;

class EquipmentMapper
{
    public function mapInputDtoToEntity(EquipmentInputDto $dto, Equipment $equipment): Equipment
    {
        $equipment->setName($dto->name);
        $equipment->setCapacity($dto->capacity);

        return $equipment;
    }

    public function mapEntityToOutputDto(Equipment $equipment): EquipmentOutputDto
    {
        $roomIds = [];
        foreach ($equipment->getRooms() as $room) {
            $roomIds[] = $room->getId();
        }

        return new EquipmentOutputDto(
            id: $equipment->getId(),
            name: $equipment->getName(),
            capacity: $equipment->getCapacity(),
            createdAt: $equipment->getCreatedAt()?->format('c'),
            roomIds: $roomIds,
        );
    }
}
