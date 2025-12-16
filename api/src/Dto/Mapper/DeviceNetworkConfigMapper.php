<?php

namespace App\Dto\Mapper;

use App\Dto\Input\DeviceNetworkConfigInputDto;
use App\Dto\Output\DeviceNetworkConfigOutputDto;
use App\Entity\DeviceNetworkConfig;

class DeviceNetworkConfigMapper
{
    public function mapInputDtoToEntity(DeviceNetworkConfigInputDto $data, DeviceNetworkConfig $deviceNetworkConfig): void
    {
        if ($data->wifiSSID !== null) {
            $deviceNetworkConfig->setWifiSsid($data->wifiSSID);
        }
        if ($data->ntpServer !== null) {
            $deviceNetworkConfig->setNtpServer($data->ntpServer);
        }
        if ($data->timezone !== null) {
            $deviceNetworkConfig->setTimezone($data->timezone);
        }
    }

    public function mapEntityToOutputDto(DeviceNetworkConfig $deviceNetworkConfig): DeviceNetworkConfigOutputDto
    {
        return new DeviceNetworkConfigOutputDto(
            id: $deviceNetworkConfig->getId(),
            wifiSSID: $deviceNetworkConfig->getWifiSsid(),
            wifiPassword: null, // Not stored in current entity
            ntpServer: $deviceNetworkConfig->getNtpServer(),
            timezone: $deviceNetworkConfig->getTimezone(),
            acquisitionSystemId: $deviceNetworkConfig->getAcquisitionSystem()?->getId(),
            clientAccountId: method_exists($deviceNetworkConfig, 'getClientAccount') ? $deviceNetworkConfig->getClientAccount()?->getId() : null,
        );
    }
}