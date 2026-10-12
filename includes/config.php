<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_interventions_ocp');
define('DB_USER', 'root');
define('DB_PASS', '');

// Inclure la classe de base de données
require_once __DIR__ . '/../classes/Database.php';

// Initialiser la connexion à la base de données
$database = new Database();
$pdo = $database->getConnection();

// Inclure toutes les classes métier
require_once __DIR__ . '/../classes/Utilisateur.php';
require_once __DIR__ . '/../classes/Administrateur.php';
require_once __DIR__ . '/../classes/Responsable.php';
require_once __DIR__ . '/../classes/Technicien.php';
require_once __DIR__ . '/../classes/Demande.php';
require_once __DIR__ . '/../classes/Intervention.php';
require_once __DIR__ . '/../classes/Equipement.php';
require_once __DIR__ . '/../classes/Statistique.php';
?>