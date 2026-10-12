<?php
// Classe Demande
class Demande {
    private $id;
    private $titre;
    private $description;
    private $priorite;
    private $statut;
    private $utilisateur_id;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Getters et Setters
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getPriorite() { return $this->priorite; }
    public function getStatut() { return $this->statut; }
    public function getUtilisateurId() { return $this->utilisateur_id; }

    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setPriorite($priorite) { $this->priorite = $priorite; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setUtilisateurId($utilisateur_id) { $this->utilisateur_id = $utilisateur_id; }


    public function valider() {
        $this->statut = 'validee';
        return $this->sauvegarder();
    }

    public function rejeter() {
        $this->statut = 'rejetee';
        return $this->sauvegarder();
    }

    public function sauvegarder() {
        if ($this->id) {
            $sql = "UPDATE demandes SET titre = :titre, description = :description,
                            priorite = :priorite, statut = :statut WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':titre' => $this->titre,
                ':description' => $this->description,
                ':priorite' => $this->priorite,
                ':statut' => $this->statut,
                ':id' => $this->id
            ]);
        }
        return false;
    }

    public static function findById($id, $db) {
        $sql = "SELECT * FROM demandes WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $demande = new self($db);
            $demande->id = $data['id'];
            $demande->titre = $data['titre'];
            $demande->description = $data['description'];
            $demande->priorite = $data['priorite'];
            $demande->statut = $data['statut'];
            $demande->utilisateur_id = $data['utilisateur_id'];
            return $demande;
        }
        return null;
    }
}
?>