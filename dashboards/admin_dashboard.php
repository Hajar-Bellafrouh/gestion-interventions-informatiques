<?php
// Assurez-vous que $utilisateur_actuel est une instance de Administrateur
if (!($utilisateur_actuel instanceof Administrateur)) {
    echo "<p class='error-message'>Accès non autorisé pour ce rôle.</p>";
    exit();
}

$message = '';

// Gérer les actions CRUD sur les utilisateurs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'ajouter_utilisateur') {
        $nom = sanitize_input($_POST['nom']);
        $prenom = sanitize_input($_POST['prenom']);
        $email = sanitize_input($_POST['email']);
        $motDePasse = $_POST['motDePasse'];
        $role = sanitize_input($_POST['role']);
        $niveau = isset($_POST['niveau']) && $_POST['niveau'] !== '' ? (int)$_POST['niveau'] : null;

        if ($utilisateur_actuel->ajouterUtilisateur($nom, $prenom, $email, $motDePasse, $role, $niveau)) {
            $message = "Utilisateur ajouté avec succès.";
        } else {
            $message = "Erreur lors de l'ajout de l'utilisateur. L'email existe peut-être déjà.";
        }
    } elseif ($_POST['action'] === 'modifier_utilisateur') {
        $id = (int)$_POST['user_id'];
        $nom = sanitize_input($_POST['nom']);
        $prenom = sanitize_input($_POST['prenom']);
        $email = sanitize_input($_POST['email']);
        $role = sanitize_input($_POST['role']);
        $niveau = isset($_POST['niveau']) && $_POST['niveau'] !== '' ? (int)$_POST['niveau'] : null;
        $actif = isset($_POST['actif']) ? 1 : 0;

        if ($utilisateur_actuel->modifierUtilisateur($id, $nom, $prenom, $email, $role, $niveau, $actif)) {
            $message = "Utilisateur #{$id} modifié avec succès.";
        } else {
            $message = "Erreur lors de la modification de l'utilisateur #{$id}.";
        }
    } elseif ($_POST['action'] === 'supprimer_utilisateur') {
        $id = (int)$_POST['user_id'];
        if ($utilisateur_actuel->supprimerUtilisateur($id)) {
            $message = "Utilisateur #{$id} supprimé avec succès.";
        } else {
            $message = "Erreur lors de la suppression de l'utilisateur #{$id}.";
        }
    } elseif ($_POST['action'] === 'ajouter_equipement') {
        $nom = sanitize_input($_POST['nom']);
        $type = sanitize_input($_POST['type']);
        $localisation = sanitize_input($_POST['localisation']);
        $equipement = new Equipement($pdo);
        if ($equipement->ajouter($nom, $type, $localisation)) {
            $message = "Équipement ajouté avec succès.";
        } else {
            $message = "Erreur lors de l'ajout de l'équipement.";
        }
    } elseif ($_POST['action'] === 'modifier_equipement') {
        $id = (int)$_POST['equipement_id'];
        $nom = sanitize_input($_POST['nom']);
        $type = sanitize_input($_POST['type']);
        $localisation = sanitize_input($_POST['localisation']);
        $statut = sanitize_input($_POST['statut']);
        $equipement = new Equipement($pdo);
        if ($equipement->modifier($id, $nom, $type, $localisation, $statut)) {
            $message = "Équipement #{$id} modifié avec succès.";
        } else {
            $message = "Erreur lors de la modification de l'équipement #{$id}.";
        }
    } elseif ($_POST['action'] === 'supprimer_equipement') {
        $id = (int)$_POST['equipement_id'];
        $equipement = new Equipement($pdo);
        if ($equipement->supprimer($id)) {
            $message = "Équipement #{$id} supprimé avec succès.";
        } else {
            $message = "Erreur lors de la suppression de l'équipement #{$id}.";
        }
    } elseif ($_POST['action'] === 'exporter_stats') {
        $format = sanitize_input($_POST['format']);
        $utilisateur_actuel->exporterStatistiques($format); // Cette fonction gère la sortie et l'exit
    } elseif ($_POST['action'] === 'marquer_lues_admin') {
        if ($utilisateur_actuel->marquerNotificationsCommeLues()) {
            $message = "Toutes les notifications ont été marquées comme lues.";
        } else {
            $message = "Erreur lors du marquage des notifications.";
        }
        header('Location: dashboard.php?page=notifications');
        exit();
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
        <h2>Bienvenue, Administrateur <?php echo htmlspecialchars($utilisateur_actuel->getPrenom()); ?> !</h2>
        <p>Gérez l'ensemble de l'application : utilisateurs, interventions, équipements et statistiques.</p>
    </div>
<?php endif; ?>

<?php if ($page === 'gestion_utilisateurs'): ?>
    <div class="card">
        <h2>Gestion des utilisateurs</h2>
        <?php if ($message): ?>
            <?php display_message($message); ?>
        <?php endif; ?>

        <h3>Ajouter un nouvel utilisateur</h3>
        <form action="dashboard.php?page=gestion_utilisateurs" method="POST">
            <input type="hidden" name="action" value="ajouter_utilisateur">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="motDePasse">Mot de passe :</label>
                <input type="password" id="motDePasse" name="motDePasse" required>
            </div>
            <div class="form-group">
                <label for="role">Rôle :</label>
                <select id="role" name="role" onchange="toggleNiveauField(this.value)" required>
                    <option value="utilisateur">Utilisateur</option>
                    <option value="technicien">Technicien</option>
                    <option value="responsable">Responsable</option>
                    <option value="administrateur">Administrateur</option>
                </select>
            </div>
            <div class="form-group" id="niveau-group" style="display: none;">
                <label for="niveau">Niveau (pour Technicien) :</label>
                <input type="number" id="niveau" name="niveau" min="1" max="3">
            </div>
            <button type="submit" class="button">Ajouter l'utilisateur</button>
        </form>

        <script>
            function toggleNiveauField(role) {
                const niveauGroup = document.getElementById('niveau-group');
                if (role === 'technicien') {
                    niveauGroup.style.display = 'block';
                    document.getElementById('niveau').setAttribute('required', 'required');
                } else {
                    niveauGroup.style.display = 'none';
                    document.getElementById('niveau').removeAttribute('required');
                    document.getElementById('niveau').value = ''; // Clear value if not technician
                }
            }
            // Initial call in case of page reload with pre-selected role
            document.addEventListener('DOMContentLoaded', () => {
                toggleNiveauField(document.getElementById('role').value);
            });
        </script>

        <h3 style="margin-top: 30px;">Liste des utilisateurs</h3>
        <?php
        $utilisateurs = $utilisateur_actuel->gererUtilisateurs();
        if ($utilisateurs):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Niveau</th>
                        <th>Actif</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['nom']); ?></td>
                            <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                            <td><?php echo htmlspecialchars($user['niveau'] ?? 'N/A'); ?></td>
                            <td><?php echo $user['actif'] ? 'Oui' : 'Non'; ?></td>
                            <td class="actions-cell">
                                <button class="button small-button edit-button" onclick="openEditUserModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">Modifier</button>
                                <form action="dashboard.php?page=gestion_utilisateurs" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                    <input type="hidden" name="action" value="supprimer_utilisateur">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="button small-button danger-button">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun utilisateur enregistré.</p>
        <?php endif; ?>
    </div>

    <!-- Modal de modification d'utilisateur -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditUserModal()">&times;</span>
            <h2>Modifier l'utilisateur</h2>
            <form action="dashboard.php?page=gestion_utilisateurs" method="POST">
                <input type="hidden" name="action" value="modifier_utilisateur">
                <input type="hidden" id="edit-user-id" name="user_id">
                <div class="form-group">
                    <label for="edit-nom">Nom :</label>
                    <input type="text" id="edit-nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="edit-prenom">Prénom :</label>
                    <input type="text" id="edit-prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="edit-email">Email :</label>
                    <input type="email" id="edit-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="edit-role">Rôle :</label>
                    <select id="edit-role" name="role" onchange="toggleNiveauFieldEdit(this.value)" required>
                        <option value="utilisateur">Utilisateur</option>
                        <option value="technicien">Technicien</option>
                        <option value="responsable">Responsable</option>
                        <option value="administrateur">Administrateur</option>
                    </select>
                </div>
                <div class="form-group" id="edit-niveau-group">
                    <label for="edit-niveau">Niveau (pour Technicien) :</label>
                    <input type="number" id="edit-niveau" name="niveau" min="1" max="3">
                </div>
                <div class="form-group">
                    <input type="checkbox" id="edit-actif" name="actif" value="1">
                    <label for="edit-actif">Actif</label>
                </div>
                <button type="submit" class="button">Enregistrer les modifications</button>
            </form>
        </div>
    </div>

    <script>
        const editUserModal = document.getElementById('editUserModal');
        const editNiveauGroup = document.getElementById('edit-niveau-group');

        function openEditUserModal(user) {
            document.getElementById('edit-user-id').value = user.id;
            document.getElementById('edit-nom').value = user.nom;
            document.getElementById('edit-prenom').value = user.prenom;
            document.getElementById('edit-email').value = user.email;
            document.getElementById('edit-role').value = user.role;
            document.getElementById('edit-niveau').value = user.niveau || '';
            document.getElementById('edit-actif').checked = user.actif == 1;

            toggleNiveauFieldEdit(user.role);
            editUserModal.style.display = 'block';
        }

        function closeEditUserModal() {
            editUserModal.style.display = 'none';
        }

        function toggleNiveauFieldEdit(role) {
            if (role === 'technicien') {
                editNiveauGroup.style.display = 'block';
                document.getElementById('edit-niveau').setAttribute('required', 'required');
            } else {
                editNiveauGroup.style.display = 'none';
                document.getElementById('edit-niveau').removeAttribute('required');
                document.getElementById('edit-niveau').value = '';
            }
        }

        window.onclick = function(event) {
            if (event.target == editUserModal) {
                closeEditUserModal();
            }
        }
    </script>
<?php endif; ?>

<?php if ($page === 'supervision_interventions'): ?>
    <div class="card">
        <h2>Supervision globale des interventions</h2>
        <?php
        $all_interventions = $utilisateur_actuel->superviserInterventions();
        if ($all_interventions):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Interv.</th>
                        <th>Demande</th>
                        <th>Demandeur</th>
                        <th>Technicien</th>
                        <th>Statut</th>
                        <th>Date Planifiée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_interventions as $intervention): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($intervention['id']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['titre']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['demandeur_prenom'] . ' ' . $intervention['demandeur_nom']); ?></td>
                            <td><?php echo htmlspecialchars($intervention['technicien_prenom'] . ' ' . $intervention['technicien_nom']); ?></td>
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
            <p>Aucune intervention enregistrée.</p>
        <?php endif; ?>

        <h3 style="margin-top: 30px;">Toutes les demandes</h3>
        <?php
        $all_demandes = $utilisateur_actuel->getAllDemandes();
        if ($all_demandes):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Demande</th>
                        <th>Titre</th>
                        <th>Demandeur</th>
                        <th>Statut</th>
                        <th>Priorité</th>
                        <th>Date Création</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_demandes as $demande): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($demande['id']); ?></td>
                            <td><?php echo htmlspecialchars($demande['titre']); ?></td>
                            <td><?php echo htmlspecialchars($demande['demandeur_prenom'] . ' ' . $demande['demandeur_nom']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $demande['statut']))); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($demande['priorite'])); ?></td>
                            <td><?php echo format_date($demande['date_creation']); ?></td>
                            <td><?php echo htmlspecialchars($demande['responsable_prenom'] ? $demande['responsable_prenom'] . ' ' . $demande['responsable_nom'] : 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune demande enregistrée.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($page === 'gestion_equipements'): ?>
    <div class="card">
        <h2>Gestion de l'inventaire des équipements</h2>
        <?php if ($message): ?>
            <?php display_message($message); ?>
        <?php endif; ?>

        <h3>Ajouter un nouvel équipement</h3>
        <form action="dashboard.php?page=gestion_equipements" method="POST">
            <input type="hidden" name="action" value="ajouter_equipement">
            <div class="form-group">
                <label for="eq_nom">Nom :</label>
                <input type="text" id="eq_nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="eq_type">Type :</label>
                <input type="text" id="eq_type" name="type" required>
            </div>
            <div class="form-group">
                <label for="eq_localisation">Localisation :</label>
                <input type="text" id="eq_localisation" name="localisation" required>
            </div>
            <button type="submit" class="button">Ajouter l'équipement</button>
        </form>

        <h3 style="margin-top: 30px;">Liste des équipements</h3>
        <?php
        $equipements = Equipement::listerTous($pdo);
        if ($equipements):
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Localisation</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipements as $eq): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($eq['id']); ?></td>
                            <td><?php echo htmlspecialchars($eq['nom']); ?></td>
                            <td><?php echo htmlspecialchars($eq['type']); ?></td>
                            <td><?php echo htmlspecialchars($eq['localisation']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($eq['statut'])); ?></td>
                            <td class="actions-cell">
                                <button class="button small-button edit-button" onclick="openEditEquipementModal(<?php echo htmlspecialchars(json_encode($eq)); ?>)">Modifier</button>
                                <form action="dashboard.php?page=gestion_equipements" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet équipement ?');">
                                    <input type="hidden" name="action" value="supprimer_equipement">
                                    <input type="hidden" name="equipement_id" value="<?php echo $eq['id']; ?>">
                                    <button type="submit" class="button small-button danger-button">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun équipement enregistré.</p>
        <?php endif; ?>
    </div>

    <!-- Modal de modification d'équipement -->
    <div id="editEquipementModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditEquipementModal()">&times;</span>
            <h2>Modifier l'équipement</h2>
            <form action="dashboard.php?page=gestion_equipements" method="POST">
                <input type="hidden" name="action" value="modifier_equipement">
                <input type="hidden" id="edit-equipement-id" name="equipement_id">
                <div class="form-group">
                    <label for="edit-eq-nom">Nom :</label>
                    <input type="text" id="edit-eq-nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="edit-eq-type">Type :</label>
                    <input type="text" id="edit-eq-type" name="type" required>
                </div>
                <div class="form-group">
                    <label for="edit-eq-localisation">Localisation :</label>
                    <input type="text" id="edit-eq-localisation" name="localisation" required>
                </div>
                <div class="form-group">
                    <label for="edit-eq-statut">Statut :</label>
                    <select id="edit-eq-statut" name="statut" required>
                        <option value="operationnel">Opérationnel</option>
                        <option value="en_panne">En Panne</option>
                        <option value="en_maintenance">En Maintenance</option>
                        <option value="hors_service">Hors Service</option>
                    </select>
                </div>
                <button type="submit" class="button">Enregistrer les modifications</button>
            </form>
        </div>
    </div>

    <script>
        const editEquipementModal = document.getElementById('editEquipementModal');

        function openEditEquipementModal(equipement) {
            document.getElementById('edit-equipement-id').value = equipement.id;
            document.getElementById('edit-eq-nom').value = equipement.nom;
            document.getElementById('edit-eq-type').value = equipement.type;
            document.getElementById('edit-eq-localisation').value = equipement.localisation;
            document.getElementById('edit-eq-statut').value = equipement.statut;
            editEquipementModal.style.display = 'block';
        }

        function closeEditEquipementModal() {
            editEquipementModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == editEquipementModal) {
                closeEditEquipementModal();
            }
        }
    </script>
<?php endif; ?>

<?php if ($page === 'statistiques_admin'): ?>
    <div class="card">
        <h2>Statistiques et Exports</h2>
        <?php
        $statistique_obj = new Statistique($pdo);
        $stats = $statistique_obj->genererRapport();
        if ($stats):
        ?>
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>Total Demandes</h3>
                    <p><?php echo htmlspecialchars($stats['total_demandes']); ?></p>
                </div>
                <div class="stat-item">
                    <h3>Demandes Terminées</h3>
                    <p><?php echo htmlspecialchars($stats['demandes_terminees']); ?></p>
                </div>
                <div class="stat-item">
                    <h3>Demandes En Attente</h3>
                    <p><?php echo htmlspecialchars($stats['demandes_en_attente']); ?></p>
                </div>
                <div class="stat-item">
                    <h3>Demandes En Cours</h3>
                    <p><?php echo htmlspecialchars($stats['demandes_en_cours']); ?></p>
                </div>
             <div class="stat-item">
    <h3>Temps Moyen Validation (h)</h3>
    <p><?php echo isset($stats['temps_moyen_validation']) ? htmlspecialchars(round((float)$stats['temps_moyen_validation'], 2)) : 'N/A'; ?></p>
</div>
<div class="stat-item">
    <h3>Temps Moyen Résolution (h)</h3>
    <p><?php echo isset($stats['temps_moyen_resolution']) ? htmlspecialchars(round((float)$stats['temps_moyen_resolution'], 2)) : 'N/A'; ?></p>
</div>
            </div>

            <h3 style="margin-top: 30px;">Graphique des statuts des demandes</h3>
            <canvas id="adminDemandeStatusChart"></canvas>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx = document.getElementById('adminDemandeStatusChart').getContext('2d');
                    var adminDemandeStatusChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Total', 'Terminées', 'En Attente', 'En Cours'],
                            datasets: [{
                                label: 'Nombre de Demandes',
                                data: [<?php echo $stats['total_demandes']; ?>, <?php echo $stats['demandes_terminees']; ?>, <?php echo $stats['demandes_en_attente']; ?>, <?php echo $stats['demandes_en_cours']; ?>],
                                backgroundColor: [
                                    'rgba(153, 102, 255, 0.8)', // Violet
                                    'rgba(75, 192, 192, 0.8)',  // Vert
                                    'rgba(255, 99, 132, 0.8)',  // Rouge
                                    'rgba(255, 206, 86, 0.8)'   // Jaune
                                ],
                                borderColor: [
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(255, 206, 86, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: true,
                                    text: 'Statuts des Demandes'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            </script>


 
            <h3 style="margin-top: 30px;">Répartition des Demandes</h3>
<div class="charts-container">
    <div class="chart-wrapper">
        <canvas id="demandeStatusPieChart"></canvas>
    </div>
    <div class="chart-wrapper">
        <canvas id="priorityDistributionChart"></canvas>
    </div>
</div>



<style>
.charts-container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}
.chart-wrapper {
    flex: 1;
    min-width: 300px;
    max-width: 45%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour les graphiques (à adapter avec vos vraies données)
    const statsData = {
        statusDistribution: {
            labels: ['Terminées', 'En Attente', 'En Cours', 'Validées', 'Rejetées'],
            data: [
                <?= $stats['demandes_terminees'] ?? 0 ?>,
                <?= $stats['demandes_en_attente'] ?? 0 ?>,
                <?= $stats['demandes_en_cours'] ?? 0 ?>,
                <?= $stats['demandes_validees'] ?? 0 ?>,
                <?= $stats['demandes_rejetees'] ?? 0 ?>
            ],
            colors: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ]
        },
        priorityDistribution: {
            labels: ['Basse', 'Normale', 'Haute', 'Critique'],
            data: [
                <?= $stats['priorite_basse'] ?? 0 ?>,
                <?= $stats['priorite_normale'] ?? 0 ?>,
                <?= $stats['priorite_haute'] ?? 0 ?>,
                <?= $stats['priorite_critique'] ?? 0 ?>
            ],
            colors: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ]
        }
    };

    // Graphique circulaire - Statut des demandes
    new Chart(
        document.getElementById('demandeStatusPieChart').getContext('2d'),
        {
            type: 'pie',
            data: {
                labels: statsData.statusDistribution.labels,
                datasets: [{
                    data: statsData.statusDistribution.data,
                    backgroundColor: statsData.statusDistribution.colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Par Statut'
                    }
                }
            }
        }
    );

    // Graphique circulaire - Priorité des demandes
    new Chart(
        document.getElementById('priorityDistributionChart').getContext('2d'),
        {
            type: 'doughnut',
            data: {
                labels: statsData.priorityDistribution.labels,
                datasets: [{
                    data: statsData.priorityDistribution.data,
                    backgroundColor: statsData.priorityDistribution.colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Par Priorité'
                    }
                }
            }
        }
    );

    
});
</script>



<h3 style="margin-top: 30px;">Évolution Mensuelle des Demandes</h3>
<canvas id="monthlyTrendChart"></canvas>

<script>
// Récupération des données depuis PHP
const monthlyTrendData = {
    labels: <?php echo json_encode(['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']); ?>,
    datasets: [{
        label: 'Demandes créées',
        data: <?php 
            $stmt = $pdo->query("SELECT MONTH(date_creation) as mois, COUNT(*) as total 
                                FROM demandes 
                                WHERE YEAR(date_creation) = YEAR(CURRENT_DATE)
                                GROUP BY MONTH(date_creation)");
            $data = array_fill(0, 12, 0);
            while ($row = $stmt->fetch()) {
                $data[$row['mois']-1] = $row['total'];
            }
            echo json_encode($data);
        ?>,
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.1)',
        tension: 0.3,
        fill: true
    }, {
        label: 'Demandes résolues',
        data: <?php 
            $stmt = $pdo->query("SELECT MONTH(date_creation) as mois, COUNT(*) as total 
                                FROM demandes 
                                WHERE statut = 'terminee' AND YEAR(date_creation) = YEAR(CURRENT_DATE)
                                GROUP BY MONTH(date_creation)");
            $data = array_fill(0, 12, 0);
            while ($row = $stmt->fetch()) {
                $data[$row['mois']-1] = $row['total'];
            }
            echo json_encode($data);
        ?>,
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.1)',
        tension: 0.3,
        fill: true
    }]
};

new Chart(
    document.getElementById('monthlyTrendChart').getContext('2d'),
    {
        type: 'line',
        data: monthlyTrendData,
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    }
);
</script>

<h3 style="margin-top: 30px;">Compétences des Techniciens</h3>
<canvas id="technicianSkillsChart"></canvas>

<script>
new Chart(
    document.getElementById('technicianSkillsChart').getContext('2d'),
    {
        type: 'radar',
        data: {
            labels: ['Réseaux', 'Systèmes', 'Bases de données', 'Sécurité', 'Support'],
            datasets: [
                <?php
                $techniciens = $pdo->query("SELECT id, CONCAT(prenom, ' ', nom) as nom_complet, niveau 
                                          FROM utilisateurs 
                                          WHERE role = 'technicien'");
                $colors = [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ];
                $i = 0;
                while ($tech = $techniciens->fetch()) {
                    echo "{
                        label: '".$tech['nom_complet']."',
                        data: [".($tech['niveau']*30).", ".($tech['niveau']*25).", ".($tech['niveau']*30).", ".($tech['niveau']*20).", ".($tech['niveau']*35)."],
                        backgroundColor: '".$colors[$i % count($colors)]."',
                        borderColor: '".str_replace('0.2', '1', $colors[$i % count($colors)])."'
                    },";
                    $i++;
                }
                ?>
            ]
        },
        options: {
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
    }
);
</script>

<h3 style="margin-top: 30px;">Répartition des Interventions par Technicien</h3>
<canvas id="technicianWorkloadChart"></canvas>

<script>
new Chart(
    document.getElementById('technicianWorkloadChart').getContext('2d'),
    {
        type: 'bar',
        data: {
            labels: <?php
                $stmt = $pdo->query("SELECT CONCAT(prenom, ' ', nom) as nom_complet 
                                    FROM utilisateurs 
                                    WHERE role = 'technicien'");
                $noms = [];
                while ($row = $stmt->fetch()) {
                    $noms[] = $row['nom_complet'];
                }
                echo json_encode($noms);
            ?>,
            datasets: [{
                label: 'Terminées',
                data: <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as total 
                                        FROM interventions i
                                        JOIN utilisateurs u ON i.technicien_id = u.id
                                        WHERE i.statut = 'terminee'
                                        GROUP BY technicien_id");
                    $data = [];
                    while ($row = $stmt->fetch()) {
                        $data[] = $row['total'];
                    }
                    echo json_encode($data);
                ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.8)'
            }, {
                label: 'En cours',
                data: <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as total 
                                        FROM interventions i
                                        JOIN utilisateurs u ON i.technicien_id = u.id
                                        WHERE i.statut = 'en_cours'
                                        GROUP BY technicien_id");
                    $data = [];
                    while ($row = $stmt->fetch()) {
                        $data[] = $row['total'];
                    }
                    echo json_encode($data);
                ?>,
                backgroundColor: 'rgba(255, 206, 86, 0.8)'
            }, {
                label: 'En retard',
                data: <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as total 
                                        FROM interventions i
                                        JOIN utilisateurs u ON i.technicien_id = u.id
                                        WHERE i.date_fin < NOW() AND i.statut != 'terminee'
                                        GROUP BY technicien_id");
                    $data = [];
                    while ($row = $stmt->fetch()) {
                        $data[] = $row['total'];
                    }
                    echo json_encode($data);
                ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.8)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    }
);
</script>


            <h3 style="margin-top: 30px;">Exporter les données</h3>
            <form action="dashboard.php?page=statistiques_admin" method="POST">
                <input type="hidden" name="action" value="exporter_stats">
                <div class="form-group">
                    <label for="export_format">Format d'export :</label>
                    <select id="export_format" name="format">
                        <option value="csv">CSV (compatible Excel)</option>
                        <option value="text">Texte</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
                <button type="submit" class="button">Exporter</button>
            </form>
        <?php else: ?>
            <p>Aucune donnée statistique disponible.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($page === 'configuration'): ?>
    <div class="card">
        <h2>Configuration de la plateforme</h2>
        <p>Cette section est pour les paramètres système et les notifications.</p>
        <p>Implémentation future : Ici, vous pourriez avoir des formulaires pour modifier des paramètres globaux (ex: seuils SLA, modèles de notification, etc.).</p>
        <?php if ($message): ?>
            <?php display_message($message); ?>
        <?php endif; ?>
        <form action="dashboard.php?page=configuration" method="POST">
            <input type="hidden" name="action" value="configurer_plateforme">
            <div class="form-group">
                <label for="parametre">Paramètre :</label>
                <input type="text" id="parametre" name="parametre" placeholder="Nom du paramètre">
            </div>
            <div class="form-group">
                <label for="valeur">Valeur :</label>
                <input type="text" id="valeur" name="valeur" placeholder="Valeur du paramètre">
            </div>
            <button type="submit" class="button secondary-button">Enregistrer la configuration</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'configurer_plateforme') {
            $param = sanitize_input($_POST['parametre']);
            $val = sanitize_input($_POST['valeur']);
            if ($utilisateur_actuel->configurerPlateforme($param, $val)) {
                $message = "Paramètre '{$param}' mis à jour à '{$val}'. (Implémentation factice)";
            } else {
                $message = "Erreur lors de la configuration.";
            }
            // Afficher le message après le rechargement de la page pour éviter la soumission multiple
            echo "<script>window.location.href = 'dashboard.php?page=configuration&msg=" . urlencode($message) . "';</script>";
            exit();
        }
        if (isset($_GET['msg'])) {
            display_message(htmlspecialchars($_GET['msg']));
        }
        ?>
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
                <input type="hidden" name="action" value="marquer_lues_admin">
                <button type="submit" class="button">Marquer tout comme lu</button>
            </form>
        <?php else: ?>
            <p>Vous n'avez aucune nouvelle notification.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>