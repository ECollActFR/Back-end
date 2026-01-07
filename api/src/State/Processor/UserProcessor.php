<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Input\UserInputDto;
use App\Dto\Input\UserUpdateDto;
use App\Dto\Mapper\UserMapper;
use App\Entity\User;
use App\Entity\ClientAccount;
use App\Service\PasswordGeneratorService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserMapper $mapper,
        private UserPasswordHasherInterface $passwordHasher,
        private TokenStorageInterface $tokenStorage,
        private PasswordGeneratorService $passwordGenerator,
        private EmailService $emailService,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Gestion de l'opération /users/{id}/desactivate
        if ($operation instanceof Delete && str_ends_with($context['uri'] ?? '', '/desactivate')) {
            return $this->handleDesactivate($uriVariables['id']);
        }

        // DELETE
        if ($operation instanceof Delete) {
            $this->entityManager->remove($context['previous_data']);
            $this->entityManager->flush();
            return null;
        }

        // POST (création)
        if (!isset($context['previous_data'])) {
            $user = new User();
            
            if ($data instanceof UserInputDto) {
                $this->mapper->mapInputDtoToEntity($data, $user);
                
                // Générer et hasher le mot de passe automatiquement
                $passwordData = $this->passwordGenerator->generateAndHashPassword($this->passwordHasher, $user);
                $user->setPassword($passwordData['hashed']);
                
                // Envoyer l'email de bienvenue avec les identifiants
                $this->emailService->sendWelcomeEmail($user, $passwordData['plain']);
                
                // Associer au compte client si spécifié
                if ($data->clientAccountId) {
                    $clientAccount = $this->entityManager->getRepository(ClientAccount::class)->find($data->clientAccountId);
                    if ($clientAccount) {
                        $user->setClientAccount($clientAccount);
                    }
                }
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->mapper->mapEntityToOutputDto($user);
        }

        // PATCH (mise à jour)
        $user = $context['previous_data'];
        
        if ($data instanceof UserUpdateDto) {
            $this->mapper->mapUpdateDtoToEntity($data, $user);
            
            // Hacher le mot de passe si fourni
            if ($data->password !== null) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $data->password);
                $user->setPassword($hashedPassword);
            }
        }

        $this->entityManager->flush();

        return $this->mapper->mapEntityToOutputDto($user);
    }

    private function handleDesactivate(int $userId): JsonResponse
    {
        $authenticatedUser = $this->tokenStorage->getToken()?->getUser();
        
        if (!$authenticatedUser instanceof User) {
            return new JsonResponse([
                'error' => 'Utilisateur non authentifié'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        
        if (!$user) {
            return new JsonResponse([
                'error' => 'Utilisateur non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($authenticatedUser->getId() !== $user->getId()) {
            return new JsonResponse([
                'error' => 'Accès refusé : vous ne pouvez désactiver que votre propre compte.'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user->isActive()) {
            $user->setIsActive(false);
            $this->entityManager->flush();
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Compte désactivé avec succès.'
        ]);
    }
}
