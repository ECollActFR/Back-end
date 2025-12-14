<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AcquisitionSystemRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AcquisitionSystemProvider implements ProviderInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
        private ItemProvider $itemProvider,
        private AcquisitionSystemRepository $acquisitionSystemRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            return $this->itemProvider->provide($operation, $uriVariables, $context);
        }

        // Pour les collections, filtrer par propriétaire du building de la room
        if ($user && method_exists($user, 'getId')) {
            // Créer un query builder avec filtrage et laisser API Platform gérer la pagination
            $qb = $this->acquisitionSystemRepository->createQueryBuilder('a')
                ->join('a.room', 'r')
                ->join('r.building', 'b')
                ->where('b.owner = :user')
                ->setParameter('user', $user);
            
            // Ajouter le query builder au contexte pour qu'API Platform l'utilise
            $context['query_builder'] = $qb;
            
            return $this->collectionProvider->provide($operation, $uriVariables, $context);
        }

        return [];
    }
}