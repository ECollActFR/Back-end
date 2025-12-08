<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\RoomRepository;
use App\Service\ClientAccountAccessService;
use Symfony\Bundle\SecurityBundle\Security;

class RoomProvider implements ProviderInterface
{
    public function __construct(
        private RoomRepository $roomRepository,
        private Security $security,
        private ClientAccountAccessService $clientAccountAccessService
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            $room = $this->roomRepository->find($uriVariables['id']);
            
            // Vérifier que l'utilisateur a accès à cette room
            if ($room && $user && $this->clientAccountAccessService->canAccessResource($room, $user)) {
                return $room;
            }
            
            return null;
        }

        // Pour les collections, filtrer par compte client
        if ($user && $user instanceof User && method_exists($user, 'getClientAccount') && $user->getClientAccount()) {
            // Si super admin, retourner toutes les rooms
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                return $this->roomRepository->findAll();
            }
            
            // Sinon, filtrer par client account
            $clientAccount = $user->getClientAccount();
            return $this->roomRepository->createQueryBuilder('r')
                ->join('r.building', 'b')
                ->join('b.owner', 'u')
                ->where('u.clientAccount = :clientAccount')
                ->setParameter('clientAccount', $clientAccount)
                ->getQuery()
                ->getResult();
        }

        return [];
    }
}