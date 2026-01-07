<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:consume-emails',
    description: 'Démarre le worker pour consommer les emails en attente'
)]
class ConsumeEmailsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Démarrage du worker de consommation des emails');
        
        $io->info('Le worker écoute maintenant les messages dans la queue "async"...');
        $io->info('Appuyez sur Ctrl+C pour arrêter le worker.');
        
        // Cette commande sera remplacée par le véritable appel à messenger:consume
        // Pour l'instant, nous donnons les instructions à l'utilisateur
        
        return Command::SUCCESS;
    }
}