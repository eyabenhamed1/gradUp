<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../Model/evenement.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

class EvenementController {
    private $model;
    private $connexion;
    public function __construct() {
        $this->connexion = DB::getConnexion();
    }

    // Créer un événement
    public function createEvenement($titre, $description, $date_evenement, $lieu, $type_evenement, $image) {
        try {
            $query = $this->connexion->prepare("INSERT INTO evenement (titre, description, date_evenement, lieu, type_evenement, image) 
                                        VALUES (:titre, :description, :date_evenement, :lieu, :type_evenement, :image)");
            return $query->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':date_evenement' => $date_evenement,
                ':lieu' => $lieu,
                ':type_evenement' => $type_evenement,
                ':image' => $image
            ]);
        } catch (PDOException $e) {
            error_log("Erreur création événement: " . $e->getMessage());
            throw new Exception("Erreur lors de la création de l'événement");
        }
    }

    // Lire la liste des événements (pour backoffice)
    public function listeEvenement() {
        try {
            $query = $this->connexion->prepare("SELECT * FROM evenement ORDER BY date_evenement DESC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur liste événements: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des événements");
        }
    }

    // Mettre à jour un événement
    public function updateEvenement($id, $titre, $description, $date_evenement, $lieu, $type_evenement, $image) {
        try {
            $sql = "UPDATE evenement SET 
                    titre = :titre, 
                    description = :description, 
                    date_evenement = :date_evenement, 
                    lieu = :lieu, 
                    type_evenement = :type_evenement, 
                    image = :image 
                    WHERE id = :id";
            
            $query = $this->connexion->prepare($sql);
            return $query->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':date_evenement' => $date_evenement,
                ':lieu' => $lieu,
                ':type_evenement' => $type_evenement,
                ':image' => $image,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour événement: " . $e->getMessage());
            throw new Exception("Erreur lors de la mise à jour de l'événement");
        }
    }

    
    // Supprimer un événement
    public function deleteEvenement($id) {
        try {
            $query = $this->connexion->prepare("DELETE FROM evenement WHERE id = :id");
            return $query->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression événement: " . $e->getMessage());
            return false;
        }
    }

    // Lire un événement par ID
    public function getEvenementById($id) {
        try {
            $query = $this->connexion->prepare("SELECT * FROM evenement WHERE id = :id");
            $query->execute([':id' => $id]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception("Événement non trouvé");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur récupération événement: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération de l'événement");
        }
    }

    /********************
     * METHODES FRONTOFFICE
     ********************/
    
    // Récupère tous les événements à venir (pour frontoffice)
    public function getAllEvenements() {
        try {
            $query = $this->connexion->prepare("SELECT * FROM evenement 
                                        WHERE date_evenement >= CURDATE()
                                        ORDER BY date_evenement ASC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur liste événements front: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des événements");
        }
    }

    // Récupère les prochains événements (pour widget)
    public function getUpcomingEvenements($limit = 3) {
        try {
            $query = $this->connexion->prepare("SELECT * FROM evenement 
                                        WHERE date_evenement >= CURDATE()
                                        ORDER BY date_evenement ASC 
                                        LIMIT :limit");
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur prochains événements: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des événements à venir");
        }
    }

    // Récupère les événements passés (optionnel)
    public function getPastEvenements($limit = 3) {
        try {
            $query = $this->connexion->prepare("SELECT * FROM evenement 
                                        WHERE date_evenement < CURDATE()
                                        ORDER BY date_evenement DESC 
                                        LIMIT :limit");
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur événements passés: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des événements passés");
        }
    }

    // Recherche d'événements (optionnel)
    public function searchEvenements($keyword) {
        try {
            $searchTerm = "%$keyword%";
            $query = $this->connexion->prepare("SELECT * FROM evenement 
                                        WHERE titre LIKE :keyword 
                                        OR description LIKE :keyword
                                        ORDER BY date_evenement DESC");
            $query->execute([':keyword' => $searchTerm]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur recherche événements: " . $e->getMessage());
            throw new Exception("Erreur lors de la recherche d'événements");
        }
    }

    //////////////

    public function getEventById($id) {
        $sql = "SELECT * FROM evenement WHERE id = :id";
        $stmt = $this->connexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDerniersEvenements($limit = 3) {
        try {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/Model/evenement.php');
            $model = new Evenement();
            
            // Récupère les événements à venir, triés par date croissante
            return $model->getEvenementsRecents($limit);
            
        } catch (PDOException $e) {
            error_log("Erreur dans getDerniersEvenements: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des événements");
        }
    }

    ///////pagination 
    public function listeEvenementPagines($page = 1, $parPage = 6) {
        $sql = "SELECT * FROM evenement ORDER BY date_evenement DESC";
        $db = DB::getConnexion();
        
        try {
            // Requête pour compter le nombre total d'événements
            $countQuery = $db->query("SELECT COUNT(*) as total FROM evenement");
            $total = $countQuery->fetch()['total'];
            
            // Calcul de l'offset
            $offset = ($page - 1) * $parPage;
            $sql .= " LIMIT $offset, $parPage";
            
            $query = $db->query($sql);
            $events = $query->fetchAll();
            
            return [
                'events' => $events,
                'total' => $total,
                'page' => $page,
                'parPage' => $parPage,
                'totalPages' => ceil($total / $parPage)
            ];
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des événements: " . $e->getMessage());
        }
    }

    private function getEventColor($eventDate) {
        $eventTimestamp = is_string($eventDate) ? strtotime($eventDate) : $eventDate;
        $now = time();
        
        // Événement passé
        if ($eventTimestamp < $now) {
            return '#6c757d'; // Gris
        }
        // Événement dans moins de 24h
        elseif ($eventTimestamp - $now < 86400) {
            return '#dc3545'; // Rouge
        }
        // Événement futur
        else {
            return '#28a745'; // Vert
        }
    }

    /**
     * Récupère les événements pour FullCalendar
     * @return array Tableau d'événements formatés
     */
    public function getEventsForCalendar() {
        try {
            $db = DB::getConnexion();
            $stmt = $db->query("SELECT 
                id, 
                titre as title, 
                date_evenement as start,
                lieu as location,
                description,
                type_evenement as type
                FROM evenement");
            
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($events as &$event) {
                $event['backgroundColor'] = $this->getEventColor($event['start']);
                $event['textColor'] = '#ffffff';
                $event['borderColor'] = $this->getEventColor($event['start']);
                
                // Ajoute un tooltip
                $event['extendedProps'] = [
                    'tooltip' => $event['title'] . "\n" . 
                                date('d/m/Y H:i', strtotime($event['start'])) . "\n" .
                                $event['location']
                ];
            }
            
            return $events;
            
        } catch (PDOException $e) {
            error_log("Erreur dans getEventsForCalendar: " . $e->getMessage());
            return [];
        }
    }

////stat
public function getEventStatistics() {
    try {
        $stats = [];
        
        // Nombre total d'événements
        $query = $this->connexion->query("SELECT COUNT(*) as total FROM evenement");
        $stats['total_events'] = $query->fetch()['total'];
        
        // Événements passés et à venir
        $query = $this->connexion->query("SELECT 
            SUM(CASE WHEN date_evenement < NOW() THEN 1 ELSE 0 END) as past_events,
            SUM(CASE WHEN date_evenement >= NOW() THEN 1 ELSE 0 END) as upcoming_events
            FROM evenement");
        $result = $query->fetch();
        $stats['past_events'] = $result['past_events'];
        $stats['upcoming_events'] = $result['upcoming_events'];
        
        // Répartition par type
        $query = $this->connexion->query("SELECT type_evenement, COUNT(*) as count 
                                         FROM evenement 
                                         GROUP BY type_evenement");
        $stats['by_type'] = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
        
    } catch (PDOException $e) {
        error_log("Erreur statistiques: " . $e->getMessage());
        throw new Exception("Erreur lors du calcul des statistiques");
    }
}

public function getParticipationStats() {
    try {
        $stats = [];
        
        // Participation moyenne
        $query = $this->connexion->query("SELECT AVG(participant_count) as avg_participation 
                                         FROM (SELECT COUNT(*) as participant_count 
                                               FROM participation 
                                               GROUP BY id_evenement) as counts");
        $stats['avg_participation'] = round($query->fetch()['avg_participation'], 1);
        
        // Top 5 événements
        $query = $this->connexion->query("SELECT e.titre, COUNT(p.id_participation) as participants 
                                         FROM evenement e
                                         LEFT JOIN participation p ON e.id = p.id_evenement
                                         GROUP BY e.id
                                         ORDER BY participants DESC
                                         LIMIT 5");
        $stats['top_events'] = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
        
    } catch (PDOException $e) {
        error_log("Erreur stats participation: " . $e->getMessage());
        throw new Exception("Erreur stats participation");
    }
}

}