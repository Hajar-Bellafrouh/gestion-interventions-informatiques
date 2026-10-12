<aside class="sidebar">
    <nav class="sidebar-nav">
        <ul>
            <li><a href="dashboard.php" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'accueil') ? 'active' : ''; ?>">Accueil</a></li>
            <?php if (estRole('utilisateur')): ?>
                <li><a href="dashboard.php?page=creer_demande" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'creer_demande') ? 'active' : ''; ?>">Créer une demande</a></li>
                <li><a href="dashboard.php?page=mes_demandes" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'mes_demandes') ? 'active' : ''; ?>">Mes demandes</a></li>
                <li><a href="dashboard.php?page=notifications" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'notifications') ? 'active' : ''; ?>">Notifications</a></li>
            <?php endif; ?>

            <?php if (estRole('responsable')): ?>
                <li><a href="dashboard.php?page=demandes_en_attente" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'demandes_en_attente') ? 'active' : ''; ?>">Demandes en attente</a></li>
                <li><a href="dashboard.php?page=planification" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'planification') ? 'active' : ''; ?>">Planification</a></li>
                <li><a href="dashboard.php?page=suivi_interventions" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'suivi_interventions') ? 'active' : ''; ?>">Suivi Interventions</a></li>
                <li><a href="dashboard.php?page=statistiques_resp" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'statistiques_resp') ? 'active' : ''; ?>">Statistiques</a></li>
                <li><a href="dashboard.php?page=notifications" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'notifications') ? 'active' : ''; ?>">Notifications</a></li>
            <?php endif; ?>

            <?php if (estRole('technicien')): ?>
                <li><a href="dashboard.php?page=mes_interventions" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'mes_interventions') ? 'active' : ''; ?>">Mes interventions</a></li>
                <?php if ($_SESSION['user_niveau'] >= 2): ?>
                    <li><a href="dashboard.php?page=inventaire_partiel" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'inventaire_partiel') ? 'active' : ''; ?>">Inventaire Partiel</a></li>
                <?php endif; ?>
                <li><a href="dashboard.php?page=notifications" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'notifications') ? 'active' : ''; ?>">Notifications</a></li>
            <?php endif; ?>

            <?php if (estRole('administrateur')): ?>
                <li><a href="dashboard.php?page=gestion_utilisateurs" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'gestion_utilisateurs') ? 'active' : ''; ?>">Gestion Utilisateurs</a></li>
                <li><a href="dashboard.php?page=supervision_interventions" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'supervision_interventions') ? 'active' : ''; ?>">Supervision Interventions</a></li>
                <li><a href="dashboard.php?page=gestion_equipements" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'gestion_equipements') ? 'active' : ''; ?>">Gestion Équipements</a></li>
                <li><a href="dashboard.php?page=statistiques_admin" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'statistiques_admin') ? 'active' : ''; ?>">Statistiques & Exports</a></li>
                <li><a href="dashboard.php?page=configuration" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'configuration') ? 'active' : ''; ?>">Configuration</a></li>
                <li><a href="dashboard.php?page=notifications" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'notifications') ? 'active' : ''; ?>">Notifications</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>