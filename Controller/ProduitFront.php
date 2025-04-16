<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../model/Produit.php");

class ProduitFront
{
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function listeProduits() {
        try {
            // Exécution de la requête pour récupérer les produits
            $query = $this->pdo->prepare("SELECT * FROM produit ORDER BY name DESC");
            $query->execute();

            // Récupération des produits
            $produits = $query->fetchAll(PDO::FETCH_ASSOC);

            // Si aucun produit n'est trouvé, afficher un message
            if (empty($produits)) {
                echo "Aucun produit trouvé";
                return [];
            }

            // Boucle pour vérifier l'existence des images
            foreach ($produits as &$produit) {
                // Générer le chemin vers l'image
                $imagePath = 'View/Backoffice/uploads/' . $produit['image'];
                $physicalPath = $_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/' . $imagePath;

                // Vérifier si l'image existe et ajuster l'URL en conséquence
                if (file_exists($physicalPath)) {
                    $produit['image_path'] = $imagePath;
                } else {
                    $produit['image_path'] = 'https://via.placeholder.com/280x230?text=Image+Indisponible';
                }
            }

            return $produits;
        } catch (PDOException $e) {
            error_log("Erreur dans listeProduits: " . $e->getMessage());
            return [];
        }
    }

    public function getProduit($id_produit) {
        try {
            // Exécution de la requête pour récupérer un produit spécifique
            $query = $this->pdo->prepare("SELECT * FROM produit WHERE id_produit = :id");
            $query->execute([':id' => $id_produit]);

            // Récupération du produit
            $produit = $query->fetch(PDO::FETCH_ASSOC);

            // Vérification de l'existence de l'image
            if ($produit) {
                $produit['image_path'] = 'View/Backoffice/uploads/' . $produit['image'];
            }

            return $produit;
        } catch (PDOException $e) {
            error_log("Erreur dans getProduit: " . $e->getMessage());
            return null;
        }
    }
}
?>
