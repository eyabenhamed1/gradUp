<?php
class ParticipationController {
    private $participationModel;
    
    public function __construct() {
        require_once(__DIR__ . '/../Model/ParticipationModel.php');
        $this->participationModel = new ParticipationModel();
    }
    /**
     * Vérifie si un utilisateur est déjà inscrit à un événement
     * @param int $userId ID de l'utilisateur
     * @param int $eventId ID de l'événement
     * @return bool
     * @throws PDOException Si erreur de base de données
     */
    public function checkParticipation($userId, $eventId) {
        if (!is_numeric($userId) || !is_numeric($eventId)) {
            throw new InvalidArgumentException('IDs must be numeric');
        }
        
        return $this->participationModel->estDejaInscrit($eventId, $userId);
    }

    /**
     * Inscrit un participant à un événement
     * @param int $eventId
     * @param int $id_etudiant
     * @param string $statut
     * @param string $date_inscription
     * @return bool
     * @throws Exception
     */
     
     public function inscrireParticipant($eventId, $id_etudiant, $email, $statut = 'confirme', $comments = '', $date_inscription = null) {
        // Validation des entrées
        if (!is_numeric($eventId) || $eventId <= 0) {
            throw new InvalidArgumentException('ID événement invalide');
        }
        
        if (!is_numeric($id_etudiant) || $id_etudiant <= 0) {
            throw new InvalidArgumentException('ID étudiant invalide');
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email invalide');
        }
    
        // Vérification d'inscription existante
        if ($this->checkParticipation($id_etudiant, $eventId)) {
            throw new Exception('Vous êtes déjà inscrit à cet événement');
        }
        
        // Création de la participation
        return $this->participationModel->creerParticipation([
            'id_evenement' => (int)$eventId,
            'id_etudiant' => (int)$id_etudiant,
            'email' => $email, // Nouveau champ
            'statut' => $statut,
            'commentaire' => $comments,
            'date_inscription' => $date_inscription ?? date('Y-m-d H:i:s')
        ]);
    }
     public function countParticipants($eventId) {
        return $this->participationModel->countParticipantsByEvent($eventId);
    } 
/////
public function getParticipationsByUser($id_etudiant) {
    $sql = "SELECT * FROM participation WHERE id_etudiant = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function annulerParticipationByUserAndEvent($studentId, $participationId) {
    try {
        // 1. Vérifier d'abord si la participation appartient bien à l'étudiant
        $participation = $this->participationModel->getParticipationById($participationId);
        
        if (!$participation || $participation['id_etudiant'] != $studentId) {
            throw new Exception("Participation non trouvée ou non autorisée");
        }

        // 2. Vérifier si l'annulation est possible (statut approprié)
        if (!in_array($participation['statut'], ['confirme'])) {
            throw new Exception("Seules les inscriptions confirmées peuvent être annulées");
        }

        // 3. Procéder à l'annulation
        return $this->participationModel->annulerParticipation($participationId, $studentId);
        
    } catch (PDOException $e) {
        error_log("Erreur DB: " . $e->getMessage());
        throw new Exception("Erreur technique lors de l'annulation");
    }
}

public function modifierStatutParticipation($id_participation, $newStatus) {
    $allowedStatus = ['en_attente', 'confirme', 'annulé'];
    if (!in_array($newStatus, $allowedStatus)) {
        return false;
    }
    
    $sql = "UPDATE participation SET statut = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$newStatus, $id_participation]);
}


// Ajoutez cette méthode si vous voulez accéder à la connexion PDO
public function getConnexion() {
    return $this->db;
}
////////////
public function getParticipationsByStudentId($studentId) {
    if (!is_numeric($studentId)) {
        throw new InvalidArgumentException('Student ID must be numeric');
    }
    
    // Utilisez le modèle pour récupérer les données
    return $this->participationModel->getParticipationsByStudentId($studentId);
}

public function getParticipationById($id_participation, $id_etudiant) {
    try {
        $db = DB::getConnexion();
        $query = "SELECT p.*, e.titre FROM participation p
                 JOIN evenement e ON p.id_evenement = e.id_evenement
                 WHERE p.id_participation = :id_participation 
                 AND p.id_etudiant = :id_etudiant";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':id_participation' => $id_participation,
            ':id_etudiant' => $id_etudiant
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Erreur de base de données: " . $e->getMessage());
    }
}

/////////
public function getParticipationsByEvent($eventId) {
    $sql = "SELECT p.* FROM participation p WHERE p.id_evenement = :event_id";
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function updateParticipation($id_participation, $newData) {
    try {
        // Validation des données
        if (!isset($id_participation) || !is_numeric($id_participation)) {
            throw new Exception("ID de participation invalide");
        }

        // Connexion à la base de données
        $db = DB::getConnexion();
        
        // Préparation de la requête
        $query = "UPDATE participation SET ";
        $params = [];
        $updates = [];
        
        // Construction dynamique de la requête en fonction des données fournies
        if (isset($newData['statut'])) {
            $validStatuses = ['en_attente', 'confirme', 'annulé'];
            if (!in_array($newData['statut'], $validStatuses)) {
                throw new Exception("Statut invalide");
            }
            $updates[] = "statut = :statut";
            $params[':statut'] = $newData['statut'];
        }
        
        // Ajoutez d'autres champs modifiables ici
        // Exemple pour un champ 'commentaire':
        if (isset($newData['commentaire'])) {
            $updates[] = "commentaire = :commentaire";
            $params[':commentaire'] = $newData['commentaire'];
        }
        
        // Si aucun champ valide à mettre à jour
        if (empty($updates)) {
            throw new Exception("Aucune donnée valide à mettre à jour");
        }
        
        $query .= implode(", ", $updates);
        $query .= " WHERE id_participation = :id_participation";
        $params[':id_participation'] = $id_participation;
        
        // Exécution de la requête
        $stmt = $db->prepare($query);
        $success = $stmt->execute($params);
        
        if (!$success) {
            throw new Exception("Erreur lors de la mise à jour de la participation");
        }
        
        return true;
    } catch (PDOException $e) {
        throw new Exception("Erreur de base de données: " . $e->getMessage());
    }
}

public function createParticipation($data) {
    try {
        // Validation des données requises
        $requiredFields = ['id_etudiant', 'id_evenement'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Le champ $field est requis");
            }
        }
        
        // Validation supplémentaire
        if (!is_numeric($data['id_etudiant']) || !is_numeric($data['id_evenement'])) {
            throw new Exception("ID étudiant ou événement invalide");
        }

        
        // Connexion à la base de données
        $db = DB::getConnexion();
        
        // Vérifier si l'étudiant est déjà inscrit à cet événement
        $checkQuery = "SELECT id_participation FROM participation 
                      WHERE id_etudiant = :id_etudiant 
                      AND id_evenement = :id_evenement";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute([
            ':id_etudiant' => $data['id_etudiant'],
            ':id_evenement' => $data['id_evenement']
        ]);
        
        if ($checkStmt->fetch()) {
            throw new Exception("L'étudiant est déjà inscrit à cet événement");
        }
        
        // Valeurs par défaut
        $defaultValues = [
            'statut' => '',
            'date_inscription' => date('Y-m-d H:i:s'),
            'commentaire' => null
        ];
        
        // Fusion des dconfirméonnées avec les valeurs par défaut
        $insertData = array_merge($defaultValues, $data);
        
        // Préparation de la requête
        $query = "INSERT INTO participation
                 (id_etudiant, id_evenement, email, statut, date_inscription, commentaire) 
                 VALUES 
                 (:id_etudiant, :id_evenement, :email, :statut, :date_inscription, :commentaire)";
        
        // Exécution de la requête
        $stmt = $db->prepare($query);
        $success = $stmt->execute([
            ':id_etudiant' => $insertData['id_etudiant'],
            ':id_evenement' => $insertData['id_evenement'],
            ':email' => $insertData['email'],
            ':statut' => $insertData['statut'],
            ':date_inscription' => $insertData['date_inscription'],
            ':commentaire' => $insertData['commentaire']
        ]);
        
        if (!$success) {
            throw new Exception("Erreur lors de la création de la participation");
        }
        
        return $db->lastInsertId();
    } catch (PDOException $e) {
        throw new Exception("Erreur de base de données: " . $e->getMessage());
    }
}
// La méthode que vous avez déjà créée est parfaite :
public function getAvailableEvents($id_etudiant) {
    try {
        if (!is_numeric($id_etudiant)) {
            throw new InvalidArgumentException("ID étudiant invalide");
        }
        
        $events = $this->participationModel->getAvailableEventsForStudent($id_etudiant);
        
        // Vous pouvez ajouter un log pour le débogage
        error_log("Événements trouvés: " . count($events));
        
        return $events;
        
    } catch (Exception $e) {
        // Journalisation et gestion propre de l'erreur
        error_log("Erreur dans getAvailableEvents: " . $e->getMessage());
        
        // Retourne un tableau vide en cas d'erreur (ou lancez l'exception)
        return [];
    }
}
public function isStudentRegistered($id_etudiant, $id_evenement) {
    $sql = "SELECT COUNT(*) FROM participation WHERE id_etudiant = ? AND id_evenement = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id_etudiant, $id_evenement]);
    return $stmt->fetchColumn() > 0;
}

public function checkConfirmedParticipation($studentId, $eventId) {
    $sql = "SELECT statut FROM participation WHERE id_etudiant = ? AND id_evenement = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$studentId, $eventId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return ($result && $result['statut'] === 'confirme');
}
public function getParticipationStatus($studentId, $eventId) {
    return $this->participationModel->getParticipationStatus($studentId, $eventId);
}

/////reminder
public function getUpcomingEventsReminders($studentId) {
    if (!is_numeric($studentId)) {
        throw new InvalidArgumentException('Student ID must be numeric');
    }
    
    return $this->participationModel->getUpcomingParticipations($studentId);
}
}