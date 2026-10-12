<?php
session_start();

// Fonction pour vérifier si l'utilisateur est connecté
function estConnecte() {
    return isset($_SESSION['user_id']);
}

// Fonction pour vérifier le rôle de l'utilisateur
function estRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

// Redirige vers la page de connexion si non connecté
function verifierConnexion() {
    if (!estConnecte()) {
        header('Location: login.php');
        exit();
    }
}

// Redirige vers le tableau de bord si déjà connecté
function redirigerSiConnecte() {
    if (estConnecte()) {
        header('Location: dashboard.php');
        exit();
    }
}

// Récupérer l'objet utilisateur actuel de la session
function getUtilisateurActuel($pdo) {
    if (estConnecte()) {
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];

        $sql = "SELECT * FROM utilisateurs WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            switch ($userRole) {
                case 'administrateur':
                    return new Administrateur($pdo, $userData);
                case 'responsable':
                    return new Responsable($pdo, $userData);
                case 'technicien':
                    return new Technicien($pdo, $userData);
                case 'utilisateur':
                    $user = new Utilisateur($pdo);
                    $user->setId($userData['id']);
                    $user->setNom($userData['nom']);
                    $user->setPrenom($userData['prenom']);
                    $user->setEmail($userData['email']);
                    $user->setRole($userData['role']);
                    return $user;
                default:
                    return null;
            }
        }
    }
    return null;
}
?>