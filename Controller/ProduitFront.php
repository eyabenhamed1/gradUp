<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../model/Produit.php");

class ProduitFront
{
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function listeProduitsPagination($offset = 0, $limit = 8) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM produit ORDER BY name DESC LIMIT :offset, :limit");
            $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            $produits = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($produits as &$produit) {
                $produit['image_path'] = $this->getVerifiedImagePath($produit['image']);
            }

            return $produits;
        } catch (PDOException $e) {
            error_log("Erreur dans listeProduitsPagination: " . $e->getMessage());
            return [];
        }
    }
    public function countProduits() {
        try {
            $query = $this->pdo->prepare("SELECT COUNT(*) as total FROM produit");
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Erreur dans countProduits: " . $e->getMessage());
            return 0;
        }
    }

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
    }

    //
    private function getVerifiedImagePath($imageName) {
        if (empty($imageName)) {
            return 'https://via.placeholder.com/280x230?text=Image+Indisponible';
        }

        $relativePath = '../../View/Backoffice/material-dashboard-master/uploads/' . $imageName;
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/' . $imageName;

        if (file_exists($absolutePath)) {
            return $relativePath;
        } else {
            error_log("Image non trouvée: " . $absolutePath);
            return 'https://via.placeholder.com/280x230?text=Image+Indisponible';
        }
    }
    public function getProductsForCart($productIds) {
        if (empty($productIds)) return [];

        try {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $sql = "SELECT id_produit, name, prix, image FROM produit WHERE id_produit IN ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($productIds);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as &$product) {
                $product['image'] = $this->getVerifiedImagePath($product['image']);
            }

            return $products;
        } catch (PDOException $e) {
            error_log("Erreur dans getProductsForCart: " . $e->getMessage());
            return [];
        }
    }
}
