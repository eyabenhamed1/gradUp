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
    public function getAverageRating($productId) {
        try {
            $query = $this->pdo->prepare("SELECT AVG(note) as average FROM avis WHERE id_produit = :productId");
            $query->execute([':productId' => $productId]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['average'] ? round($result['average'], 1) : 0;
        } catch (PDOException $e) {
            error_log("Erreur dans getAverageRating: " . $e->getMessage());
            return 0;
        }
    }
    
    public function saveRating($productId, $rating) {
        try {
            $query = $this->pdo->prepare("INSERT INTO avis (id_produit, note) VALUES (:productId, :rating)");
            $query->execute([
                ':productId' => $productId,
                ':rating' => $rating
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur dans saveRating: " . $e->getMessage());
            return false;
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
    public function listeProduitsByRatingPagination($offset = 0, $limit = 8) {
        try {
            $query = $this->pdo->prepare("
                SELECT p.*, COALESCE(AVG(a.note), 0) as average_rating 
                FROM produit p
                LEFT JOIN avis a ON p.id_produit = a.id_produit
                GROUP BY p.id_produit
                ORDER BY average_rating DESC, p.name ASC
                LIMIT :offset, :limit
            ");
            $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            $produits = $query->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($produits as &$produit) {
                $produit['image_path'] = $this->getVerifiedImagePath($produit['image']);
            }
    
            return $produits;
        } catch (PDOException $e) {
            error_log("Erreur dans listeProduitsByRatingPagination: " . $e->getMessage());
            return [];
        }
    }
    
    public function listeProduitsByCategoryAndRatingPagination($category, $offset = 0, $limit = 8) {
        try {
            $query = $this->pdo->prepare("
                SELECT p.*, COALESCE(AVG(a.note), 0) as average_rating 
                FROM produit p
                LEFT JOIN avis a ON p.id_produit = a.id_produit
                WHERE p.categorie = :category
                GROUP BY p.id_produit
                ORDER BY average_rating DESC, p.name ASC
                LIMIT :offset, :limit
            ");
            $query->bindValue(':category', $category, PDO::PARAM_STR);
            $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            $produits = $query->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($produits as &$produit) {
                $produit['image_path'] = $this->getVerifiedImagePath($produit['image']);
            }
    
            return $produits;
        } catch (PDOException $e) {
            error_log("Erreur dans listeProduitsByCategoryAndRatingPagination: " . $e->getMessage());
            return [];
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
    public function countProduitsByCategory($category) {
        try {
            $query = $this->pdo->prepare("SELECT COUNT(*) as total FROM produit WHERE categorie = :category");
            $query->execute([':category' => $category]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Erreur dans countProduitsByCategory: " . $e->getMessage());
            return 0;
        }
    }
    
    public function listeProduitsByCategoryPagination($category, $offset = 0, $limit = 8) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM produit WHERE categorie = :category ORDER BY name DESC LIMIT :offset, :limit");
            $query->bindValue(':category', $category, PDO::PARAM_STR);
            $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            $produits = $query->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($produits as &$produit) {
                $produit['image_path'] = $this->getVerifiedImagePath($produit['image']);
            }
    
            return $produits;
        } catch (PDOException $e) {
            error_log("Erreur dans listeProduitsByCategoryPagination: " . $e->getMessage());
            return [];
        }
    }
}
