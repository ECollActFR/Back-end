<?php

namespace App\Service;

use App\Entity\User;
use App\Message\SendEmailMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailService
{
    public function __construct(
        private MessageBusInterface $bus,
        private string $fromEmail = 'contact@ecollact.fr'
    ) {
    }

    public function sendWelcomeEmail(User $user, string $plainPassword): void
    {
        $email = (new TemplatedEmail())
            ->from($this->fromEmail)
            ->to($user->getEmail())
            ->subject('Bienvenue sur Ecollact - Vos identifiants de connexion')
            ->htmlTemplate('emails/welcome.html.twig')
            ->context([
                'user' => $user,
                'plainPassword' => $plainPassword,
                'loginUrl' => $_ENV['FRONTEND_URL'] ?? 'https://app.ecollact.fr/login'
            ]);

        $this->bus->dispatch(new SendEmailMessage($email));
    }
}