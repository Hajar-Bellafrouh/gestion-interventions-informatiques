<?php
// Classe Utilisateur (classe parent)
class Utilisateur {
    protected $id;
    protected $nom;
    protected $prenom;
    protected $email;
    protected $motDePasse;
    protected $role;
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }

    // Setters
    public function setRole($role) { $this->role = $role; }
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setMotDePasse($motDePasse) {
        // Le mot de passe est haché avant d'être stocké
        $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
    }


    // Méthodes communes
    public function creerDemande($titre, $description, $priorite = 'normale') {
        $sql = "INSERT INTO demandes (titre, description, priorite, utilisateur_id)
                 VALUES (:titre, :description, :priorite, :utilisateur_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':priorite' => $priorite,
            ':utilisateur_id' => $this->id
        ]);
    }

    public function consulterEtatDemande($demande_id) {
        $sql = "SELECT * FROM demandes WHERE id = :id AND utilisateur_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $demande_id, ':user_id' => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function sauvegarder() {
        if ($this->id) {
            // Mise à jour
            $sql = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom,
                            email = :email WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nom' => $this->nom,
                ':prenom' => $this->prenom,
                ':email' => $this->email,
                ':id' => $this->id
            ]);
        } else {
            // Insertion
            $sql = "INSERT INTO utilisateurs (nom, prenom, email, motDePasse, role)
                            VALUES (:nom, :prenom, :email, :motDePasse, :role)";
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute([
                ':nom' => $this->nom,
                ':prenom' => $this->prenom,
                ':email' => $this->email,
                ':motDePasse' => $this->motDePasse,
                ':role' => $this->role
            ])) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
        }
        return false;
    }

    public static function authentifier($email, $motDePasse, $db) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email AND actif = 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Accepte les mots de passe hachés (password_hash) ET les anciens mots de passe en clair
        if ($user && (password_verify($motDePasse, $user['motDePasse']) || $motDePasse === $user['motDePasse'])) {
            // Initialiser l'objet utilisateur de base
            $utilisateur = new Utilisateur($db);
            $utilisateur->id = $user['id'];
            $utilisateur->nom = $user['nom'];
            $utilisateur->prenom = $user['prenom'];
            $utilisateur->email = $user['email'];
            $utilisateur->role = $user['role'];

            // Retourner l'objet spécifique selon le rôle
            switch ($user['role']) {
                case 'administrateur':
                    $admin = new Administrateur($db);
                    $admin->id = $user['id'];
                    $admin->nom = $user['nom'];
                    $admin->prenom = $user['prenom'];
                    $admin->email = $user['email'];
                    $admin->role = $user['role'];
                    return $admin;

                case 'responsable':
                    $resp = new Responsable($db);
                    $resp->id = $user['id'];
                    $resp->nom = $user['nom'];
                    $resp->prenom = $user['prenom'];
                    $resp->email = $user['email'];
                    $resp->role = $user['role'];
                    return $resp;

                case 'technicien':
                    $tech = new Technicien($db);
                    $tech->id = $user['id'];
                    $tech->nom = $user['nom'];
                    $tech->prenom = $user['prenom'];
                    $tech->email = $user['email'];
                    $tech->role = $user['role'];
                    $tech->setNiveau($user['niveau']); // Assurez-vous que le niveau est défini
                    return $tech;

                case 'utilisateur':
                    return $utilisateur;

                default:
                    throw new Exception("Rôle inconnu: " . $user['role']);
            }
        }
        return null;
    }

    public function getDemandesUtilisateur() {
        $sql = "SELECT d.*,
                        DATE_FORMAT(d.date_creation, '%Y-%m-%d %H:%i:%s') as date_creation,
                        DATE_FORMAT(d.date_validation, '%Y-%m-%d %H:%i:%s') as date_validation
                        FROM demandes d
                        WHERE d.utilisateur_id = :user_id
                        ORDER BY d.date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNotificationsNonLues() {
        $sql = "SELECT * FROM notifications
                        WHERE destinataire_id = :user_id AND lue = 0
                        ORDER BY date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marquerNotificationsCommeLues() {
        $sql = "UPDATE notifications SET lue = 1
                        WHERE destinataire_id = :user_id AND lue = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $this->id]);
    }

    public function supprimerNotification($notification_id) {
        $sql = "DELETE FROM notifications
                        WHERE id = :id AND destinataire_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $notification_id,
            ':user_id' => $this->id
        ]);
    }

    public static function getAllUtilisateurs(PDO $pdo) {
        $stmt = $pdo->query("SELECT * FROM utilisateurs ORDER BY nom, prenom");
        $utilisateurs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $utilisateur = new Utilisateur($pdo);
            $utilisateur->id = $row['id'];
            $utilisateur->nom = $row['nom'];
            $utilisateur->prenom = $row['prenom'];
            $utilisateur->email = $row['email'];
            $utilisateur->role = $row['role'];
            // Si c'est un technicien, initialiser le niveau
            if ($row['role'] === 'technicien') {
                $tech = new Technicien($pdo);
                $tech->id = $row['id'];
                $tech->nom = $row['nom'];
                $tech->prenom = $row['prenom'];
                $tech->email = $row['email'];
                $tech->role = $row['role'];
                $tech->setNiveau($row['niveau']);
                $utilisateurs[] = $tech;
            } else {
                $utilisateurs[] = $utilisateur;
            }
        }
        return $utilisateurs;
    }


    
}
?>