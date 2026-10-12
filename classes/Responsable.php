<?php
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/Intervention.php';
require_once __DIR__ . '/Demande.php';

// Classe Responsable (hérite d'Utilisateur)
class Responsable extends Utilisateur {
    public function __construct($db, $data = null) {
        parent::__construct($db);
        $this->role = 'responsable';
        if ($data) {
            $this->id = $data['id'];
            $this->nom = $data['nom'];
            $this->prenom = $data['prenom'];
            $this->email = $data['email'];
        }
    }

    public function validerDemande($demande_id, $validation = true) {
        $statut = $validation ? 'validee' : 'rejetee';
        $sql = "UPDATE demandes SET statut = :statut, responsable_id = :responsable_id,
                        date_validation = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':statut' => $statut,
            ':responsable_id' => $this->id,
            ':id' => $demande_id
        ]);
    }

    public function attribuerIntervention($demande_id, $technicien_id, $date_planifiee) {
        // Vérifier si la demande est validée avant d'attribuer
        $demande = Demande::findById($demande_id, $this->db);
        if (!$demande || $demande->getStatut() !== 'validee') {
            return false; // La demande n'est pas valide ou n'est pas validée
        }

        $sql = "INSERT INTO interventions (demande_id, technicien_id, date_planifiee, statut)
                        VALUES (:demande_id, :technicien_id, :date_planifiee, 'planifiee')";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':demande_id' => $demande_id,
            ':technicien_id' => $technicien_id,
            ':date_planifiee' => $date_planifiee
        ]);

        if ($success) {
            // Mettre à jour le statut de la demande à 'en_cours' ou 'en_attente_technicien'
            $sql_update_demande = "UPDATE demandes SET statut = 'en_attente_technicien' WHERE id = :demande_id";
            $stmt_update_demande = $this->db->prepare($sql_update_demande);
            $stmt_update_demande->execute([':demande_id' => $demande_id]);
        }
        return $success;
    }

    public function planifierIntervention($intervention_id, $nouvelle_date) {
        $sql = "UPDATE interventions SET date_planifiee = :date WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':date' => $nouvelle_date,
            ':id' => $intervention_id
        ]);
    }

    public function consulterStatistiques() {
        $sql = "SELECT
                            COUNT(CASE WHEN statut = 'en_attente' THEN 1 END) as en_attente,
                            COUNT(CASE WHEN statut = 'validee' THEN 1 END) as validees,
                            COUNT(CASE WHEN statut = 'en_cours' THEN 1 END) as en_cours,
                            COUNT(CASE WHEN statut = 'terminee' THEN 1 END) as terminees
                        FROM demandes
                        WHERE MONTH(date_creation) = MONTH(NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDemandesEnAttente() {
        $sql = "SELECT d.*, u.nom as demandeur_nom, u.prenom as demandeur_prenom
                        FROM demandes d
                        JOIN utilisateurs u ON d.utilisateur_id = u.id
                        WHERE d.statut = 'en_attente'
                        ORDER BY
                                CASE d.priorite
                                    WHEN 'critique' THEN 1
                                    WHEN 'haute' THEN 2
                                    WHEN 'normale' THEN 3
                                    WHEN 'basse' THEN 4
                                END,
                                d.date_creation";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTechniciens() {
        $sql = "SELECT id, nom, prenom, niveau FROM utilisateurs WHERE role = 'technicien' ORDER BY niveau, nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInterventionsPlanifiees() {
        $sql = "SELECT i.*, d.titre, d.description, u.nom as technicien_nom, u.prenom as technicien_prenom,
                       dem.nom as demandeur_nom, dem.prenom as demandeur_prenom
                        FROM interventions i
                        JOIN demandes d ON i.demande_id = d.id
                        JOIN utilisateurs u ON i.technicien_id = u.id
                        JOIN utilisateurs dem ON d.utilisateur_id = dem.id
                        WHERE i.statut IN ('planifiee', 'en_cours', 'en_attente_pieces')
                        ORDER BY i.date_planifiee ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInterventionsTerminees() {
        $sql = "SELECT i.*, d.titre, d.description, u.nom as technicien_nom, u.prenom as technicien_prenom,
                       dem.nom as demandeur_nom, dem.prenom as demandeur_prenom
                        FROM interventions i
                        JOIN demandes d ON i.demande_id = d.id
                        JOIN utilisateurs u ON i.technicien_id = u.id
                        JOIN utilisateurs dem ON d.utilisateur_id = dem.id
                        WHERE i.statut = 'terminee'
                        ORDER BY i.date_fin DESC
                        LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNotificationsNonLues() {
        $sql = "SELECT * FROM notifications
                        WHERE destinataire_id = :responsable_id AND lue = 0
                        ORDER BY date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':responsable_id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marquerNotificationsCommeLues() {
        $sql = "UPDATE notifications SET lue = 1
                        WHERE destinataire_id = :responsable_id AND lue = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':responsable_id' => $this->id]);
    }

    public function supprimerNotification($notification_id) {
        $sql = "DELETE FROM notifications
                        WHERE id = :id AND destinataire_id = :responsable_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $notification_id,
            ':responsable_id' => $this->id
        ]);
    }
}
?>