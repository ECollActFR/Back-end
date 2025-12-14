<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\CaptureRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CaptureProvider implements ProviderInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
        private ItemProvider $itemProvider,
        private CaptureRepository $captureRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            $capture = $this->itemProvider->provide($operation, $uriVariables, $context);
            
            // Vérifier les permissions pour l'item
            if ($capture && $user && $user instanceof User) {
                if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    // Vérifier que l'utilisateur a accès à cette capture
                    $room = $capture->getRoom();
                    $building = $room?->getBuilding();
                    if ($building && $building->getOwner()?->getId() !== $user->getId()) {
                        return null;
                    }
                }
            }
            
            return $capture;
        }

        // Pour les collections, utiliser le provider par défaut avec filtrage
        if ($user && $user instanceof User && method_exists($user, 'getId')) {
            // Si super admin, utiliser le provider par défaut sans filtrage
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                return $this->collectionProvider->provide($operation, $uriVariables, $context);
            }
            
            // Sinon, créer un query builder avec filtrage et laisser API Platform gérer la pagination
            $qb = $this->captureRepository->createQueryBuilder('c')
                ->join('c.room', 'r')
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