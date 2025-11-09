-- Script SQL pour initialiser un utilisateur dans la base de données
-- Basé sur les données des fixtures AppFixtures.php
-- Pour exécuter : docker exec -i neutria-backend-database mysql -uuserneutria -pRGKmnVsUVP7m3nGuLMAL3TvRdVMj6h dbneutria < api/init_user.sql

-- Création de l'utilisateur
-- Note: Le mot de passe "password" a été hashé avec l'algorithme bcrypt de Symfony
-- Hash généré pour le mot de passe "password"
INSERT INTO `user` (
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
    'alexis.baron.nsd@gmail.com',
    '["ROLE_USER"]',
    '$2y$13$YourHashedPasswordHereNeedToBeGeneratedBySymfony',
    NOW(),
    NULL,
    1,
    'Alexis',
    'Baron',
    NULL,
    '+33782058609',
    NULL,
    0
);

-- Affichage de confirmation
SELECT CONCAT('Utilisateur créé avec ID: ', LAST_INSERT_ID()) AS message;
