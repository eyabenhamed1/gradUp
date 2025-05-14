<?php
require_once(__DIR__ . "/../Config.php");

class ParticipationModel {
    private $db;

    public function __construct() {
        try {
            $this->db = config::getConnexion();
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    protected function getConnexion() {
        return config::getConnexion();
    }

    /**
     * Crée une nouvelle participation
     * @param array $data Données de la participation
     * @return int ID de la nouvelle participation
     * @throws PDOException
     */
    public function creerParticipation($data) {
        try {
            $sql = "INSERT INTO participation 
                   (id_evenement, id_utilisateur, email, statut, date_inscription, commentaire, telephone) 
                   VALUES 
                   (:id_evenement, :id_utilisateur, :email, :statut, :date_inscription, :commentaire, :telephone)";
            
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':id_evenement' => $data['id_evenement'],
                ':id_utilisateur' => $data['id_utilisateur'],
                ':email' => $data['email'], 
                ':statut' => $data['statut'],
                ':date_inscription' => $data['date_inscription'],
                ':commentaire' => $data['commentaire'] ?? null,
                ':telephone' => $data['telephone'] ?? null
            ]);
            
            if (!$success) {
                error_log("Erreur d'insertion: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
            
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si un utilisateur est déjà inscrit à un événement
     * @param int $id_evenement
     * @param int $id_utilisateur
     * @return bool
     * @throws PDOException
     */
    public function estDejaInscrit($eventId, $userId) {
        $sql = "SELECT COUNT(*) FROM participation 
                WHERE id_evenement = ? AND id_utilisateur = ? 
                AND statut = 'confirme'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId, $userId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Supprime une participation avec vérification de l'utilisateur
     * @param int $participation_id
     * @param int $id_utilisateur
     * @return bool
     * @throws PDOException
     */
    public function supprimerParticipation(int $participation_id, int $id_utilisateur): bool {
        $stmt = $this->db->prepare("DELETE FROM participation
                                  WHERE id_participation = :part_id
                                  AND id_utilisateur = :id_utilisateur");
        return $stmt->execute([
            ':part_id' => $participation_id,
            ':id_utilisateur' => $id_utilisateur
        ]);
    }

    /**
     * Récupère toutes les participations d'un utilisateur
     * @param int $id_utilisateur
     * @return array
     * @throws PDOException
     */
    public function getByUserId(int $id_utilisateur): array {
        $stmt = $this->db->prepare("SELECT p.*, e.titre, e.date_evenement, e.lieu
                                  FROM participation p
                                  JOIN evenement e ON p.id_evenement = e.id
                                  WHERE p.id_utilisateur = :id_utilisateur
                                  ORDER BY p.date_inscription DESC");
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Récupère une participation spécifique
     * @param int $event_id
     * @param int $id_utilisateur
     * @return array|null
     * @throws PDOException
     */
    public function getParticipation(int $event_id, int $id_utilisateur): ?array {
        $stmt = $this->db->prepare("SELECT p.*, e.titre, e.date_evenement, e.lieu
                                  FROM participation p
                                  JOIN evenement e ON p.id_evenement = e.id
                                  WHERE p.id_evenement = :event_id
                                  AND p.id_utilisateur = :id_utilisateur");
        $stmt->execute([
            ':event_id' => $event_id,
            ':id_utilisateur' => $id_utilisateur
        ]);
        
        return $stmt->fetch() ?: null;
    }

    /**
     * Met à jour le statut d'une participation
     * @param int $participation_id
     * @param int $id_utilisateur
     * @param string $new_statut
     * @return bool
     * @throws PDOException
     */
    public function updateStatutParticipation($participationId, $newStatut) {
        $sql = "UPDATE participation SET statut = ? WHERE id_participation = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newStatut, $participationId]);
    }

    /**
     * Récupère toutes les participations pour un événement (pour l'admin)
     * @param int $event_id
     * @return array
     * @throws PDOException
     */
    public function getParticipantsForEvent(int $event_id): array {
        $stmt = $this->db->prepare("SELECT p.*, u.nom, u.prenom, u.email
                                  FROM participation p
                                  JOIN user u ON p.id_utilisateur = u.id
                                  WHERE p.id_evenement = :event_id
                                  ORDER BY p.date_inscription DESC");
        $stmt->execute([':event_id' => $event_id]);
        return $stmt->fetchAll() ?: [];
    }
    public function countParticipantsByEvent($eventId) {
        $query = "SELECT COUNT(*) FROM participation WHERE id_evenement = :event_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":event_id", $eventId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    ////////////
    
    public function getParticipationsByStudentId($studentId) {
        $sql = "SELECT p.*, e.titre, e.date_evenement, e.lieu 
                FROM participation p
                JOIN evenement e ON p.id_evenement = e.id
                WHERE p.id_utilisateur = :student_id
                ORDER BY p.date_inscription DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

/////////////
 /**
     * Annule une participation spécifique
     */
    public function annulerParticipation($participationId, $studentId) {
        $sql = "UPDATE participation 
                SET statut = 'annule' 
                WHERE id_participation = ? 
                AND id_utilisateur = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$participationId, $studentId]);
    }
    public function getParticipationById($id_participation, $id_utilisateur) {
        $sql = "SELECT p.*, e.titre, e.date_evenement, e.lieu 
                FROM participation p
                JOIN evenement e ON p.id_evenement = e.id
                WHERE p.id_participation = :id_participation 
                AND p.id_utilisateur = :id_utilisateur";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_participation' => $id_participation,
            ':id_utilisateur' => $id_utilisateur
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAvailableEventsForStudent($studentId) {
        $sql = "SELECT e.* FROM evenement e
                WHERE e.id NOT IN (
                    SELECT p.id_evenement 
                    FROM participation p 
                    WHERE p.id_utilisateur = :student_id
                    AND p.statut = 'confirme'
                )
                AND e.date_evenement >= CURDATE()
                ORDER BY e.date_evenement ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
  ////-->  public function getParticipationStatus($studentId, $eventId) {
      ////  $sql = "SELECT statut FROM participation WHERE id_utilisateur = ? AND id_evenement = ?";
       /// $stmt = $this->db->prepare($sql);
     ///   $stmt->execute([$studentId, $eventId]);
      ///  $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
     ///   return $result ? $result['statut'] : null;
   /// } 

    public function updateParticipation($id_participation, $data) {
        $sql = "UPDATE participation SET ";
        $updates = [];
        $params = [':id' => $id_participation];
    
        foreach ($data as $key => $value) {
            $updates[] = "$key = :$key";
            $params[":$key"] = $value;
        }
    
        $sql .= implode(", ", $updates) . " WHERE id_participation = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
}
public function getParticipationStatus($studentId, $eventId) {
    $sql = "SELECT statut FROM participation 
            WHERE id_utilisateur = :studentId 
            AND id_evenement = :eventId";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':studentId' => $studentId,
        ':eventId' => $eventId
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['statut'] : null;
}
////reminder
public function getUpcomingParticipations($studentId, $daysAhead = 7) {
    $sql = "SELECT p.*, e.titre, e.date_evenement, e.lieu 
            FROM participation p
            JOIN evenement e ON p.id_evenement = e.id
            WHERE p.id_utilisateur = :student_id
            AND e.date_evenement BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL :days DAY)
            AND p.statut = 'confirme'
            ORDER BY e.date_evenement ASC";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
    $stmt->bindValue(':days', $daysAhead, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

public function markNotificationAsRead($participationId, $studentId) {
    $sql = "UPDATE participation SET notification_read = 1 
            WHERE id_participation = ? AND id_utilisateur = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$participationId, $studentId]);
}

public function updateParticipationStatus($userId, $eventId, $newStatus) {
    $sql = "UPDATE participation 
            SET statut = :status 
            WHERE id_utilisateur = :userId 
            AND id_evenement = :eventId";
    
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':status' => $newStatus,
        ':userId' => $userId,
        ':eventId' => $eventId
    ]);
}

}
