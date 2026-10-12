<?php
// Classe Intervention
class Intervention {
    private $id;
    private $demande_id;
    private $technicien_id;
    private $date_planifiee;
    private $statut;
    private $rapport;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getId() { return $this->id; }
    public function getStatut() { return $this->statut; }
    public function getRapport() { return $this->rapport; }
    public function getDemandeId() { return $this->demande_id; }
    public function getTechnicienId() { return $this->technicien_id; }
    public function getDatePlanifiee() { return $this->date_planifiee; }

    public function setId($id) { $this->id = $id; }
    public function setDemandeId($demande_id) { $this->demande_id = $demande_id; }
    public function setTechnicienId($technicien_id) { $this->technicien_id = $technicien_id; }
    public function setDatePlanifiee($date_planifiee) { $this->date_planifiee = $date_planifiee; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setRapport($rapport) { $this->rapport = $rapport; }


    public function attribuerTechnicien($technicien_id) {
        $this->technicien_id = $technicien_id;
        return $this->sauvegarder();
    }

    public function mettreAJourStatut($statut) {
        $this->statut = $statut;
        return $this->sauvegarder();
    }

    public function genererFiche() {
        $sql = "SELECT i.*, d.titre, d.description, d.priorite, d.statut as demande_statut,
                       u.nom as technicien_nom, u.prenom as technicien_prenom,
                       dem.nom as demandeur_nom, dem.prenom as demandeur_prenom
                        FROM interventions i
                        JOIN demandes d ON i.demande_id = d.id
                        JOIN utilisateurs u ON i.technicien_id = u.id
                        JOIN utilisateurs dem ON d.utilisateur_id = dem.id
                        WHERE i.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function sauvegarder() {
        if ($this->id) {
            $sql = "UPDATE interventions SET statut = :statut, rapport = :rapport,
                            date_planifiee = :date_planifiee, technicien_id = :technicien_id
                            WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':statut' => $this->statut,
                ':rapport' => $this->rapport,
                ':date_planifiee' => $this->date_planifiee,
                ':technicien_id' => $this->technicien_id,
                ':id' => $this->id
            ]);
        }
        return false;
    }

    public static function findById($id, $db) {
        $sql = "SELECT * FROM interventions WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $intervention = new self($db);
            $intervention->setId($data['id']);
            $intervention->setDemandeId($data['demande_id']);
            $intervention->setTechnicienId($data['technicien_id']);
            $intervention->setDatePlanifiee($data['date_planifiee']);
            $intervention->setStatut($data['statut']);
            $intervention->setRapport($data['rapport']);
            return $intervention;
        }
        return null;
    }
}
?>