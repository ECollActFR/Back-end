-- Insertion du compte client Neutria SAS
INSERT INTO client_account (id, name, siret, address, city, postal_code, country, phone, contact_email) 
VALUES (1, 'Neutria SAS', '12345678901234', '15 Rue de la République', 'Paris', '75001', 'France', '+33123456789', 'contact@neutria.fr');

-- Insertion de l'utilisateur Alexis avec mot de passe hashé
INSERT INTO "user" (first_name, last_name, email, phone, roles, password, client_account_id) 
VALUES ('Alexis', 'Baron', 'alexis.baron.nsd@gmail.com', '+33782058609', '["ROLE_SUPER_ADMIN"]', '$2y$13$NhZnGxzhbhgU8rEtMOyE8eYjLZQG3rWvJQKv2L7F4pE9mDq8vI9e', 1);