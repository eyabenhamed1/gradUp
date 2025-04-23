<?php
require_once(__DIR__ . "/../Config.php");
require_once(__DIR__ . "/../model/Produit.php");

class ProduiController {

    // Créer un produit
    public function createProduit($name, $description, $prix, $stock, $image) {
        $db = config::getConnexion();
        try {
            // Ajout du placeholder :image dans la clause VALUES pour insérer l'image dans la BDD
            $query = $db->prepare("INSERT INTO produit (name, description, prix, stock, image) 
                                   VALUES (:name, :description, :prix, :stock, :image)");
            $query->bindParam(':name', $name);
            $query->bindParam(':description', $description);
            $query->bindParam(':prix', $prix);
            $query->bindParam(':stock', $stock);
            $query->bindParam(':image', $image);
            
            $query->execute();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // Lire la liste des produits
    public function listeProduit() {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT * FROM produit ORDER BY name DESC");
            $query->execute();
            $produits = $query->fetchAll(PDO::FETCH_ASSOC);
            return $produits;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    // Mettre à jour un produit
    public function updateProduit($id_produit, $name, $description, $prix, $stock, $image) {
        $db = config::getConnexion();
        try {
            // Dans la mise à jour, on met aussi à jour la colonne image
            $query = $db->prepare("UPDATE produit 
                                   SET name = :name, description = :description, prix = :prix, stock = :stock, image = :image
                                   WHERE id_produit = :id_produit");
            $query->bindParam(':id_produit', $id_produit);
            $query->bindParam(':name', $name);
            $query->bindParam(':description', $description);
            $query->bindParam(':prix', $prix);
            $query->bindParam(':stock', $stock);
            $query->bindParam(':image', $image);
            
            $query->execute();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // Supprimer un produit
    public function deleteProduit($id_produit) {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
            $query->bindParam(':id_produit', $id_produit);
            $query->execute();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // Lire un produit par ID
    public function getProduitById($id_produit) {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
            $query->bindParam(':id_produit', $id_produit);
            $query->execute();
            $produit = $query->fetch(PDO::FETCH_ASSOC);
            return $produit;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return null;
        }
    }
}
?>
