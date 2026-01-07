<?php

namespace App\Controller;

use App\Service\FileUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ProfilePictureController extends AbstractController
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    #[Route('/profile-picture/upload', methods: ['POST'])]
    public function uploadProfilePicture(Request $request): JsonResponse
    {
        $profilePictureFile = $request->files->get('profilePicture');

        if (!$profilePictureFile) {
            return new JsonResponse([
                'error' => 'Aucun fichier image fourni'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $filename = $this->fileUploadService->uploadProfilePicture($profilePictureFile);
            return new JsonResponse([
                'success' => true,
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}