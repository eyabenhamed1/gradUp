<?php
class Evenement {
    private $db;  // Déclaration explicite de la propriété

    public function __construct() {
        // Initialisation robuste de la connexion
        try {
            $this->db = new PDO(
                "mysql:host=localhost;dbname=projetweb2a", 
                "root", 
                "", 
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException("Erreur de connexion DB: " . $e->getMessage());
        }
    }

    public function getEvenementsRecents($limit) {
        if (!$this->db) {
            throw new RuntimeException("Connexion DB non initialisée");
        }

        try {
            $sql = "SELECT * FROM evenement 
                    WHERE date_evenement >= CURDATE()
                    ORDER BY date_evenement ASC
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new RuntimeException("Erreur de requête: " . $e->getMessage());
        }
    }


    // Getters
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getDateEvenement() { return $this->date_evenement; }
    public function getLieu() { return $this->lieu; }
    public function getTypeEvenement() { return $this->type_evenement; }
    public function getImage() { return $this->image; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setDateEvenement($date_evenement) { $this->date_evenement = $date_evenement; }
    public function setLieu($lieu) { $this->lieu = $lieu; }
    public function setTypeEvenement($type_evenement) { $this->type_evenement = $type_evenement; }
    public function setImage($image) { $this->image = $image; }
}
?>