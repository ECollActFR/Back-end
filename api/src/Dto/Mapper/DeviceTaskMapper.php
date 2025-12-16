<?php

namespace App\Dto\Mapper;

use App\Dto\Input\DeviceTaskInputDto;
use App\Dto\Output\DeviceTaskOutputDto;
use App\Entity\DeviceTask;

class DeviceTaskMapper
{
    public function mapInputDtoToEntity(DeviceTaskInputDto $data, DeviceTask $deviceTask): void
    {
        if ($data->taskName !== null) {
            $deviceTask->setTaskName($data->taskName);
        }
        if ($data->taskType !== null) {
            $deviceTask->setTaskType($data->taskType);
        }
        if ($data->parameters !== null) {
            $deviceTask->setParameters($data->parameters);
        }
        if ($data->enabled !== null) {
            $deviceTask->setEnabled($data->enabled);
        }
        if ($data->executionInterval !== null) {
            $deviceTask->setExecutionInterval($data->executionInterval);
        }
        if ($data->priority !== null) {
            $deviceTask->setPriority($data->priority);
        }
    }

    public function mapEntityToOutputDto(DeviceTask $deviceTask): DeviceTaskOutputDto
    {
        return new DeviceTaskOutputDto(
            id: $deviceTask->getId(),
            taskName: $deviceTask->getTaskName(),
            taskType: $deviceTask->getTaskType(),
            parameters: $deviceTask->getParameters(),
            enabled: $deviceTask->isEnabled(),
            executionInterval: $deviceTask->getExecutionInterval(),
            priority: $deviceTask->getPriority(),
            acquisitionSystemId: $deviceTask->getAcquisitionSystem()?->getId(),
            clientAccountId: method_exists($deviceTask, 'getClientAccount') ? $deviceTask->getClientAccount()?->getId() : null,
        );
    }
}