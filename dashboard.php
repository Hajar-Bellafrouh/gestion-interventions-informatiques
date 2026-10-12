<?php
// Tampon de sortie : permet les header('Location: ...') envoyés par les dashboards
// après l'affichage de l'en-tête HTML (indépendant de la config php.ini).
ob_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

verifierConnexion(); // Redirige si l'utilisateur n'est pas connecté

$utilisateur_actuel = getUtilisateurActuel($pdo);
if (!$utilisateur_actuel) {
    // Si l'objet utilisateur ne peut pas être créé (ex: rôle inconnu), déconnecter
    header('Location: logout.php');
    exit();
}

$page = $_GET['page'] ?? 'accueil'; // Page par défaut

include __DIR__ . '/includes/header.php';

// Inclure le tableau de bord spécifique au rôle
switch ($_SESSION['user_role']) {
    case 'administrateur':
        include __DIR__ . '/dashboards/admin_dashboard.php';
        break;
    case 'responsable':
        include __DIR__ . '/dashboards/responsable_dashboard.php';
        break;
    case 'technicien':
        include __DIR__ . '/dashboards/technicien_dashboard.php';
        break;
    case 'utilisateur':
        include __DIR__ . '/dashboards/utilisateur_dashboard.php';
        break;
    default:
        echo "<p>Rôle non reconnu. Veuillez contacter l'administrateur.</p>";
        break;
}

include __DIR__ . '/includes/footer.php';
?>