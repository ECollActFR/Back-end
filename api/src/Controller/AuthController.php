<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AuthController extends AbstractController
{
    public function __construct(
        private TokenStorageInterface $tokenStorage
    ) {}

    #[Route(path: '/auth/validate', name: 'validate_token', methods: ['POST'])]
    public function validateToken(): JsonResponse
    {
        $token = $this->tokenStorage->getToken();

        // Vérifier si un token existe
        if (!$token) {
            return $this->json([
                'success' => false,
                'message' => 'Aucun token fourni'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $authenticatedUser = $token->getUser();

        // Vérifier si l'utilisateur existe et est valide
        if (!$authenticatedUser instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Token invalide ou expiré'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier si le compte est actif
        if (!$authenticatedUser->isActive()) {
            return $this->json([
                'success' => false,
                'message' => 'Compte désactivé'
            ], Response::HTTP_FORBIDDEN);
        }

        return $this->json([
            'success' => true,
            'message' => 'Token valide'
        ], Response::HTTP_OK);
    }
}
