<?php

namespace App\Dto\Mapper;

use App\Dto\Input\DeviceSensorInputDto;
use App\Dto\Output\DeviceSensorOutputDto;
use App\Entity\DeviceSensor;

class DeviceSensorMapper
{
    public function mapInputDtoToEntity(DeviceSensorInputDto $data, DeviceSensor $deviceSensor): void
    {
        if ($data->pin !== null) {
            $deviceSensor->setPin($data->pin);
        }
        if ($data->readInterval !== null) {
            $deviceSensor->setReadInterval($data->readInterval);
        }
        if ($data->enabled !== null) {
            $deviceSensor->setEnabled($data->enabled);
        }
    }

    public function mapEntityToOutputDto(DeviceSensor $deviceSensor): DeviceSensorOutputDto
    {
        return new DeviceSensorOutputDto(
            id: $deviceSensor->getId(),
            pin: $deviceSensor->getPin(),
            readInterval: $deviceSensor->getReadInterval(),
            enabled: $deviceSensor->isEnabled(),
            acquisitionSystemId: $deviceSensor->getAcquisitionSystem()?->getId(),
            captureTypeId: $deviceSensor->getCaptureType()?->getId(),
            clientAccountId: method_exists($deviceSensor, 'getClientAccount') ? $deviceSensor->getClientAccount()?->getId() : null,
        );
    }
}