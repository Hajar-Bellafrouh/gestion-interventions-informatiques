<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Interventions OCP - <?php echo ucfirst($_SESSION['user_role'] ?? 'Connexion'); ?></title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="wrapper">
      <header class="main-header">
      <a href="dashboard.php" class="logo-container">
          <!-- Chemin vers votre logo dans le dossier img/ -->
          <img src="img/logo-header.png" alt="OCP Logo" class="header-logo">
          <h1>OCP Interventions</h1>
      </a>
      <nav class="main-nav">
          <ul>
              <?php if (estConnecte()): ?>
                  <li><a href="logout.php">Déconnexion</a></li>
              <?php else: ?>
                  <li><a href="login.php">Connexion</a></li>
              <?php endif; ?>
          </ul>
      </nav>
  </header>
      <div class="content-area">
          <?php if (estConnecte()): ?>
              <?php include __DIR__ . '/sidebar.php'; ?>
          <?php endif; ?>
          <main class="main-content">
              <div class="page-header">
                  <h1>Tableau de Bord <?php echo ucfirst($_SESSION['user_role'] ?? ''); ?></h1>
              </div>
              <div class="page-content">
