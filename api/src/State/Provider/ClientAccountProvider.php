<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\ClientAccount;
use App\Repository\ClientAccountRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ClientAccountProvider implements ProviderInterface
{
    public function __construct(
        private ClientAccountRepository $clientAccountRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if ($operation->getName() === 'get') {
            $clientAccount = $this->clientAccountRepository->find($uriVariables['id']);
            
            // Vérifier que l'utilisateur a accès à ce compte client
            if ($clientAccount && $user && $clientAccount->getUsers()->contains($user)) {
                return $clientAccount;
            }
            
            return null;
        }

        // Pour les collections, retourner le compte client de l'utilisateur
        if ($user && method_exists($user, 'getClientAccount')) {
            $clientAccount = $user->getClientAccount();
            return $clientAccount ? [$clientAccount] : [];
        }

        return [];
    }
}