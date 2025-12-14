<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Service\ClientAccountAccessService;
use Symfony\Bundle\SecurityBundle\Security;

class BuildingProvider implements ProviderInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
        private ItemProvider $itemProvider,
        private BuildingRepository $buildingRepository,
        private Security $security,
        private ClientAccountAccessService $clientAccountAccessService
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            $building = $this->itemProvider->provide($operation, $uriVariables, $context);
            
            // Vérifier que l'utilisateur a accès à ce building
            if ($building && $user && $this->clientAccountAccessService->canAccessResource($building, $user)) {
                return $building;
            }
            
            return null;
        }

        // Pour les collections, filtrer par compte client
        if ($user && $user instanceof User && method_exists($user, 'getClientAccount') && $user->getClientAccount()) {
            // Si super admin, utiliser le provider par défaut sans filtrage
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                return $this->collectionProvider->provide($operation, $uriVariables, $context);
            }
            
            // Sinon, créer un query builder avec filtrage et laisser API Platform gérer la pagination
            $clientAccount = $user->getClientAccount();
            $qb = $this->buildingRepository->createQueryBuilder('b')
                ->join('b.owner', 'u')
                ->where('u.clientAccount = :clientAccount')
                ->setParameter('clientAccount', $clientAccount);
            
            // Ajouter le query builder au contexte pour qu'API Platform l'utilise
            $context['query_builder'] = $qb;
            
            return $this->collectionProvider->provide($operation, $uriVariables, $context);
        }

        return [];
    }
}