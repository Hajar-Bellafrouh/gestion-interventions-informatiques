<?php
// Classe de connexion à la base de données
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct() {
        // Les valeurs viennent de includes/config.php (constantes DB_*).
        // Valeurs par défaut si la classe est utilisée seule (ex: scripts/seed_users.php).
        $this->host     = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->dbname   = defined('DB_NAME') ? DB_NAME : 'gestion_interventions_ocp';
        $this->username = defined('DB_USER') ? DB_USER : 'root';
        $this->password = defined('DB_PASS') ? DB_PASS : '';

        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>