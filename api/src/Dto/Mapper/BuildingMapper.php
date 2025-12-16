<?php

namespace App\Dto\Mapper;

use App\Dto\Input\BuildingInputDto;
use App\Dto\Output\BuildingOutputDto;
use App\Entity\Building;

class BuildingMapper
{
    public function mapInputDtoToEntity(BuildingInputDto $data, Building $building): void
    {
        $building->setName($data->name);
    }

    public function mapEntityToOutputDto(Building $building): BuildingOutputDto
    {
        return new BuildingOutputDto(
            id: $building->getId(),
            name: $building->getName(),
            ownerId: $building->getOwner()?->getId(),
            clientAccountId: $building->getClientAccount()?->getId(),
        );
    }
}