<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

redirigerSiConnecte(); // Redirige si l'utilisateur est déjà connecté

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    $utilisateur = Utilisateur::authentifier($email, $password, $pdo);

    if ($utilisateur) {
        $_SESSION['user_id'] = $utilisateur->getId();
        $_SESSION['user_nom'] = $utilisateur->getNom();
        $_SESSION['user_prenom'] = $utilisateur->getPrenom();
        $_SESSION['user_email'] = $utilisateur->getEmail();
        $_SESSION['user_role'] = $utilisateur->getRole();
        if ($utilisateur instanceof Technicien) {
            $_SESSION['user_niveau'] = $utilisateur->getNiveau();
        }
        header('Location: dashboard.php');
        exit();
    } else {
        $message = "Email ou mot de passe incorrect, ou compte inactif.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des Interventions OCP</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .back-arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            border-radius: 50%;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 100;
        }
        .back-arrow:hover {
            background-color: #e0e0e0;
        }
        .back-arrow svg {
            width: 16px;
            height: 16px;
        }
    </style>
</head>
<body>
    <!-- Flèche SVG intégrée -->
    <a href="index.php" class="back-arrow" title="Retour à l'accueil">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 18l-6-6 6-6"/>
        </svg>
    </a>
    
    <div class="login-page-wrapper">
        <div class="login-container">
            <div class="login-logo">
                <!-- Chemin vers votre logo dans le dossier img/ -->
                <img src="img/ocp.svg" alt="OCP Logo">
            </div>
            <h2>Connexion</h2>
            <?php if ($message): ?>
                <div class="message error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="button">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>