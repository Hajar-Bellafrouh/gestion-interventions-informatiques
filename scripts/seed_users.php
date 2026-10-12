<?php
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Utilisateur.php';
require_once __DIR__ . '/../classes/Administrateur.php';
require_once __DIR__ . '/../classes/Responsable.php';
require_once __DIR__ . '/../classes/Technicien.php';

$database = new Database();
$db = $database->getConnection();

echo "Début du script de seeding...\n";

// Supprimer les utilisateurs existants pour un re-seeding propre (optionnel)
// $db->exec("DELETE FROM utilisateurs");
// echo "Utilisateurs existants supprimés.\n";

$users_to_seed = [
    ['nom' => 'Admin', 'prenom' => 'Super', 'email' => 'admin@ocp.ma', 'motDePasse' => 'adminpass', 'role' => 'administrateur', 'niveau' => null],
    ['nom' => 'Responsable', 'prenom' => 'Chef', 'email' => 'responsable@ocp.ma', 'motDePasse' => 'resppass', 'role' => 'responsable', 'niveau' => null],
    ['nom' => 'Technicien1', 'prenom' => 'Niveau1', 'email' => 'tech1@ocp.ma', 'motDePasse' => 'tech1pass', 'role' => 'technicien', 'niveau' => 1],
    ['nom' => 'Technicien2', 'prenom' => 'Niveau2', 'email' => 'tech2@ocp.ma', 'motDePasse' => 'tech2pass', 'role' => 'technicien', 'niveau' => 2],
    ['nom' => 'Technicien3', 'prenom' => 'Niveau3', 'email' => 'tech3@ocp.ma', 'motDePasse' => 'tech3pass', 'role' => 'technicien', 'niveau' => 3],
    ['nom' => 'Utilisateur', 'prenom' => 'Simple', 'email' => 'user@ocp.ma', 'motDePasse' => 'userpass', 'role' => 'utilisateur', 'niveau' => null],
    ['nom' => 'Utilisateur2', 'prenom' => 'Simple', 'email' => 'user2@ocp.ma', 'motDePasse' => 'user2pass', 'role' => 'utilisateur', 'niveau' => null],
    ['nom' => 'Utilisateur3', 'prenom' => 'Simple', 'email' => 'user3@ocp.ma', 'motDePasse' => 'user3pass', 'role' => 'utilisateur', 'niveau' => null],
];

foreach ($users_to_seed as $userData) {
    // Vérifier si l'utilisateur existe déjà
    $stmt = $db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
    $stmt->execute([':email' => $userData['email']]);
    if ($stmt->fetchColumn() > 0) {
        echo "L'utilisateur {$userData['email']} existe déjà. Saut.\n";
        continue;
    }

    $utilisateur = new Utilisateur($db);
    $utilisateur->setNom($userData['nom']);
    $utilisateur->setPrenom($userData['prenom']);
    $utilisateur->setEmail($userData['email']);
    $utilisateur->setMotDePasse($userData['motDePasse']); // Cette méthode hache le mot de passe
    $utilisateur->setRole($userData['role']);

    // Pour les techniciens, définir le niveau
    if ($userData['role'] === 'technicien' && $userData['niveau'] !== null) {
        $stmt = $db->prepare("INSERT INTO utilisateurs (nom, prenom, email, motDePasse, role, niveau) VALUES (:nom, :prenom, :email, :motDePasse, :role, :niveau)");
        $success = $stmt->execute([
            ':nom' => $userData['nom'],
            ':prenom' => $userData['prenom'],
            ':email' => $userData['email'],
            ':motDePasse' => password_hash($userData['motDePasse'], PASSWORD_DEFAULT),
            ':role' => $userData['role'],
            ':niveau' => $userData['niveau']
        ]);
    } else {
        $success = $utilisateur->sauvegarder();
    }

    if ($success) {
        echo "Utilisateur {$userData['email']} ajouté avec succès.\n";
    } else {
        echo "Échec de l'ajout de l'utilisateur {$userData['email']}.\n";
    }
}

echo "Script de seeding terminé.\n";
?>