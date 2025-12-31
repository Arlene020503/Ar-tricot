CREATE DATABASE IF NOT EXISTS ar_tricot_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ar_tricot_db;

-- 1. Table des administrateurs
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    last_login DATETIME
);

-- 2. Table des articles (Créations)
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix_cfa INT NOT NULL, -- Stocké en entier pour le CFA
    image_url VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Table des commandes
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT,
    client_nom VARCHAR(100),
    client_contact VARCHAR(100), -- Téléphone ou email
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE SET NULL
);

-- 4. Table des avis
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_visiteur VARCHAR(100),
    commentaire TEXT,
    contact_visiteur VARCHAR(100),
    status ENUM('en_attente', 'publie') DEFAULT 'en_attente',
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Table des messages de contact
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_nom VARCHAR(100),
    expediteur_contact VARCHAR(100),
    message_contenu TEXT,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);