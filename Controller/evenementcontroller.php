<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../Model/evenement.php");

class EvenementController {
    private $conn;

    public function __construct() {
        $this->conn = config::getConnexion();
    }

    // Créer un événement
    public function createEvenement($titre, $description, $date_evenement, $lieu, $type_evenement, $image) {
        try {
            $query = $this->conn->prepare("INSERT INTO evenement (titre, description, date_evenement, lieu, type_evenement, image) 
                                        VALUES (:titre, :description, :date_evenement, :lieu, :type_evenement, :image)");
            $query->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':date_evenement' => $date_evenement,
                ':lieu' => $lieu,
                ':type_evenement' => $type_evenement,
                ':image' => $image
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur création événement: " . $e->getMessage());
            return false;
        }
    }

    // Lire la liste des événements (pour backoffice)
    public function listeEvenement() {
        try {
            $query = $this->conn->prepare("SELECT * FROM evenement ORDER BY date_evenement DESC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur liste événements: " . $e->getMessage());
            return [];
        }
    }

    // Mettre à jour un événement
    public function updateEvenement($id, $titre, $description, $date_evenement, $lieu, $type_evenement, $image) {
        try {
            $query = $this->conn->prepare("UPDATE evenement 
                                       SET titre = :titre, 
                                           description = :description, 
                                           date_evenement = :date_evenement, 
                                           lieu = :lieu, 
                                           type_evenement = :type_evenement,
                                           image = :image
                                       WHERE id = :id");
            $query->execute([
                ':id' => $id,
                ':titre' => $titre,
                ':description' => $description,
                ':date_evenement' => $date_evenement,
                ':lieu' => $lieu,
                ':type_evenement' => $type_evenement,
                ':image' => $image
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur mise à jour événement: " . $e->getMessage());
            return false;
        }
    }

    // Supprimer un événement
    public function deleteEvenement($id) {
        try {
            $query = $this->conn->prepare("DELETE FROM evenement WHERE id = :id");
            $query->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur suppression événement: " . $e->getMessage());
            return false;
        }
    }

    // Lire un événement par ID
    public function getEvenementById($id) {
        try {
            $query = $this->conn->prepare("SELECT * FROM evenement WHERE id = :id");
            $query->execute([':id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération événement: " . $e->getMessage());
            return null;
        }
    }

    /********************
     * METHODES FRONTOFFICE
     ********************/
    
    // Récupère tous les événements à venir (pour frontoffice)
    public function getAllEvenements() {
        try {
            $query = $this->conn->prepare("SELECT * FROM evenement 
                                        WHERE date_evenement >= CURDATE()
                                        ORDER BY date_evenement ASC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur liste événements front: " . $e->getMessage());
            return [];
        }
    }

    // Récupère les prochains événements (pour widget)
    public function getUpcomingEvenements($limit = 3) {
        try {
            $query = $this->conn->prepare("SELECT * FROM evenement 
                                        WHERE date_evenement >= CURDATE()
                                        ORDER BY date_evenement ASC 
                                        LIMIT :limit");
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur prochains événements: " . $e->getMessage());
            return [];
        }
    }

    // Récupère les événements passés (optionnel)
    public function getPastEvenements($limit = 3) {
        try {
            $query = $this->conn->prepare("SELECT * FROM evenement 
                                        WHERE date_evenement < CURDATE()
                                        ORDER BY date_evenement DESC 
                                        LIMIT :limit");
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur événements passés: " . $e->getMessage());
            return [];
        }
    }
    // Recherche d'événements (optionnel)
    public function searchEvenements($keyword) {
        try {
            $searchTerm = "%$keyword%";
            $query = $this->conn->prepare("SELECT * FROM evenement 
                                        WHERE titre LIKE :keyword 
                                        OR description LIKE :keyword
                                        ORDER BY date_evenement DESC");
            $query->execute([':keyword' => $searchTerm]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur recherche événements: " . $e->getMessage());
            return [];
        }
    }
}
?>