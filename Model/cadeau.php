<?php
require_once(__DIR__ . "/../Config.php");

class Cadeau {
    private ?int $id_cadeau;
    private ?string $type_cadeau;
    private ?string $date_cadeau;
    private ?int $id;
    private ?string $image;

    // Constructeur modifié avec paramètres optionnels
    public function __construct(?int $id_cadeau = null, ?string $type_cadeau = null, 
                               ?string $date_cadeau = null, ?int $id = null, 
                               ?string $image = null) {
        $this->id_cadeau = $id_cadeau;
        $this->type_cadeau = $type_cadeau;
        $this->date_cadeau = $date_cadeau;
        $this->id = $id;
        $this->image = $image;
    }

    // Getters et Setters (inchangés mais avec typage nullable)
    public function getIdCadeau(): ?int {
        return $this->id_cadeau;
    }

    public function setIdCadeau(?int $id_cadeau): void {
        $this->id_cadeau = $id_cadeau;
    }

    // ... (autres getters/setters similaires)

    public function save(): bool {
        try {
            $db = Config::getConnexion();
            
            if ($this->id_cadeau === null) {
                // Insertion
                $query = $db->prepare("INSERT INTO cadeau (type_cadeau, date_cadeau, id, image) 
                                     VALUES (:type_cadeau, :date_cadeau, :id, :image)");
            } else {
                // Mise à jour
                $query = $db->prepare("UPDATE cadeau SET 
                                     type_cadeau = :type_cadeau, 
                                     date_cadeau = :date_cadeau, 
                                     id = :id, 
                                     image = :image 
                                     WHERE id_cadeau = :id_cadeau");
                $query->bindParam(':id_cadeau', $this->id_cadeau);
            }
            
            $query->bindParam(':type_cadeau', $this->type_cadeau);
            $query->bindParam(':date_cadeau', $this->date_cadeau);
            $query->bindParam(':id', $this->id);
            $query->bindParam(':image', $this->image);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la sauvegarde: " . $e->getMessage());
            return false;
        }
    }

    // Méthodes statiques pour les opérations qui ne nécessitent pas d'instance
    public static function delete(int $id_cadeau): bool {
        try {
            $db = Config::getConnexion();
            $query = $db->prepare("DELETE FROM cadeau WHERE id_cadeau = :id_cadeau");
            $query->bindParam(':id_cadeau', $id_cadeau);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression: " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(): array {
        try {
            $db = Config::getConnexion();
            $query = $db->query("SELECT * FROM cadeau ORDER BY date_cadeau DESC");
            return $query->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération: " . $e->getMessage());
            return [];
        }
    }

    public static function getById(int $id_cadeau): ?self {
        try {
            $db = Config::getConnexion();
            $query = $db->prepare("SELECT * FROM cadeau WHERE id_cadeau = :id_cadeau");
            $query->bindParam(':id_cadeau', $id_cadeau, PDO::PARAM_INT);
            $query->execute();
            
            $data = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                return new self(
                    $data['id_cadeau'],
                    $data['type_cadeau'],
                    $data['date_cadeau'],
                    $data['id'],
                    $data['image']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du cadeau: " . $e->getMessage());
            return null;
        }
    }
}
?>