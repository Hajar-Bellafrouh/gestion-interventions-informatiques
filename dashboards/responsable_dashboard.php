<?php
// Assurez-vous que $utilisateur_actuel est une instance de Responsable
if (!($utilisateur_actuel instanceof Responsable)) {
    echo "<p class='error-message'>Accès non autorisé pour ce rôle.</p>";
    exit();
}

$message = '';

// Gérer la validation/rejet de demande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $demande_id = (int)$_POST['demande_id'];
    if ($_POST['action'] === 'valider_demande') {
        if ($utilisateur_actuel->validerDemande($demande_id, true)) {
            $message = "Demande #{$demande_id} validée avec succès.";
        } else {
            $message = "Erreur lors de la validation de la demande #{$demande_id}.";
        }
    } elseif ($_POST['action'] === 'rejeter_demande') {
        if ($utilisateur_actuel->validerDemande($demande_id, false)) {
            $message = "Demande #{$demande_id} rejetée avec succès.";
        } else {
            $message = "Erreur lors du rejet de la demande #{$demande_id}.";
        }
    } elseif ($_POST['action'] === 'attribuer_intervention') {
        $technicien_id = (int)$_POST['technicien_id'];
        $date_planifiee = sanitize_input($_POST['date_planifiee']);
        if ($utilisateur_actuel->attribuerIntervention($demande_id, $technicien_id, $date_planifiee)) {
            $message = "Intervention attribuée pour la demande #{$demande_id} avec succès.";
        } else {
            $message = "Erreur lors de l'attribution de l'intervention pour la demande #{$demande_id}. La demande doit être validée.";
        }
    } elseif ($_POST['action'] === 'planifier_intervention') {
        $intervention_id = (int)$_POST['intervention_id'];
        $nouvelle_date = sanitize_input($_POST['nouvelle_date']);
        if ($utilisateur_actuel->planifierIntervention($intervention_id, $nouvelle_date)) {
            $message = "Intervention #{$intervention_id} replanifiée avec succès.";
        } else {
            $message = "Erreur lors de la replanification de l'intervention #{$intervention_id}.";
        }
    } elseif ($_POST['action'] === 'marquer_lues_resp') {
        if ($utilisateur_actuel->marquerNotificationsCommeLues()) {
            $message = "Toutes les notifications ont été marquées comme lues.";
        } else {
            $message = "Erreur lors du marquage des notifications.";
        }
        header('Location: dashboard.php?page=notifications');
        exit();
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
    header('Location: dashboard.php?page=notifications');
    exit();
}

?>

<?php if ($page === 'accueil'): ?>
    <div class="card">
        <h2>Bienvenue, Responsable <?php echo htmlspecialchars($utilisateur_actuel->getPrenom()); ?> !</h2>
        <p>Gérez les demandes, planifiez les interventions et suivez les statistiques.</p>
    </div>
<?php endif; ?>

<?php if ($page === 'demandes_en_attente'): ?>
    <div class="card">
        <h2>Demandes d'intervention en attente de validation</h2>
        <?php if ($message): ?>
            <?php display_message($message); ?>
        <?php endif; ?>
        <?php
        $demandes_en_attente = $utilisateur_actuel->getDemandesEnAttente();
        if ($demandes_en_attente):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Demandeur</th>
                        <th>Priorité</th>
                        <th>Date Création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demandes_en_attente as $demande): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($demande['id']); ?></td>
                            <td><?php echo htmlspecialchars($demande['titre']); ?></td>
                            <td><?php echo htmlspecialchars($demande['demandeur_prenom'] . ' ' . $demande['demandeur_nom']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($demande['priorite'])); ?></td>
                            <td><?php echo format_date($demande['date_creation']); ?></td>
                            <td class="actions-cell">
                                <form action="dashboard.php?page=demandes_en_attente" method="POST" style="display:inline;">
                                    <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                                    <input type="hidden" name="action" value="valider_demande">
                                    <button type="submit" class="button small-button success-button">Valider</button>
                                </form>
                                <form action="dashboard.php?page=demandes_en_attente" method="POST" style="display:inline;">
                                    <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                                    <input type="hidden" name="action" value="rejeter_demande">
                                    <button type="submit" class="button small-button danger-button">Rejeter</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune demande en attente de validation.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($page === 'planification'): ?>
    <div class="card">
        <h2>Planification des interventions</h2>
        <?php if ($message): ?>
            <?php display_message($message); ?>
        <?php endif; ?>

        <h3>Attribuer une nouvelle intervention</h3>
        <?php
        $demandes_validees = $pdo->query("SELECT id, titre FROM demandes WHERE statut = 'validee' AND id NOT IN (SELECT demande_id FROM interventions)")->fetchAll(PDO::FETCH_ASSOC);
        $techniciens = $utilisateur_actuel->getTechniciens();
        if ($demandes_validees && $techniciens):
        ?>
            <form action="dashboard.php?page=planification" method="POST">
                <input type="hidden" name="action" value="attribuer_intervention">
                <div class="form-group">
                    <label for="demande_id">Demande :</label>
                    <select id="demande_id" name="demande_id" required>
                        <?php foreach ($demandes_validees as $demande): ?>
                            <option value="<?php echo htmlspecialchars($demande['id']); ?>"><?php echo htmlspecialchars($demande['titre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="technicien_id">Technicien :</label>
                    <select id="technicien_id" name="technicien_id" required>
                        <?php foreach ($techniciens as $tech): ?>
                            <option value="<?php echo htmlspecialchars($tech['id']); ?>"><?php echo htmlspecialchars($tech['prenom'] . ' ' . $tech['nom'] . ' (Niveau ' . $tech['niveau'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_planifiee">Date et heure planifiée :</label>
                    <input type="datetime-local" id="date_planifiee" name="date_planifiee" required>
                </div>
                <button type="submit" class="button">Attribuer</button>
            </form>
        <?php else: ?>
            <p>Aucune demande validée à attribuer ou aucun technicien disponible.</p>
        <?php endif; ?>

        <h3 style="margin-top: 30px;">Replanifier une intervention existante</h3>
        <?php
        $interventions_planifiees = $utilisateur_actuel->getInterventionsPlanifiees();
        if ($interventions_planifiees):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Intervention</th>
                        <th>Demande</th>
                        <th>Technicien</th>
                        <th>Statut</th>
                        <th>Date Planifiée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($interventions_planifiees as $intervention): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($intervention['id']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['titre']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['technicien_prenom'] . ' ' . $intervention['technicien_nom']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $intervention['statut']))); ?></td>
                            <td><?php echo format_date($intervention['date_planifiee']); ?></td>
                            <td class="actions-cell">
                                <form action="dashboard.php?page=planification" method="POST" style="display:inline;">
                                    <input type="hidden" name="intervention_id" value="<?php echo $intervention['id']; ?>">
                                    <input type="hidden" name="action" value="planifier_intervention">
                                    <input type="datetime-local" name="nouvelle_date" value="<?php echo date('Y-m-d\TH:i', strtotime($intervention['date_planifiee'])); ?>" required>
                                    <button type="submit" class="button small-button">Replanifier</button>
                                </form>
                                <a href="fiche_intervention.php?id=<?php echo $intervention['id']; ?>" class="button small-button view-button">Fiche</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune intervention planifiée ou en cours.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($page === 'suivi_interventions'): ?>
    <div class="card">
        <h2>Suivi des interventions</h2>
        <h3>Interventions en cours / planifiées</h3>
        <?php
        $interventions_en_cours = $utilisateur_actuel->getInterventionsPlanifiees(); // Cette méthode récupère aussi les 'en_cours'
        if ($interventions_en_cours):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Demande</th>
                        <th>Technicien</th>
                        <th>Demandeur</th>
                        <th>Statut</th>
                        <th>Date Planifiée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($interventions_en_cours as $intervention): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($intervention['id']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['titre']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['technicien_prenom'] . ' ' . $intervention['technicien_nom']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['demandeur_prenom'] . ' ' . $intervention['demandeur_nom']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $intervention['statut']))); ?></td>
                            <td><?php echo format_date($intervention['date_planifiee']); ?></td>
                            <td class="actions-cell">
                                <a href="fiche_intervention.php?id=<?php echo $intervention['id']; ?>" class="button small-button view-button">Voir Fiche</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune intervention en cours ou planifiée.</p>
        <?php endif; ?>

        <h3 style="margin-top: 30px;">Dernières interventions terminées</h3>
        <?php
        $interventions_terminees = $utilisateur_actuel->getInterventionsTerminees();
        if ($interventions_terminees):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Demande</th>
                        <th>Technicien</th>
                        <th>Demandeur</th>
                        <th>Date Fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($interventions_terminees as $intervention): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($intervention['id']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['titre']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['technicien_prenom'] . ' ' . $intervention['technicien_nom']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['demandeur_prenom'] . ' ' . $intervention['demandeur_nom']); ?></td>
                            <td><?php echo format_date($intervention['date_fin']); ?></td>
                            <td class="actions-cell">
                                <a href="fiche_intervention.php?id=<?php echo $intervention['id']; ?>" class="button small-button view-button">Voir Fiche</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune intervention terminée récemment.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($page === 'statistiques_resp'): ?>
    <div class="card">
        <h2>Statistiques des demandes (Mois actuel)</h2>
        <?php
        $stats = $utilisateur_actuel->consulterStatistiques();
        if ($stats):
        ?>
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>En attente</h3>
                    <p><?php echo htmlspecialchars($stats['en_attente']); ?></p>
                </div>
                <div class="stat-item">
                    <h3>Validées</h3>
                    <p><?php echo htmlspecialchars($stats['validees']); ?></p>
                </div>
                <div class="stat-item">
                    <h3>En cours</h3>
                    <p><?php echo htmlspecialchars($stats['en_cours']); ?></p>
                </div>
                <div class="stat-item">
                    <h3>Terminées</h3>
                    <p><?php echo htmlspecialchars($stats['terminees']); ?></p>
                </div>
            </div>

            <h3 style="margin-top: 30px;">Graphique des statuts des demandes</h3>
            <canvas id="demandeStatusChart"></canvas>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx = document.getElementById('demandeStatusChart').getContext('2d');
                    var demandeStatusChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['En attente', 'Validées', 'En cours', 'Terminées'],
                            datasets: [{
                                label: 'Statut des Demandes',
                                data: [<?php echo $stats['en_attente']; ?>, <?php echo $stats['validees']; ?>, <?php echo $stats['en_cours']; ?>, <?php echo $stats['terminees']; ?>],
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.8)', // Rouge pour en attente
                                    'rgba(54, 162, 235, 0.8)', // Bleu pour validées
                                    'rgba(255, 206, 86, 0.8)', // Jaune pour en cours
                                    'rgba(75, 192, 192, 0.8)'  // Vert pour terminées
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Répartition des Statuts de Demandes'
                                }
                            }
                        }
                    });
                });
            </script>
        <?php else: ?>
            <p>Aucune donnée statistique disponible pour le mois actuel.</p>
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
                <input type="hidden" name="action" value="marquer_lues_resp">
                <button type="submit" class="button">Marquer tout comme lu</button>
            </form>
        <?php else: ?>
            <p>Vous n'avez aucune nouvelle notification.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>