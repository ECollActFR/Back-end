<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class ClientAccountProvider implements ProviderInterface
{
    public function __construct(
        private ItemProvider $itemProvider,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            $clientAccount = $this->itemProvider->provide($operation, $uriVariables, $context);
            
            // Vérifier que l'utilisateur a accès à ce compte client
            if ($clientAccount && $user && $clientAccount->getUsers()->contains($user)) {
                return $clientAccount;
            }
            
            return null;
        }

        // Pour les collections, retourner le compte client de l'utilisateur
        if ($user && $user instanceof User && method_exists($user, 'getClientAccount')) {
            $clientAccount = $user->getClientAccount();
            return $clientAccount ? [$clientAccount] : [];
        }

        return [];
    }
}