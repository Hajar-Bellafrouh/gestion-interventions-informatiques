<?php
// Classe Statistique
class Statistique {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

   public function genererRapport($periode = 'mensuel') {
    // Requête principale pour les stats de base
    $sql = "SELECT
                COUNT(*) as total_demandes,
                COUNT(CASE WHEN d.statut = 'terminee' THEN 1 END) as demandes_terminees,
                COUNT(CASE WHEN d.statut = 'en_attente' THEN 1 END) as demandes_en_attente,
                COUNT(CASE WHEN d.statut = 'en_cours' THEN 1 END) as demandes_en_cours,
                COUNT(CASE WHEN d.statut = 'validee' THEN 1 END) as demandes_validees,
                COUNT(CASE WHEN d.statut = 'rejetee' THEN 1 END) as demandes_rejetees,
                COUNT(CASE WHEN d.priorite = 'basse' THEN 1 END) as priorite_basse,
                COUNT(CASE WHEN d.priorite = 'normale' THEN 1 END) as priorite_normale,
                COUNT(CASE WHEN d.priorite = 'haute' THEN 1 END) as priorite_haute,
                COUNT(CASE WHEN d.priorite = 'critique' THEN 1 END) as priorite_critique,
                AVG(CASE WHEN d.statut = 'terminee' AND d.date_validation IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, d.date_creation, d.date_validation) END) as temps_moyen_validation,
                AVG(CASE WHEN d.statut = 'terminee' AND i.date_debut IS NOT NULL AND i.date_fin IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, i.date_debut, i.date_fin) END) as temps_moyen_resolution
            FROM demandes d
            LEFT JOIN interventions i ON d.id = i.demande_id";
    
    // Filtre par période
    if ($periode == 'mensuel') {
        $sql .= " WHERE MONTH(d.date_creation) = MONTH(NOW()) AND YEAR(d.date_creation) = YEAR(NOW())";
    } elseif ($periode == 'annuel') {
        $sql .= " WHERE YEAR(d.date_creation) = YEAR(NOW())";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function exporterDonnees($format = 'json') {
        $data = $this->genererRapport();

        if ($format == 'json') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
            exit();
        } elseif ($format == 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=statistiques_rapport_' . date('Y-m-d') . '.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($data));
            fputcsv($output, array_values($data));
            fclose($output);
            exit();
        }

        return $data;
    }
}
?>