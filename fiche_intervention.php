<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

verifierConnexion();

$intervention_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$fiche_intervention = null;

if ($intervention_id > 0) {
    $intervention = new Intervention($pdo);
    $intervention->setId($intervention_id);
    $fiche_intervention = $intervention->genererFiche();
}

include __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h2>Fiche d'Intervention #<?php echo htmlspecialchars($intervention_id); ?></h2>
    <?php if ($fiche_intervention): ?>
        <div class="fiche-details">
            <p><strong>Titre de la demande :</strong> <?php echo htmlspecialchars($fiche_intervention['titre']); ?></p>
            <p><strong>Description de la demande :</strong> <?php echo nl2br(htmlspecialchars($fiche_intervention['description'])); ?></p>
            <p><strong>Priorité :</strong> <?php echo htmlspecialchars(ucfirst($fiche_intervention['priorite'])); ?></p>
            <p><strong>Statut de la demande :</strong> <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $fiche_intervention['demande_statut']))); ?></p>
            <p><strong>Technicien Assigné :</strong> <?php echo htmlspecialchars($fiche_intervention['technicien_prenom'] . ' ' . $fiche_intervention['technicien_nom']); ?></p>
            <p><strong>Demandeur :</strong> <?php echo htmlspecialchars($fiche_intervention['demandeur_prenom'] . ' ' . $fiche_intervention['demandeur_nom']); ?></p>
            <p><strong>Date Planifiée :</strong> <?php echo format_date($fiche_intervention['date_planifiee']); ?></p>
            <p><strong>Statut de l'Intervention :</strong> <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $fiche_intervention['statut']))); ?></p>
            <p><strong>Date Début :</strong> <?php echo format_date($fiche_intervention['date_debut']); ?></p>
            <p><strong>Date Fin :</strong> <?php echo format_date($fiche_intervention['date_fin']); ?></p>
            <h3>Rapport d'Intervention :</h3>
            <div class="rapport-content">
                <?php echo nl2br(htmlspecialchars($fiche_intervention['rapport'] ?? 'Aucun rapport disponible.')); ?>
            </div>
        </div>
        <div class="actions">
            <a href="javascript:history.back()" class="button secondary-button">Retour</a>
            <!-- Vous pouvez ajouter un bouton d'impression ici si nécessaire -->
            <button onclick="window.print()" class="button print-button">Imprimer la fiche</button>
        </div>
    <?php else: ?>
        <p class="error-message">Fiche d'intervention introuvable ou accès non autorisé.</p>
        <a href="dashboard.php" class="button secondary-button">Retour au tableau de bord</a>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>