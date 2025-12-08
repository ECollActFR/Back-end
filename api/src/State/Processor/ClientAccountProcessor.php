<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ClientAccount;
use App\Repository\ClientAccountRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ClientAccountProcessor implements ProcessorInterface
{
    public function __construct(
        private ClientAccountRepository $clientAccountRepository,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ClientAccount
    {
        $user = $this->security->getUser();
        
        // Si c'est une création, associer l'utilisateur actuel au compte
        if (!$data->getId() && $user) {
            $data->addUser($user);
        }

        // Mettre à jour la date de modification
        $data->setUpdatedAt(new \DateTime());

        $this->clientAccountRepository->save($data, true);

        return $data;
    }
}