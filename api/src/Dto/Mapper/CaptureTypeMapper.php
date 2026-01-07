<?php

namespace App\Dto\Mapper;

use App\Dto\Input\CaptureTypeInputDto;
use App\Dto\Output\CaptureTypeOutputDto;
use App\Entity\CaptureType;

class CaptureTypeMapper
{
    public function mapInputDtoToEntity(CaptureTypeInputDto $dto, CaptureType $captureType): CaptureType
    {
        $captureType->setName($dto->name);
        $captureType->setDescription($dto->description);

        return $captureType;
    }

    public function mapEntityToOutputDto(CaptureType $captureType): CaptureTypeOutputDto
    {
        return new CaptureTypeOutputDto(
            id: $captureType->getId(),
            name: $captureType->getName(),
            description: $captureType->getDescription(),
            createdAt: $captureType->getCreatedAt()?->format('c'),
        );
    }
}
