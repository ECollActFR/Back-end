<?php

namespace App\Dto\Mapper;

use App\Dto\Input\AcquisitionSystemInputDto;
use App\Dto\Output\AcquisitionSystemOutputDto;
use App\Entity\AcquisitionSystem;

class AcquisitionSystemMapper
{
    public function mapInputDtoToEntity(AcquisitionSystemInputDto $data, AcquisitionSystem $acquisitionSystem): void
    {
        $acquisitionSystem->setName($data->name);
    }

    public function mapEntityToOutputDto(AcquisitionSystem $acquisitionSystem): AcquisitionSystemOutputDto
    {
        return new AcquisitionSystemOutputDto(
            id: $acquisitionSystem->getId(),
            name: $acquisitionSystem->getName(),
            roomId: $acquisitionSystem->getRoom()?->getId(),
            clientAccountId: method_exists($acquisitionSystem, 'getClientAccount') ? $acquisitionSystem->getClientAccount()?->getId() : null,
        );
    }
}