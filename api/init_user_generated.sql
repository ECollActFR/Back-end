-- Script SQL généré automatiquement pour initialiser un utilisateur
-- Généré le: 2025-11-09 20:50:56
-- Basé sur les données des fixtures AppFixtures.php

-- Suppression de l'utilisateur existant si présent
DELETE FROM \`user\` WHERE email = 'alexis.baron.nsd@gmail.com';

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
    'alexis.baron.nsd@gmail.com',
    '["ROLE_USER"]',
    '$2y$13$W8Jx.VLPEgDIel1Jfcy2a.hF7WB.Ir6CScfRC2o5LinoKLefpOP6W',
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