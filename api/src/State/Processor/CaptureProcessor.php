<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\CaptureInputDto;
use App\Dto\Mapper\CaptureMapper;
use App\Entity\Capture;
use App\Entity\Room;
use App\Entity\CaptureType;
use App\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

final class CaptureProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CaptureMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        $capture = isset($context['previous_data'])
            ? $context['previous_data']
            : new Capture();

        $this->mapper->mapInputDtoToEntity($data, $capture);

        // Resolve room relationship
        if ($data->roomId) {
            $room = $this->entityManager->getRepository(Room::class)->find($data->roomId);
            if (!$room) {
                throw new \InvalidArgumentException('Room not found');
            }
            $capture->setRoom($room);
        }

        // Resolve capture type relationship
        if ($data->captureTypeId) {
            $captureType = $this->entityManager->getRepository(CaptureType::class)->find($data->captureTypeId);
            if (!$captureType) {
                throw new \InvalidArgumentException('CaptureType not found');
            }
            $capture->setCaptureType($captureType);
        }

        // Set client account
        if ($data->clientAccountId && method_exists($capture, 'setClientAccount')) {
            $clientAccount = $this->entityManager->getRepository(ClientAccount::class)->find($data->clientAccountId);
            if ($clientAccount) {
                $capture->setClientAccount($clientAccount);
            }
        }

        $this->entityManager->persist($capture);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($capture);
    }
}