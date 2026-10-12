<?php
// Assurez-vous que $utilisateur_actuel est une instance de Utilisateur
if (!($utilisateur_actuel instanceof Utilisateur)) {
    echo "<p class='error-message'>Accès non autorisé pour ce rôle.</p>";
    exit();
}

$message = '';

// Gérer la création de demande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'creer_demande') {
    $titre = sanitize_input($_POST['titre']);
    $description = sanitize_input($_POST['description']);
    $priorite = sanitize_input($_POST['priorite']);

    if ($utilisateur_actuel->creerDemande($titre, $description, $priorite)) {
        $message = "Votre demande a été créée avec succès et est en attente de validation.";
    } else {
        $message = "Erreur lors de la création de la demande.";
    }
}

// Gérer la suppression de notification
if (isset($_GET['action']) && $_GET['action'] === 'supprimer_notification' && isset($_GET['id'])) {
    $notification_id = (int)$_GET['id'];
    if ($utilisateur_actuel->supprimerNotification($notification_id)) {
        $message = "Notification supprimée avec succès.";
    } else {
        $message = "Erreur lors de la suppression de la notification.";
    }
    // Rediriger pour éviter la re-suppression au rafraîchissement
    header('Location: dashboard.php?page=notifications');
    exit();
}

?>

<?php if ($page === 'accueil'): ?>
    <div class="card">
        <h2>Bienvenue, <?php echo htmlspecialchars($utilisateur_actuel->getPrenom()); ?> !</h2>
        <p>Utilisez le menu latéral pour créer et suivre vos demandes d'intervention.</p>
    </div>
<?php endif; ?>

<?php if ($page === 'creer_demande'): ?>
    <div class="card">
        <h2>Créer une nouvelle demande d'intervention</h2>
        <?php if ($message): ?>
            <?php display_message($message); ?>
        <?php endif; ?>
        <form action="dashboard.php?page=creer_demande" method="POST">
            <input type="hidden" name="action" value="creer_demande">
            <div class="form-group">
                <label for="titre">Titre de la demande :</label>
                <input type="text" id="titre" name="titre" required>
            </div>
            <div class="form-group">
                <label for="description">Description détaillée :</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="priorite">Priorité :</label>
                <select id="priorite" name="priorite">
                    <option value="normale">Normale</option>
                    <option value="basse">Basse</option>
                    <option value="haute">Haute</option>
                    <option value="critique">Critique</option>
                </select>
            </div>
            <button type="submit" class="button">Soumettre la demande</button>
        </form>
    </div>
<?php endif; ?>

<?php if ($page === 'mes_demandes'): ?>
    <div class="card">
        <h2>Mes demandes d'intervention</h2>
        <?php
        $mes_demandes = $utilisateur_actuel->getDemandesUtilisateur();
        if ($mes_demandes):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Date Création</th>
                        <th>Date Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mes_demandes as $demande): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($demande['id']); ?></td>
                            <td><?php echo htmlspecialchars($demande['titre']); ?></td>
                            <td><?php echo htmlspecialchars(substr($demande['description'], 0, 50)) . (strlen($demande['description']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($demande['priorite'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $demande['statut']))); ?></td>
                            <td><?php echo format_date($demande['date_creation']); ?></td>
                            <td><?php echo format_date($demande['date_validation']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Vous n'avez pas encore créé de demandes d'intervention.</p>
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
                <input type="hidden" name="action" value="marquer_lues">
                <button type="submit" class="button">Marquer tout comme lu</button>
            </form>
        <?php else: ?>
            <p>Vous n'avez aucune nouvelle notification.</p>
        <?php endif; ?>
    </div>
    <?php
    // Gérer le marquage comme lu
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'marquer_lues') {
        if ($utilisateur_actuel->marquerNotificationsCommeLues()) {
            $message = "Toutes les notifications ont été marquées comme lues.";
        } else {
            $message = "Erreur lors du marquage des notifications.";
        }
        // Rediriger pour rafraîchir la liste
        header('Location: dashboard.php?page=notifications');
        exit();
    }
    ?>
<?php endif; ?>