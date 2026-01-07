<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendEmailMessageHandler
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    public function __invoke(SendEmailMessage $message): void
    {
        $this->mailer->send($message->getEmail());
    }
}