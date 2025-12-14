<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Mapper\UserMapper;
use App\Dto\Output\TokenValidationOutputDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserProvider implements ProviderInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
        private ItemProvider $itemProvider,
        private UserRepository $repository,
        private UserMapper $mapper,
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Gestion /users/me
        if ($operation->getName() === 'get_me') {
            return $this->handleGetMe();
        }

        // Gestion /auth/validate
        if ($operation->getName() === 'validate_token') {
            return $this->handleValidateToken();
        }

        // GetCollection
        if ($operation instanceof CollectionOperationInterface) {
            $user = $this->tokenStorage->getToken()?->getUser();
            
            if ($user && $user instanceof User && method_exists($user, 'getClientAccount') && $user->getClientAccount()) {
                // Si super admin, utiliser le provider par défaut sans filtrage
                if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    $users = $this->collectionProvider->provide($operation, $uriVariables, $context);
                } else {
                    // Sinon, créer un query builder avec filtrage et laisser API Platform gérer la pagination
                    $clientAccount = $user->getClientAccount();
                    $qb = $this->repository->createQueryBuilder('u')
                        ->where('u.clientAccount = :clientAccount')
                        ->setParameter('clientAccount', $clientAccount);
                    
                    $context['query_builder'] = $qb;
                    $users = $this->collectionProvider->provide($operation, $uriVariables, $context);
                }
            } else {
                $users = $this->collectionProvider->provide($operation, $uriVariables, $context);
            }
            
            // Mapper les résultats en DTOs
            if (is_array($users)) {
                return array_map(
                    fn($user) => $this->mapper->mapEntityToOutputDto($user),
                    $users
                );
            }
            
            return $users;
        }

        // Get single
        $user = $this->itemProvider->provide($operation, $uriVariables, $context);
        return $user ? $this->mapper->mapEntityToOutputDto($user) : null;
    }

    private function handleGetMe(): ?object
    {
        $authenticatedUser = $this->tokenStorage->getToken()?->getUser();
        
        if (!$authenticatedUser instanceof User) {
            return null;
        }

        return $this->mapper->mapEntityToOutputDto($authenticatedUser);
    }

    private function handleValidateToken(): TokenValidationOutputDto
    {
        $token = $this->tokenStorage->getToken();

        // Vérifier si un token existe
        if (!$token) {
            return new TokenValidationOutputDto(
                success: false,
                message: 'Aucun token fourni'
            );
        }

        $authenticatedUser = $token->getUser();

        // Vérifier si l'utilisateur existe et est valide
        if (!$authenticatedUser instanceof User) {
            return new TokenValidationOutputDto(
                success: false,
                message: 'Token invalide ou expiré'
            );
        }

        // Vérifier si le compte est actif
        if (!$authenticatedUser->isActive()) {
            return new TokenValidationOutputDto(
                success: false,
                message: 'Compte désactivé'
            );
        }

        return new TokenValidationOutputDto(
            success: true,
            message: 'Token valide'
        );
    }
}