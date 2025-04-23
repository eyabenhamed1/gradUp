<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../model/Produit.php");

class ProduitFront
{
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    /**
     * Récupère la liste des produits avec gestion des images
     * @return array Liste des produits avec leurs chemins d'images vérifiés
     */
    public function listeProduits() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM produit ORDER BY name DESC");
            $query->execute();
            $produits = $query->fetchAll(PDO::FETCH_ASSOC);

            if (empty($produits)) {
                error_log("Aucun produit trouvé dans la base de données");
                return [];
            }

            foreach ($produits as &$produit) {
                $produit['image_path'] = $this->getVerifiedImagePath($produit['image']);
            }

            return $produits;
        } catch (PDOException $e) {
            error_log("Erreur dans listeProduits: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère un produit spécifique par son ID
     * @param int $id_produit ID du produit à récupérer
     * @return array|null Données du produit ou null si non trouvé
     */
    public function getProduit($id_produit) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM produit WHERE id_produit = :id");
            $query->execute([':id' => $id_produit]);
            $produit = $query->fetch(PDO::FETCH_ASSOC);

            if ($produit) {
                $produit['image_path'] = $this->getVerifiedImagePath($produit['image']);
            }

            return $produit;
        } catch (PDOException $e) {
            error_log("Erreur dans getProduit: " . $e->getMessage());
            return null;
        }
    }/**
     * Vérifie et retourne le chemin valide d'une image
     * @param string $imageName Nom du fichier image
     * @return string Chemin vérifié ou URL d'image par défaut
     */
    private function getVerifiedImagePath($imageName) {
        if (empty($imageName)) {
            return 'https://via.placeholder.com/280x230?text=Image+Indisponible';
        }// Chemin relatif depuis le front office
        $relativePath = '../../back office/uploads/' . $imageName; // Chemin physique absolu
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/projetweb2A/back office/uploads/' . $imageName;

        if (file_exists($absolutePath)) {
            return $relativePath;
        } else {
            error_log("Image non trouvée: " . $absolutePath);
            return 'https://via.placeholder.com/280x230?text=Image+Indisponible';
        }
    }
}?>