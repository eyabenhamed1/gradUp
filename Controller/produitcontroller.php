<?php
require_once(__DIR__ . "/../Config.php");
require_once(__DIR__ . "/../model/Produit.php");

class ProduiController {

    // Créer un produit
    public function createProduit($name, $description, $prix, $stock, $image, $categorie) {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("INSERT INTO produit (name, description, prix, stock, image, categorie) 
                                   VALUES (:name, :description, :prix, :stock, :image, :categorie)");
            $query->bindParam(':name', $name);
            $query->bindParam(':description', $description);
            $query->bindParam(':prix', $prix);
            $query->bindParam(':stock', $stock);
            $query->bindParam(':image', $image);
            $query->bindParam(':categorie', $categorie);
            
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
    public function updateProduit($id, $name, $description, $prix, $stock, $image, $categorie) {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("UPDATE produit 
                                   SET name = :name, description = :description, 
                                       prix = :prix, stock = :stock, 
                                       image = :image, categorie = :categorie
                                   WHERE id_produit = :id");
            $query->bindParam(':id', $id);
            $query->bindParam(':name', $name);
            $query->bindParam(':description', $description);
            $query->bindParam(':prix', $prix);
            $query->bindParam(':stock', $stock);
            $query->bindParam(':image', $image);
            $query->bindParam(':categorie', $categorie);
            
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
