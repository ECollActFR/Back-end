<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\AcquisitionSystemInputDto;
use App\Dto\Mapper\AcquisitionSystemMapper;
use App\Entity\AcquisitionSystem;
use App\Entity\Room;
use App\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

final class AcquisitionSystemProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AcquisitionSystemMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        $acquisitionSystem = isset($context['previous_data'])
            ? $context['previous_data']
            : new AcquisitionSystem();

        $this->mapper->mapInputDtoToEntity($data, $acquisitionSystem);

        // Resolve room relationship
        if ($data->roomId) {
            $room = $this->entityManager->getRepository(Room::class)->find($data->roomId);
            if (!$room) {
                throw new \InvalidArgumentException('Room not found');
            }
            $acquisitionSystem->setRoom($room);
        }

        // Set client account
        if ($data->clientAccountId && method_exists($acquisitionSystem, 'setClientAccount')) {
            $clientAccount = $this->entityManager->getRepository(ClientAccount::class)->find($data->clientAccountId);
            if ($clientAccount) {
                $acquisitionSystem->setClientAccount($clientAccount);
            }
        }

        $this->entityManager->persist($acquisitionSystem);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($acquisitionSystem);
    }
}