-- =====================================================================
-- Données d'exemple (à exécuter APRÈS schema.sql ET scripts/seed_users.php)
-- Les identifiants sont retrouvés par email / titre : aucune dépendance
-- à des ids fixes. À exécuter une seule fois.
-- =====================================================================
USE gestion_interventions_ocp;

SET @resp := (SELECT id FROM utilisateurs WHERE email = 'responsable@ocp.ma');
SET @t1   := (SELECT id FROM utilisateurs WHERE email = 'tech1@ocp.ma');
SET @t2   := (SELECT id FROM utilisateurs WHERE email = 'tech2@ocp.ma');
SET @t3   := (SELECT id FROM utilisateurs WHERE email = 'tech3@ocp.ma');
SET @u1   := (SELECT id FROM utilisateurs WHERE email = 'user@ocp.ma');
SET @u2   := (SELECT id FROM utilisateurs WHERE email = 'user2@ocp.ma');
SET @u3   := (SELECT id FROM utilisateurs WHERE email = 'user3@ocp.ma');

INSERT INTO demandes (titre, description, priorite, utilisateur_id, statut, date_creation, responsable_id)
VALUES 
('Clavier défectueux', 'Touches du clavier ne répondent plus', 'normale', @u1, 'terminee', '2025-06-15 09:00:00', @resp),
('Souris sans fil', 'Problème de connexion de la souris', 'basse', @u2, 'terminee', '2025-06-18 10:30:00', @resp),
('Accès VPN', 'Impossible de se connecter au VPN', 'haute', @u3, 'terminee', '2025-06-20 14:15:00', @resp),
('Ecran cassé', 'Ecran LCD fissuré', 'critique', @u1, 'terminee', '2025-07-05 11:20:00', @resp),
('Logiciel CRM', 'Problème d\'accès au logiciel CRM', 'haute', @u2, 'en_cours', '2025-07-10 16:45:00', @resp),
('Imprimante bloquée', 'Imprimante bloque après 10 pages', 'normale', @u3, 'en_attente', '2025-07-12 09:30:00', @resp),
('Wifi lent', 'Connexion Wifi très lente au 3ème étage', 'haute', @u1, 'en_attente_technicien', '2025-07-15 13:15:00', @resp),
('Scanner défectueux', 'Le scanner ne détecte pas les documents', 'normale', @u2, 'validee', '2025-07-18 10:00:00', @resp);

-- Interventions pour ces demandes
INSERT INTO interventions (demande_id, technicien_id, date_planifiee, statut, rapport, date_debut, date_fin)
VALUES 
((SELECT id FROM demandes WHERE titre = 'Clavier défectueux' LIMIT 1), @t1, '2025-06-15 10:00:00', 'terminee', 'Clavier remplacé', '2025-06-15 10:05:00', '2025-06-15 10:20:00'),
((SELECT id FROM demandes WHERE titre = 'Souris sans fil' LIMIT 1), @t2, '2025-06-18 11:00:00', 'terminee', 'Souris remplacée', '2025-06-18 11:10:00', '2025-06-18 11:25:00'),
((SELECT id FROM demandes WHERE titre = 'Accès VPN' LIMIT 1), @t3, '2025-06-20 15:00:00', 'terminee', 'Configuration VPN mise à jour', '2025-06-20 15:05:00', '2025-06-20 15:45:00'),
((SELECT id FROM demandes WHERE titre = 'Ecran cassé' LIMIT 1), @t1, '2025-07-05 13:00:00', 'terminee', 'Ecran remplacé', '2025-07-05 13:10:00', '2025-07-05 13:50:00'),
((SELECT id FROM demandes WHERE titre = 'Logiciel CRM' LIMIT 1), @t2, '2025-07-10 17:00:00', 'en_cours', 'En attente de réponse du fournisseur', '2025-07-10 17:15:00', NULL),
((SELECT id FROM demandes WHERE titre = 'Scanner défectueux' LIMIT 1), @t3, '2025-07-18 11:00:00', 'planifiee', NULL, NULL, NULL);

-- Équipements supplémentaires pour les statistiques d'inventaire
INSERT INTO equipements (nom, type, localisation, statut)
VALUES 
('Imprimante Canon MF644Cdw', 'Imprimante', 'Bureau Comptabilité', 'operationnel'),
('PC HP EliteDesk 800', 'Ordinateur', 'Bureau Direction', 'operationnel'),
('Switch TP-Link TL-SG1024', 'Réseau', 'Salle serveur 2', 'operationnel'),
('Serveur Dell PowerEdge R740', 'Serveur', 'Salle serveur principale', 'en_maintenance'),
('Ecran Samsung 24"', 'Moniteur', 'Bureau Marketing', 'en_panne'),
('Routeur Cisco RV340', 'Réseau', 'Salle réseau', 'operationnel'),
('Scanner Fujitsu fi-7160', 'Scanner', 'Bureau RH', 'hors_service'),
('Tablette Samsung Galaxy Tab', 'Tablette', 'Bureau Commercial', 'operationnel');


-- Statistiques mensuelles pour les 6 derniers mois
INSERT IGNORE INTO statistiques (periode, date_rapport, total_demandes, demandes_terminees, demandes_en_attente, demandes_en_cours, temps_moyen_traitement)
VALUES 
('mensuel', '2025-01-01', 18, 15, 1, 2, 2.25),
('mensuel', '2025-02-01', 22, 18, 2, 2, 2.50),
('mensuel', '2025-03-01', 25, 20, 3, 2, 2.00),
('mensuel', '2025-04-01', 20, 16, 2, 2, 1.75),
('mensuel', '2025-05-01', 23, 19, 2, 2, 1.50),
('mensuel', '2025-06-01', 26, 22, 2, 2, 1.25);

-- Notifications pour différents statuts
INSERT INTO notifications (destinataire_id, message, date_creation)
VALUES 
(@u1, 'Votre demande "Clavier défectueux" a été résolue.', '2025-06-15 10:21:00'),
(@u2, 'Votre demande "Souris sans fil" a été résolue.', '2025-06-18 11:26:00'),
(@u3, 'Votre demande "Accès VPN" a été résolue.', '2025-06-20 15:46:00'),
(@u1, 'Votre demande "Ecran cassé" a été résolue.', '2025-07-05 13:51:00'),
(@u2, 'Un technicien a été assigné à votre demande "Logiciel CRM".', '2025-07-10 17:20:00'),
(@resp, 'Nouvelle demande créée: "Imprimante bloquée".', '2025-07-12 09:31:00'),
(@resp, 'Nouvelle demande créée: "Wifi lent".', '2025-07-15 13:16:00'),
(@u2, 'Votre demande "Scanner défectueux" a été validée.', '2025-07-18 10:01:00');
