<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class UsersController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/users/{id}/desactivate', name: 'desactivateUser')]
    public function desactivateUser(User $user): JsonResponse
    {
        /** @var User $userAuthenticated */
        $userAuthenticated = $this->getUser();
        if (!$userAuthenticated || $userAuthenticated->getId() !== $user->getId()) {
            return $this->json([
                'error' => 'Accès refusé : vous ne pouvez désactiver que votre propre compte.'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user->isActive()) {
            $user->setIsActive(false);
            $this->entityManager->flush();
        }

        return $this->json([
            'success' => true,
            'message' => 'Compte désactivé avec succès.'
        ]);
    }

    #[Route(path: '/users/me', name: 'getInfosForUser', methods: ['get'], priority: 10)]
    public function getInfosForUser() : JsonResponse {
        $userAuthenticated = $this->getUser();
        if (!$userAuthenticated) {
            return $this->json([
                'user' => $this->getUser(),
                'error' => 'Ce compte n\'existe pas.'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'success' => true,
            'user' => $this->getUser()
        ];
        return $this->json($data);
    }

    #[Route(path: '/auth/logout', name: 'logout', methods: ['post'])]
    public function logout(): JsonResponse
    {
        // Pour JWT, la déconnexion est gérée côté client en supprimant le token
        // On pourrait implémenter un système de blacklist si nécessaire
        return $this->json([
            'success' => true,
            'message' => 'Déconnexion réussie.'
        ]);
    }
}
