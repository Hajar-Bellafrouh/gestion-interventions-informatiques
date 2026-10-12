<?php
require_once __DIR__ . '/Utilisateur.php';

// Classe Technicien (hérite d'Utilisateur)
class Technicien extends Utilisateur {
    protected $niveau;

    public function __construct($db, $data = null) {
        parent::__construct($db);
        $this->role = 'technicien';
        if ($data) {
            $this->id = $data['id'];
            $this->nom = $data['nom'];
            $this->prenom = $data['prenom'];
            $this->email = $data['email'];
            $this->niveau = $data['niveau'];
        }
    }

    public function getNiveau() { return $this->niveau; }
    public function setNiveau($niveau) { $this->niveau = $niveau; }

    public function ajouterCommentaire($intervention_id, $commentaire) {
        // Ajouter à la table des commentaires ou dans le rapport
        $sql = "UPDATE interventions SET rapport = CONCAT(IFNULL(rapport, ''), '\n', :commentaire)
                        WHERE id = :id AND technicien_id = :technicien_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':commentaire' => date('Y-m-d H:i:s') . " - " . $commentaire,
            ':id' => $intervention_id,
            ':technicien_id' => $this->id
        ]);
    }

    public function consulterInterventions() {
        $sql = "SELECT i.*, d.titre, d.description, d.priorite, d.statut as demande_statut,
                       u.nom as demandeur_nom, u.prenom as demandeur_prenom
                        FROM interventions i
                        JOIN demandes d ON i.demande_id = d.id
                        JOIN utilisateurs u ON d.utilisateur_id = u.id
                        WHERE i.technicien_id = :technicien_id
                        ORDER BY i.date_planifiee ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':technicien_id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mettreAJourStatut($intervention_id, $statut) {
        $sql = "UPDATE interventions SET statut = :statut";

        if ($statut == 'en_cours') {
            $sql .= ", date_debut = NOW()";
        } elseif ($statut == 'terminee') {
            $sql .= ", date_fin = NOW()";
        }

        $sql .= " WHERE id = :id AND technicien_id = :technicien_id";

        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':statut' => $statut,
            ':id' => $intervention_id,
            ':technicien_id' => $this->id
        ]);

        if ($success && $statut == 'terminee') {
            // Mettre à jour le statut de la demande associée à 'terminee'
            $sql_demande_id = "SELECT demande_id FROM interventions WHERE id = :id";
            $stmt_demande_id = $this->db->prepare($sql_demande_id);
            $stmt_demande_id->execute([':id' => $intervention_id]);
            $demande_id = $stmt_demande_id->fetchColumn();

            if ($demande_id) {
                $sql_update_demande = "UPDATE demandes SET statut = 'terminee' WHERE id = :demande_id";
                $stmt_update_demande = $this->db->prepare($sql_update_demande);
                $stmt_update_demande->execute([':demande_id' => $demande_id]);
            }
        }
        return $success;
    }

    // Méthodes spécifiques aux niveaux de technicien
    public function getInterventionsNiveau1() {
        if ($this->niveau >= 1) {
            $sql = "SELECT i.*, d.titre, d.description, d.priorite, d.statut as demande_statut,
                           u.nom as demandeur_nom, u.prenom as demandeur_prenom
                    FROM interventions i
                    JOIN demandes d ON i.demande_id = d.id
                    JOIN utilisateurs u ON d.utilisateur_id = u.id
                    WHERE i.technicien_id = :technicien_id AND d.priorite IN ('basse', 'normale')
                    ORDER BY i.date_planifiee ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':technicien_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function getInterventionsNiveau2() {
        if ($this->niveau >= 2) {
            $sql = "SELECT i.*, d.titre, d.description, d.priorite, d.statut as demande_statut,
                           u.nom as demandeur_nom, u.prenom as demandeur_prenom
                    FROM interventions i
                    JOIN demandes d ON i.demande_id = d.id
                    JOIN utilisateurs u ON d.utilisateur_id = u.id
                    WHERE i.technicien_id = :technicien_id OR d.priorite IN ('haute')
                    ORDER BY i.date_planifiee ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':technicien_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function getInterventionsNiveau3() {
        if ($this->niveau >= 3) {
            $sql = "SELECT i.*, d.titre, d.description, d.priorite, d.statut as demande_statut,
                           u.nom as demandeur_nom, u.prenom as demandeur_prenom
                    FROM interventions i
                    JOIN demandes d ON i.demande_id = d.id
                    JOIN utilisateurs u ON d.utilisateur_id = u.id
                    WHERE i.technicien_id = :technicien_id OR d.priorite IN ('critique')
                    ORDER BY i.date_planifiee ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':technicien_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

   public function gererInventairePartiel() {
    // Affiche tous les équipements opérationnels ou en maintenance/panne
    $sql = "SELECT * FROM equipements WHERE statut IN ('operationnel', 'en_panne', 'en_maintenance')";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
?>