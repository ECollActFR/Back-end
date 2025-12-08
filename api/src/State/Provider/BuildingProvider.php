<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Building;
use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Service\ClientAccountAccessService;
use Symfony\Bundle\SecurityBundle\Security;

class BuildingProvider implements ProviderInterface
{
    public function __construct(
        private BuildingRepository $buildingRepository,
        private Security $security,
        private ClientAccountAccessService $clientAccountAccessService
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            $building = $this->buildingRepository->find($uriVariables['id']);
            
            // Vérifier que l'utilisateur a accès à ce building
            if ($building && $user && $this->clientAccountAccessService->canAccessResource($building, $user)) {
                return $building;
            }
            
            return null;
        }

        // Pour les collections, filtrer par compte client
        if ($user && $user instanceof User && method_exists($user, 'getClientAccount') && $user->getClientAccount()) {
            // Si super admin, retourner tous les buildings
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                return $this->buildingRepository->findAll();
            }
            
            // Sinon, filtrer par client account
            $clientAccount = $user->getClientAccount();
            return $this->buildingRepository->createQueryBuilder('b')
                ->join('b.owner', 'u')
                ->where('u.clientAccount = :clientAccount')
                ->setParameter('clientAccount', $clientAccount)
                ->getQuery()
                ->getResult();
        }

        return [];
    }
}