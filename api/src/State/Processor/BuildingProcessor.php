<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\BuildingInputDto;
use App\Dto\Mapper\BuildingMapper;
use App\Entity\Building;
use App\Entity\User;
use App\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

final class BuildingProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BuildingMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        $building = isset($context['previous_data'])
            ? $context['previous_data']
            : new Building();

        $this->mapper->mapInputDtoToEntity($data, $building);

        // Resolve owner relationship
        if ($data->ownerId) {
            $owner = $this->entityManager->getRepository(User::class)->find($data->ownerId);
            if (!$owner) {
                throw new \InvalidArgumentException('Owner not found');
            }
            $building->setOwner($owner);
        }

        // Resolve clientAccount relationship
        if ($data->clientAccountId) {
            $clientAccount = $this->entityManager->getRepository(ClientAccount::class)->find($data->clientAccountId);
            if (!$clientAccount) {
                throw new \InvalidArgumentException('ClientAccount not found');
            }
            $building->setClientAccount($clientAccount);
        }

        $this->entityManager->persist($building);
        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($building);
    }
}