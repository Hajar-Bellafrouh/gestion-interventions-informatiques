CREATE DATABASE IF NOT EXISTS gestion_interventions_ocp;
USE gestion_interventions_ocp;

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    motDePasse VARCHAR(255) NOT NULL, -- Stockera le mot de passe haché
    role ENUM('administrateur', 'responsable', 'technicien', 'utilisateur') NOT NULL,
    niveau INT DEFAULT NULL, -- Pour les techniciens (1, 2, 3)
    actif BOOLEAN DEFAULT TRUE, -- Pour activer/désactiver un utilisateur
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table demandes
CREATE TABLE IF NOT EXISTS demandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priorite ENUM('basse', 'normale', 'haute', 'critique') DEFAULT 'normale',
    statut ENUM('en_attente', 'validee', 'rejetee', 'en_cours', 'terminee', 'en_attente_technicien') DEFAULT 'en_attente',
    utilisateur_id INT NOT NULL, -- Utilisateur qui a créé la demande
    responsable_id INT DEFAULT NULL, -- Responsable qui a validé/assigné
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id)
);

-- Table interventions
CREATE TABLE IF NOT EXISTS interventions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL UNIQUE, -- Une intervention par demande
    technicien_id INT NOT NULL,
    date_planifiee DATETIME NOT NULL,
    statut ENUM('planifiee', 'en_cours', 'terminee', 'en_attente_pieces') DEFAULT 'planifiee',
    rapport TEXT DEFAULT NULL,
    date_debut DATETIME DEFAULT NULL,
    date_fin DATETIME DEFAULT NULL,
    FOREIGN KEY (demande_id) REFERENCES demandes(id),
    FOREIGN KEY (technicien_id) REFERENCES utilisateurs(id)
);

-- Table equipements
CREATE TABLE IF NOT EXISTS equipements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    localisation VARCHAR(255) NOT NULL,
    statut ENUM('operationnel', 'en_panne', 'en_maintenance', 'hors_service') DEFAULT 'operationnel',
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinataire_id INT NOT NULL,
    message TEXT NOT NULL,
    lue BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id)
);

-- Table statistiques (pour les données agrégées ou pré-calculées)
CREATE TABLE IF NOT EXISTS statistiques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode VARCHAR(50) NOT NULL, -- ex: 'mensuel', 'annuel'
    date_rapport DATE NOT NULL,
    total_demandes INT DEFAULT 0,
    demandes_terminees INT DEFAULT 0,
    demandes_en_attente INT DEFAULT 0,
    demandes_en_cours INT DEFAULT 0,
    temps_moyen_traitement DECIMAL(10,2) DEFAULT 0.00, -- en heures
    data_json JSON DEFAULT NULL, -- Pour stocker des données JSON plus détaillées si nécessaire
    UNIQUE(periode, date_rapport)
);
