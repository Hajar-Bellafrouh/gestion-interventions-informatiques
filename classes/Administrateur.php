<?php
require_once __DIR__ . '/Utilisateur.php';

// Classe Administrateur (hérite d'Utilisateur)
class Administrateur extends Utilisateur {
    public function __construct($db, $data = null) {
        parent::__construct($db);
        $this->role = 'administrateur';
        if ($data) {
            $this->id = $data['id'];
            $this->nom = $data['nom'];
            $this->prenom = $data['prenom'];
            $this->email = $data['email'];
        }
    }

    public function gererUtilisateurs() {
        $sql = "SELECT * FROM utilisateurs ORDER BY nom, prenom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterUtilisateur($nom, $prenom, $email, $motDePasse, $role, $niveau = null) {
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, motDePasse, role, niveau)
            VALUES (:nom, :prenom, :email, :motDePasse, :role, :niveau)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':motDePasse' => password_hash($motDePasse, PASSWORD_DEFAULT),
        ':role' => $role,
        ':niveau' => $niveau
    ]);
}


    public function modifierUtilisateur($id, $nom, $prenom, $email, $role, $niveau = null, $actif = true) {
        $sql = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, role = :role, niveau = :niveau, actif = :actif WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':role' => $role,
            ':niveau' => $niveau,
            ':actif' => $actif,
            ':id' => $id
        ]);
    }

    public function supprimerUtilisateur($id) {
        $sql = "DELETE FROM utilisateurs WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function configurerPlateforme($parametre, $valeur) {
        // Logique de configuration (peut être stockée dans une table de paramètres)
        // Pour l'instant, c'est une fonction factice
        return true;
    }

    public function superviserInterventions() {
        $sql = "SELECT i.*, d.titre, d.description, u.nom as technicien_nom, u.prenom as technicien_prenom,
                       dem.nom as demandeur_nom, dem.prenom as demandeur_prenom
                FROM interventions i
                JOIN demandes d ON i.demande_id = d.id
                JOIN utilisateurs u ON i.technicien_id = u.id
                JOIN utilisateurs dem ON d.utilisateur_id = dem.id
                ORDER BY i.date_planifiee DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exporterStatistiques($format = 'excel') {
        // Récupérer les données de la table statistiques ou générer à la volée
        $statistique = new Statistique($this->db);
        $data = $statistique->genererRapport(); // Utilise la méthode de la classe Statistique

        if ($format == 'excel') {
            // Logique d'export Excel (nécessiterait une bibliothèque comme PhpSpreadsheet)
            // Pour cet exemple, nous allons simuler un fichier CSV qui peut être ouvert par Excel
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=statistiques_interventions_' . date('Y-m-d') . '.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($data));
            fputcsv($output, array_values($data));
            fclose($output);
            exit();
        } elseif ($format == 'text') {
            // Logique d'export au format texte
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename=statistiques_interventions_' . date('Y-m-d') . '.txt');
            echo "Rapport de Statistiques des Interventions\n\n";
            foreach ($data as $key => $value) {
                echo ucfirst(str_replace('_', ' ', $key)) . ": " . $value . "\n";
            }
            exit();
        }
        return $data;
    }

    // Méthode pour obtenir toutes les demandes (pour supervision)
    public function getAllDemandes() {
        $sql = "SELECT d.*, u.nom as demandeur_nom, u.prenom as demandeur_prenom,
                       r.nom as responsable_nom, r.prenom as responsable_prenom
                FROM demandes d
                JOIN utilisateurs u ON d.utilisateur_id = u.id
                LEFT JOIN utilisateurs r ON d.responsable_id = r.id
                ORDER BY d.date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>