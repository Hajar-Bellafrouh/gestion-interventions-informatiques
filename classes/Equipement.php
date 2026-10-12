<?php
// Classe Equipement
class Equipement {
    private $id;
    private $nom;
    private $type;
    private $localisation;
    private $statut;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getType() { return $this->type; }
    public function getLocalisation() { return $this->localisation; }
    public function getStatut() { return $this->statut; }

    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setType($type) { $this->type = $type; }
    public function setLocalisation($localisation) { $this->localisation = $localisation; }
    public function setStatut($statut) { $this->statut = $statut; }


    public function ajouter($nom, $type, $localisation) {
        $this->nom = $nom;
        $this->type = $type;
        $this->localisation = $localisation;
        $this->statut = 'operationnel';

        $sql = "INSERT INTO equipements (nom, type, localisation, statut)
                        VALUES (:nom, :type, :localisation, :statut)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([
            ':nom' => $this->nom,
            ':type' => $this->type,
            ':localisation' => $this->localisation,
            ':statut' => $this->statut
        ])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function modifier($id, $nom, $type, $localisation, $statut) {
        $this->id = $id; // Assurez-vous que l'ID est défini pour la modification
        $this->nom = $nom;
        $this->type = $type;
        $this->localisation = $localisation;
        $this->statut = $statut;

        $sql = "UPDATE equipements SET nom = :nom, type = :type,
                        localisation = :localisation, statut = :statut WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $this->nom,
            ':type' => $this->type,
            ':localisation' => $this->localisation,
            ':statut' => $this->statut,
            ':id' => $this->id
        ]);
    }

    public function supprimer($id) {
        $sql = "DELETE FROM equipements WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public static function listerTous($db) {
        $sql = "SELECT * FROM equipements ORDER BY nom";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id, $db) {
        $sql = "SELECT * FROM equipements WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $equipement = new self($db);
            $equipement->setId($data['id']);
            $equipement->setNom($data['nom']);
            $equipement->setType($data['type']);
            $equipement->setLocalisation($data['localisation']);
            $equipement->setStatut($data['statut']);
            return $equipement;
        }
        return null;
    }
}
?>