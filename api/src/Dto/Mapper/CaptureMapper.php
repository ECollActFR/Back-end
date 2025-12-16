<?php

namespace App\Dto\Mapper;

use App\Dto\Input\CaptureInputDto;
use App\Dto\Output\CaptureOutputDto;
use App\Entity\Capture;

class CaptureMapper
{
    public function mapInputDtoToEntity(CaptureInputDto $data, Capture $capture): void
    {
        $capture->setValue($data->value);
    }

    public function mapEntityToOutputDto(Capture $capture): CaptureOutputDto
    {
        return new CaptureOutputDto(
            id: $capture->getId(),
            value: $capture->getValue(),
            roomId: $capture->getRoom()?->getId(),
            captureTypeId: $capture->getCaptureType()?->getId(),
            createdAt: $capture->getCreatedAt(),
            clientAccountId: method_exists($capture, 'getClientAccount') ? $capture->getClientAccount()?->getId() : null,
        );
    }
}