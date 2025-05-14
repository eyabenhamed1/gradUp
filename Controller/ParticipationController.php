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
     * @param int $id_utilisateur
     * @param string $email
     * @param string $statut
     * @param string $comments
     * @param string $nom
     * @param string $telephone
     * @return bool
     * @throws Exception
     */
     
     public function inscrireParticipant($eventId, $id_utilisateur, $email, $statut = 'confirme', $comments = '', $nom = '', $telephone = '') {
        // Validation des entrées
        if (!is_numeric($eventId) || $eventId <= 0) {
            throw new InvalidArgumentException('ID événement invalide');
        }
        
        if (!is_numeric($id_utilisateur) || $id_utilisateur <= 0) {
            throw new InvalidArgumentException('ID étudiant invalide');
        }
    
        // Vérifier si une inscription existe déjà (même annulée)
        $existingParticipation = $this->participationModel->getParticipationStatus($id_utilisateur, $eventId);
        
        if ($existingParticipation === 'confirme') {
            throw new Exception('Vous êtes déjà inscrit à cet événement');
        }
        
        // Si l'inscription existe et est annulée, on la met à jour
        if ($existingParticipation === 'annule') {
            $result = $this->participationModel->updateParticipationStatus($id_utilisateur, $eventId, 'confirme');
            if ($result) {
                $_SESSION['success_message'] = "Votre réinscription a été effectuée avec succès !";
                return true;
            }
        }
        
        // Création d'une nouvelle participation avec la date actuelle
        $result = $this->participationModel->creerParticipation([
            'id_evenement' => (int)$eventId,
            'id_utilisateur' => (int)$id_utilisateur,
            'email' => $email,
            'statut' => $statut,
            'commentaire' => $comments,
            'telephone' => $telephone,
            'date_inscription' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            $_SESSION['success_message'] = "Votre inscription a été effectuée avec succès !";
        }
        
        return $result;
    }
     public function countParticipants($eventId) {
        return $this->participationModel->countParticipantsByEvent($eventId);
    } 
/////
public function getParticipationsByUser($id_utilisateur) {
    $sql = "SELECT * FROM participation WHERE id_utilisateur = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function annulerParticipationByUserAndEvent($studentId, $participationId) {
    try {
        // 1. Vérifier d'abord si la participation appartient bien à l'étudiant
        $participation = $this->participationModel->getParticipationById($participationId, $studentId);
        
        if (!$participation) {
            throw new Exception("Participation non trouvée ou non autorisée");
        }

        // 2. Vérifier si l'annulation est possible (statut approprié)
        if ($participation['statut'] !== 'confirme') {
            throw new Exception("Seules les inscriptions confirmées peuvent être annulées");
        }

        // 3. Procéder à l'annulation
        return $this->participationModel->annulerParticipation($participationId, $studentId);
        
    } catch (PDOException $e) {
        error_log("Erreur DB: " . $e->getMessage());
        throw new Exception("Erreur technique lors de l'annulation");
    }
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
    
    try {
        return $this->participationModel->getParticipationsByStudentId($studentId);
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la récupération des participations: " . $e->getMessage());
    }
}

public function getParticipationById($id_participation, $id_utilisateur) {
    try {
        if (!is_numeric($id_participation) || !is_numeric($id_utilisateur)) {
            throw new InvalidArgumentException("IDs invalides");
        }
        
        $participation = $this->participationModel->getParticipationById($id_participation, $id_utilisateur);
        if (!$participation) {
            throw new Exception("Participation non trouvée");
        }
        
        return $participation;
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
        require_once(__DIR__ . '/../Config.php');
        $db = config::getConnexion();
        
        // Préparation de la requête
        $query = "UPDATE participation SET ";
        $params = [];
        $updates = [];
        
        // Construction dynamique de la requête en fonction des données fournies
        if (isset($newData['statut'])) {
            $updates[] = "statut = :statut";
            $params[':statut'] = $newData['statut'];
        }
        
        if (isset($newData['commentaire'])) {
            $updates[] = "commentaire = :commentaire";
            $params[':commentaire'] = $newData['commentaire'];
        }
        
        // Si aucun champ à mettre à jour
        if (empty($updates)) {
            return true; // Rien à mettre à jour
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
        error_log("Erreur PDO dans updateParticipation: " . $e->getMessage());
        throw new Exception("Erreur lors de la mise à jour de la participation");
    } catch (Exception $e) {
        error_log("Erreur dans updateParticipation: " . $e->getMessage());
        throw $e;
    }
}

public function createParticipation($data) {
    try {
        // Validation des données requises
        if (!isset($data['id']) || !isset($data['id_evenement'])) {
            throw new InvalidArgumentException('ID utilisateur et ID événement sont requis');
        }

        // Configuration du fuseau horaire
        date_default_timezone_set('Europe/Paris');

        // Préparation des données pour le modèle
        $participationData = [
            'id_evenement' => $data['id_evenement'],
            'id_utilisateur' => $data['id'],
            'email' => $_SESSION['user']['email'],
            'statut' => $data['statut'] ?? 'en_attente',
            'date_inscription' => date('Y-m-d H:i:s'),
            'commentaire' => $data['commentaire'] ?? null,
            'telephone' => $data['telephone'] ?? $_SESSION['user']['telephone'] ?? null // Ajout du téléphone
        ];

        // Vérification si l'utilisateur est déjà inscrit
        if ($this->participationModel->estDejaInscrit($participationData['id_evenement'], $participationData['id_utilisateur'])) {
            throw new Exception('Vous êtes déjà inscrit à cet événement');
        }

        // Création de la participation
        $result = $this->participationModel->creerParticipation($participationData);
        
        if (!$result) {
            throw new Exception('Erreur lors de la création de la participation');
        }

        return $result;
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}
// La méthode que vous avez déjà créée est parfaite :
public function getAvailableEvents($id_utilisateur) {
    try {
        if (!is_numeric($id_utilisateur)) {
            throw new InvalidArgumentException("ID étudiant invalide");
        }
        
        $events = $this->participationModel->getAvailableEventsForStudent($id_utilisateur);
        return $events;
        
    } catch (Exception $e) {
        error_log("Erreur dans getAvailableEvents: " . $e->getMessage());
        throw new Exception("Erreur lors de la récupération des événements disponibles");
    }
}
public function isStudentRegistered($id_utilisateur, $id_evenement) {
    $sql = "SELECT COUNT(*) FROM participation WHERE id_utilisateur = ? AND id_evenement = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id_utilisateur, $id_evenement]);
    return $stmt->fetchColumn() > 0;
}

public function checkConfirmedParticipation($studentId, $eventId) {
    $sql = "SELECT statut FROM participation WHERE id_utilisateur = ? AND id_evenement = ?";
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
    // Validation plus stricte
    if (!isset($studentId) || !is_numeric($studentId)) {
        throw new InvalidArgumentException('ID étudiant invalide ou manquant');
    }
    
    // Vérifier que l'ID est positif
    if ($studentId <= 0) {
        throw new InvalidArgumentException('ID étudiant doit être positif');
    }
    
    return $this->participationModel->getUpcomingParticipations($studentId);
}
}