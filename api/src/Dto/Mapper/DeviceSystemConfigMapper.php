<?php

namespace App\Dto\Mapper;

use App\Dto\Input\DeviceSystemConfigInputDto;
use App\Dto\Output\DeviceSystemConfigOutputDto;
use App\Entity\DeviceSystemConfig;

class DeviceSystemConfigMapper
{
    public function mapInputDtoToEntity(DeviceSystemConfigInputDto $data, DeviceSystemConfig $deviceSystemConfig): void
    {
        if ($data->debugMode !== null) {
            $deviceSystemConfig->setDebug($data->debugMode);
        }
        if ($data->bufferSize !== null) {
            $deviceSystemConfig->setBufferSize($data->bufferSize);
        }
        if ($data->deepSleepEnabled !== null) {
            $deviceSystemConfig->setDeepSleepEnabled($data->deepSleepEnabled);
        }
        if ($data->webServerEnabled !== null) {
            $deviceSystemConfig->setWebServerEnabled($data->webServerEnabled);
        }
        if ($data->webServerPort !== null) {
            $deviceSystemConfig->setWebServerPort($data->webServerPort);
        }
    }

    public function mapEntityToOutputDto(DeviceSystemConfig $deviceSystemConfig): DeviceSystemConfigOutputDto
    {
        return new DeviceSystemConfigOutputDto(
            id: $deviceSystemConfig->getId(),
            debugMode: $deviceSystemConfig->isDebug(),
            bufferSize: $deviceSystemConfig->getBufferSize(),
            deepSleepEnabled: $deviceSystemConfig->isDeepSleepEnabled(),
            deepSleepInterval: null, // Not stored in current entity
            webServerEnabled: $deviceSystemConfig->isWebServerEnabled(),
            webServerPort: $deviceSystemConfig->getWebServerPort(),
            acquisitionSystemId: $deviceSystemConfig->getAcquisitionSystem()?->getId(),
            clientAccountId: method_exists($deviceSystemConfig, 'getClientAccount') ? $deviceSystemConfig->getClientAccount()?->getId() : null,
        );
    }
}