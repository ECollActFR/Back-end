#!/usr/bin/env php
<?php

/**
 * Script d'initialisation d'un utilisateur
 * Basé sur les données des fixtures AppFixtures.php
 *
 * Exécution depuis le conteneur PHP:
 * docker exec neutria-backend-php php /app/api/init_user.php
 *
 * Ou depuis l'hôte:
 * docker exec -i neutria-backend-database mysql -uuserneutria -pRGKmnVsUVP7m3nGuLMAL3TvRdVMj6h dbneutria < /app/api/init_user_generated.sql
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

echo "=== Initialisation de l'utilisateur ===\n\n";

// Chargement de l'environnement
if (file_exists(__DIR__ . '/.env')) {
    (new Dotenv())->load(__DIR__ . '/.env');
}

// Configuration du hasher de mot de passe
$factory = new PasswordHasherFactory([
    'common' => ['algorithm' => 'bcrypt'],
    'memory-hard' => ['algorithm' => 'sodium'],
    User::class => [
        'algorithm' => 'auto',
        'cost' => 13,
        'time_cost' => 3,
        'memory_cost' => 65536,
    ],
]);

$passwordHasher = $factory->getPasswordHasher(User::class);

// Données de l'utilisateur (depuis AppFixtures.php)
$firstname = 'Alexis';
$lastname = 'Baron';
$email = 'alexis.baron.nsd@gmail.com';
$phone = '+33782058609';
$plainPassword = 'password';
$roles = json_encode(['ROLE_USER']);

// Création d'un utilisateur temporaire pour le hachage
$user = new User($firstname, $lastname, $email, $phone);
$hashedPassword = $passwordHasher->hash($plainPassword);

echo "Données de l'utilisateur:\n";
echo "  - Prénom: $firstname\n";
echo "  - Nom: $lastname\n";
echo "  - Email: $email\n";
echo "  - Téléphone: $phone\n";
echo "  - Mot de passe: $plainPassword\n";
echo "  - Hash: " . substr($hashedPassword, 0, 30) . "...\n\n";

// Génération du fichier SQL
$sqlContent = <<<SQL
-- Script SQL généré automatiquement pour initialiser un utilisateur
-- Généré le: {date}
-- Basé sur les données des fixtures AppFixtures.php

-- Suppression de l'utilisateur existant si présent
DELETE FROM \`user\` WHERE email = '$email';

-- Création de l'utilisateur
INSERT INTO \`user\` (
    email,
    roles,
    password,
    created_at,
    last_login,
    is_active,
    firstname,
    lastname,
    profile_picture_url,
    phone,
    updated_at,
    email_verified
) VALUES (
    '$email',
    '$roles',
    '$hashedPassword',
    NOW(),
    NULL,
    1,
    '$firstname',
    '$lastname',
    NULL,
    '$phone',
    NULL,
    0
);

-- Affichage de confirmation
SELECT CONCAT('Utilisateur créé avec ID: ', LAST_INSERT_ID()) AS message;
SQL;

$sqlContent = str_replace('{date}', date('Y-m-d H:i:s'), $sqlContent);

// Sauvegarde du fichier SQL
$sqlFilePath = __DIR__ . '/init_user_generated.sql';
file_put_contents($sqlFilePath, $sqlContent);

echo "✓ Fichier SQL généré: $sqlFilePath\n\n";
echo "Pour exécuter le script SQL:\n";
echo "  docker exec -i neutria-backend-database mysql -uuserneutria -pRGKmnVsUVP7m3nGuLMAL3TvRdVMj6h dbneutria < api/init_user_generated.sql\n\n";

// Connexion directe à la base de données et insertion
$dbHost = getenv('DATABASE_HOST') ?: 'database';
$dbPort = getenv('DATABASE_PORT') ?: '3306';
$dbName = getenv('DATABASE_NAME') ?: 'dbneutria';
$dbUser = getenv('DATABASE_USER') ?: 'userneutria';
$dbPassword = getenv('DATABASE_PASSWORD') ?: 'RGKmnVsUVP7m3nGuLMAL3TvRdVMj6h';

try {
    echo "Tentative de connexion à la base de données...\n";
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "✓ Connexion établie\n\n";

    // Suppression de l'utilisateur existant
    echo "Suppression de l'utilisateur existant (si présent)...\n";
    $stmt = $pdo->prepare("DELETE FROM `user` WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $deletedRows = $stmt->rowCount();
    echo "  → $deletedRows ligne(s) supprimée(s)\n\n";

    // Insertion du nouvel utilisateur
    echo "Insertion du nouvel utilisateur...\n";
    $stmt = $pdo->prepare("
        INSERT INTO `user` (
            email, roles, password, created_at, last_login, is_active,
            firstname, lastname, profile_picture_url, phone, updated_at, email_verified
        ) VALUES (
            :email, :roles, :password, NOW(), NULL, 1,
            :firstname, :lastname, NULL, :phone, NULL, 0
        )
    ");

    $stmt->execute([
        'email' => $email,
        'roles' => $roles,
        'password' => $hashedPassword,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'phone' => $phone,
    ]);

    $userId = $pdo->lastInsertId();
    echo "  → Utilisateur créé avec ID: $userId\n\n";

    echo "✓ Utilisateur initialisé avec succès!\n";

} catch (PDOException $e) {
    echo "✗ Erreur de connexion à la base de données:\n";
    echo "  " . $e->getMessage() . "\n\n";
    echo "Le fichier SQL a été généré et peut être exécuté manuellement.\n";
    exit(1);
}
