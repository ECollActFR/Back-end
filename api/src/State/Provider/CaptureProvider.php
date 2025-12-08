<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Capture;
use App\Entity\User;
use App\Repository\CaptureRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CaptureProvider implements ProviderInterface
{
    public function __construct(
        private CaptureRepository $captureRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            return $this->captureRepository->find($uriVariables['id']);
        }

        // Pour les collections, filtrer par propriétaire du building de la room
        if ($user && $user instanceof User && method_exists($user, 'getId')) {
            // Si super admin, retourner toutes les captures
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                return $this->captureRepository->findAll();
            }
            
            // Sinon, filtrer par propriétaire
            return $this->captureRepository->createQueryBuilder('c')
                ->join('c.room', 'r')
                ->join('r.building', 'b')
                ->where('b.owner = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
        }

        return [];
    }
}