<?php
require_once(__DIR__ . "/../Config.php");
require_once(__DIR__ . "/../Model/cadeau.php");

class CadeauController {

    private $conn;

    public function __construct() {
        // Initialiser la connexion à la base de données
        $this->conn = config::getConnexion();
    }

    // Afficher tous les cadeaux
    public function afficherCadeaux() {
        $cadeauModel = new Cadeau();
        $cadeaux = $cadeauModel->listeCadeaux();

        // Passer les données à la vue
        require_once(__DIR__ . '/../View/Frontoffice/index.php');
    }

    // Créer un cadeau
    public function createCadeau($type_cadeau, $date_cadeau, $id, $image)
    {
        $cadeau = new Cadeau(null, $type_cadeau, $date_cadeau, $id, $image);
        $cadeau->save();
    }

    public function getCadeauById($id_cadeau) {
        // Assurer que la connexion est correctement initialisée
        $stmt = $this->conn->prepare("SELECT * FROM cadeau WHERE id_cadeau = ?");
        $stmt->execute([$id_cadeau]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateCadeau($id_cadeau, $type, $date, $image = null) {
        // Assurer que la connexion est correctement initialisée
        $stmt = $this->conn->prepare("UPDATE cadeau SET type_cadeau = ?, date_cadeau = ?, image = ? WHERE id_cadeau = ?");
        return $stmt->execute([$type, $date, $image, $id_cadeau]);
    }

    // Supprimer un cadeau
    public function deleteCadeau($id_cadeau) {
        // Appel correct à la méthode statique delete() avec l'argument
        return Cadeau::delete($id_cadeau);
    }

    public function listeCadeaux() {
        try {
            // Utilisation de la connexion via config
            $query = $this->conn->prepare("SELECT * FROM cadeau ORDER BY date_cadeau DESC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

}
?>