<?php
// Assurez-vous que $utilisateur_actuel est une instance de Technicien
if (!($utilisateur_actuel instanceof Technicien)) {
    echo "<p class='error-message'>Accès non autorisé pour ce rôle.</p>";
    exit();
}

$message = '';

// Gérer la mise à jour du statut ou l'ajout de commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $intervention_id = (int)$_POST['intervention_id'];
    if ($_POST['action'] === 'update_statut') {
        $statut = sanitize_input($_POST['statut']);
        if ($utilisateur_actuel->mettreAJourStatut($intervention_id, $statut)) {
            $message = "Statut de l'intervention #{$intervention_id} mis à jour à " . ucfirst(str_replace('_', ' ', $statut)) . ".";
        } else {
            $message = "Erreur lors de la mise à jour du statut de l'intervention #{$intervention_id}.";
        }
    } elseif ($_POST['action'] === 'add_commentaire') {
        $commentaire = sanitize_input($_POST['commentaire']);
        if ($utilisateur_actuel->ajouterCommentaire($intervention_id, $commentaire)) {
            $message = "Commentaire ajouté à l'intervention #{$intervention_id}.";
        } else {
            $message = "Erreur lors de l'ajout du commentaire à l'intervention #{$intervention_id}.";
        }
    }
}

// Gérer la suppression de notification (pour tous les rôles)
if (isset($_GET['action']) && $_GET['action'] === 'supprimer_notification' && isset($_GET['id'])) {
    $notification_id = (int)$_GET['id'];
    if ($utilisateur_actuel->supprimerNotification($notification_id)) {
        $message = "Notification supprimée avec succès.";
    } else {
        $message = "Erreur lors de la suppression de la notification.";
    }
    header('Location: dashboard.php?page=notifications');
    exit();
}

?>

<?php if ($page === 'accueil'): ?>
    <div class="card">
        <h2>Bienvenue, Technicien <?php echo htmlspecialchars($utilisateur_actuel->getPrenom()); ?> (Niveau <?php echo htmlspecialchars($utilisateur_actuel->getNiveau()); ?>) !</h2>
        <p>Consultez vos interventions assignées et mettez à jour leur statut.</p>
    </div>
<?php endif; ?>

<?php if ($page === 'mes_interventions'): ?>
    <div class="card">
        <h2>Mes interventions</h2>
        <?php if ($message): ?>
            <?php display_message($message); ?>
        <?php endif; ?>
        <?php
        $mes_interventions = $utilisateur_actuel->consulterInterventions();
        if ($mes_interventions):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Interv.</th>
                        <th>Demande</th>
                        <th>Priorité</th>
                        <th>Statut Interv.</th>
                        <th>Date Planifiée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mes_interventions as $intervention): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($intervention['id']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['titre']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($intervention['priorite'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $intervention['statut']))); ?></td>
                            <td><?php echo format_date($intervention['date_planifiee']); ?></td>
                            <td class="actions-cell">
                                <a href="fiche_intervention.php?id=<?php echo $intervention['id']; ?>" class="button small-button view-button">Fiche</a>
                                <form action="dashboard.php?page=mes_interventions" method="POST" style="display:inline;">
                                    <input type="hidden" name="intervention_id" value="<?php echo $intervention['id']; ?>">
                                    <input type="hidden" name="action" value="update_statut">
                                    <select name="statut" onchange="this.form.submit()">
                                        <option value="planifiee" <?php echo $intervention['statut'] == 'planifiee' ? 'selected' : ''; ?>>Planifiée</option>
                                        <option value="en_cours" <?php echo $intervention['statut'] == 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                                        <option value="en_attente_pieces" <?php echo $intervention['statut'] == 'en_attente_pieces' ? 'selected' : ''; ?>>En attente de pièces</option>
                                        <option value="terminee" <?php echo $intervention['statut'] == 'terminee' ? 'selected' : ''; ?>>Terminée</option>
                                    </select>
                                </form>
                                <form action="dashboard.php?page=mes_interventions" method="POST" style="display:inline; margin-top: 10px;">
                                    <input type="hidden" name="intervention_id" value="<?php echo $intervention['id']; ?>">
                                    <input type="hidden" name="action" value="add_commentaire">
                                    <textarea name="commentaire" placeholder="Ajouter un commentaire..." rows="1" style="width: 150px; vertical-align: middle;"></textarea>
                                    <button type="submit" class="button small-button">Ajouter</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune intervention ne vous est assignée pour le moment.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($page === 'inventaire_partiel' && $utilisateur_actuel->getNiveau() >= 2): ?>
    <div class="card">
        <h2>Inventaire des équipements liés</h2>
        <?php
        $equipements_lies = $utilisateur_actuel->gererInventairePartiel();
        if ($equipements_lies):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Localisation</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipements_lies as $equipement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($equipement['id']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['nom']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['type']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['localisation']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($equipement['statut'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun équipement lié à vos interventions n'est disponible.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($page === 'notifications'): ?>
    <div class="card">
        <h2>Mes Notifications</h2>
        <?php if ($message): ?>
            <?php display_message($message); ?>
        <?php endif; ?>
        <?php
        $notifications = $utilisateur_actuel->getNotificationsNonLues();
        if ($notifications):
        ?>
            <div class="notifications-list">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item <?php echo $notif['lue'] ? 'read' : 'unread'; ?>">
                        <p><?php echo htmlspecialchars($notif['message']); ?></p>
                        <span class="notification-date"><?php echo format_date($notif['date_creation']); ?></span>
                        <a href="dashboard.php?page=notifications&action=supprimer_notification&id=<?php echo $notif['id']; ?>" class="button small-button delete-button">Supprimer</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="dashboard.php?page=notifications" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="action" value="marquer_lues_tech">
                <button type="submit" class="button">Marquer tout comme lu</button>
            </form>
        <?php else: ?>
            <p>Vous n'avez aucune nouvelle notification.</p>
        <?php endif; ?>
    </div>
    <?php
    // Gérer le marquage comme lu
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'marquer_lues_tech') {
        if ($utilisateur_actuel->marquerNotificationsCommeLues()) {
            $message = "Toutes les notifications ont été marquées comme lues.";
        } else {
            $message = "Erreur lors du marquage des notifications.";
        }
        header('Location: dashboard.php?page=notifications');
        exit();
    }
    ?>
<?php endif; ?>