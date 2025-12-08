<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\AcquisitionSystem;
use App\Repository\AcquisitionSystemRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AcquisitionSystemProvider implements ProviderInterface
{
    public function __construct(
        private AcquisitionSystemRepository $acquisitionSystemRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            return $this->acquisitionSystemRepository->find($uriVariables['id']);
        }

        // Pour les collections, filtrer par propriétaire du building de la room
        if ($user && method_exists($user, 'getId')) {
            return $this->acquisitionSystemRepository->createQueryBuilder('a')
                ->join('a.room', 'r')
                ->join('r.building', 'b')
                ->where('b.owner = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
        }

        return [];
    }
}