# Configuration du service Email avec Brevo

## Installation

### 1. Installer les dépendances

Ajoutez ces dépendances à votre `composer.json` :

```json
"symfony/mailer": "7.4.*",
"symfony/messenger": "7.4.*",
"symfony/brevo-mailer": "^7.4"
```

Puis exécutez :
```bash
composer install
```

### 2. Configurer Brevo dans le fichier .env

Ajoutez ces lignes à votre fichier `.env` :

```env
# Configuration Brevo (remplacez VOTRE_CLE_API par votre vraie clé)
MAILER_DSN=brevo://VOTRE_CLE_API@default

# URL du frontend pour les emails
FRONTEND_URL=https://app.ecollact.fr/login
```

### 3. Configuration automatique

La configuration est déjà intégrée dans `config/packages/framework.yaml` :
- Mailer configuré pour utiliser Brevo
- Messenger configuré pour l'envoi asynchrone
- Transport Doctrine pour stocker les emails en BDD

### 4. Créer la table Messenger

```bash
# Générez la migration pour les tables Messenger
php bin/console doctrine:migrations:diff

# Appliquez la migration
php bin/console doctrine:migrations:migrate
```

### 5. Démarrer le worker Messenger

Pour traiter les emails en arrière-plan :

```bash
# Démarrer le worker (terminal actif)
php bin/console messenger:consume async

# Ou en arrière-plan avec nohup
nohup php bin/console messenger:consume async > messenger.log 2>&1 &
```

## Fonctionnalités implémentées

### 1. Service EmailService (`src/Service/EmailService.php`)
- Envoi d'emails asynchrones via Messenger
- Template HTML personnalisé
- Intégration automatique avec UserProcessor

### 2. Message et Handler
- `SendEmailMessage` : Message pour l'envoi d'email
- `SendEmailMessageHandler` : Handler qui traite les messages

### 3. Templates
- `templates/emails/welcome.html.twig` : Email de bienvenue avec identifiants
- Design responsive avec CSS intégré
- Lien de connexion personnalisé

### 4. Intégration automatique
L'email est envoyé automatiquement lors de la création d'un utilisateur dans `UserProcessor` :
```php
// Le mot de passe est généré et l'email est envoyé automatiquement
$this->emailService->sendWelcomeEmail($user, $passwordData['plain']);
```

## Utilisation manuelle

Pour envoyer un email manuellement :

```php
use App\Service\EmailService;

$emailService->sendWelcomeEmail($user, $plainPassword);
```

## Configuration en production

### 1. Worker en continu

Configurez un supervisor pour maintenir le worker actif :

```bash
# Installation de supervisor
sudo apt-get install supervisor

# Configuration /etc/supervisor/conf.d/messenger.conf
[program:messenger-consume]
command=php bin/console messenger:consume async
directory=/var/www/api
autostart=true
autorestart=true
numprocs=1
user=www-data
```

### 2. Monitoring

Commandes utiles pour le monitoring :

```bash
# Statistiques des queues
php bin/console messenger:stats

# Voir les messages échoués
php bin/console messenger:failed:show

# Réessayer les messages échoués
php bin/console messenger:failed:retry
```

## Sécurité

- ✅ Les mots de passe sont envoyés uniquement lors de la création initiale
- ✅ L'email recommande de changer le mot de passe immédiatement
- ✅ L'expéditeur est fixé à contact@ecollact.fr
- ✅ Envoi asynchrone pour ne pas bloquer l'interface utilisateur

## Test

Pour tester l'envoi d'emails :

```bash
# Créez un utilisateur via l'API
curl -X POST "http://localhost:8000/api/users" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","firstname":"Test","lastname":"User"}'

# Vérifiez les logs du worker
tail -f messenger.log
```