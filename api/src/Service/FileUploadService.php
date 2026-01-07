<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService
{
    private string $uploadDir;
    private string $publicDir;

    public function __construct(
        private SluggerInterface $slugger
    ) {
        // Utiliser le répertoire temporaire par défaut
        $this->uploadDir = sys_get_temp_dir() . '/profile-pictures';
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function uploadProfilePicture(UploadedFile $file): ?string
    {
        // Valider le type de fichier
        if (!$this->isValidImage($file)) {
            throw new \InvalidArgumentException('Le fichier doit être une image valide (JPEG, PNG, WebP)');
        }

        // Valider la taille (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException('L\'image ne doit pas dépasser 5MB');
        }

        // Générer un nom de fichier unique
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        // Déplacer le fichier
        try {
            $file->move($this->uploadDir, $newFilename);
            
            // Vérifier que le fichier a bien été créé
            if (!file_exists($this->uploadDir . '/' . $newFilename)) {
                throw new \RuntimeException('Le fichier n\'a pas pu être déplacé correctement');
            }
            
            return '/tmp/profile-pictures/' . $newFilename;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de l\'upload du fichier: ' . $e->getMessage() . ' (Upload dir: ' . $this->uploadDir . ')');
        }
    }

    public function deleteProfilePicture(?string $filename): bool
    {
        if (empty($filename)) {
            return true;
        }

        $filepath = sys_get_temp_dir() . str_replace('/tmp/', '/', $filename);
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }

        return true;
    }

    private function isValidImage(UploadedFile $file): bool
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/webp'
        ];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        return in_array($file->getMimeType(), $allowedMimeTypes) &&
               in_array(strtolower($file->guessExtension()), $allowedExtensions);
    }
}