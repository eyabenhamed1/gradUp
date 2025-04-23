<?php
require_once(__DIR__ . "/../Config.php");
require_once(__DIR__ . "/../Model/commande.php");


class CommandeController {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function processOrder() {
        error_log("=== DEBUT processOrder ===");

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commander'])) {
            try {
                // Debug des données reçues
                error_log("Données POST: ".print_r($_POST, true));
                error_log("Panier session: ".print_r($_SESSION['cart'], true));

                // 1. Validation
                $requiredFields = ['nom', 'prenom', 'tel', 'adresse'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Le champ $field est requis");
                    }
                }

                // 2. Vérification panier
                if (empty($_SESSION['cart'])) {
                    throw new Exception("Votre panier est vide");
                }

                // 3. Préparation produits
                require_once($_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Controller/ProduitFront.php');
                $produitFront = new ProduitFront();

                $produits = [];
                $total = 0;

                foreach ($_SESSION['cart'] as $id => $item) {
                    $p = $produitFront->getProduit($id);
                    if (!$p) continue;

                    $produits[] = [
                        'id_produit' => $id,
                        'nom' => $p['name'],
                        'quantite' => $item['quantity'],
                        'prix_unitaire' => $p['prix']
                    ];
                    $total += $p['prix'] * $item['quantity'];
                }

                // Debug avant insertion
                error_log("Produits préparés: ".print_r($produits, true));
                error_log("Total calculé: $total");

                // 4. Insertion dans la table commande
                $sql = "INSERT INTO commande (nom, prenom, tlf, adresse, produits, prix_total) 
                        VALUES (:nom, :prenom, :tlf, :adresse, :produits, :prix_total)";

                $stmt = $this->pdo->prepare($sql);
                $produits_json = json_encode($produits);

                error_log("Requête SQL: $sql");
                error_log("Données à insérer: ".print_r([
                    ':nom' => $_POST['nom'],
                    ':prenom' => $_POST['prenom'],
                    ':tlf' => $_POST['tel'],
                    ':adresse' => $_POST['adresse'],
                    ':produits' => $produits_json,
                    ':prix_total' => $total
                ], true));

                $success = $stmt->execute([
                    ':nom' => $_POST['nom'],
                    ':prenom' => $_POST['prenom'],
                    ':tlf' => $_POST['tel'],
                    ':adresse' => $_POST['adresse'],
                    ':produits' => $produits_json,
                    ':prix_total' => $total
                ]);

                if ($success) {
                    $lastId = $this->pdo->lastInsertId();
                    error_log("Insertion réussie. ID: $lastId");

                    // Vidage du panier
                    $_SESSION['cart'] = [];
                    $_SESSION['flash_success'] = "Commande validée (N°$lastId)";

                    // 5. Mise à jour des quantités dans la table produit
                    foreach ($_SESSION['cart'] as $id => $item) {
                        // Récupérer la quantité commandée
                        $quantite_commandee = $item['quantity'];

                        // Mettre à jour la quantité du produit
                        $sqlUpdate = "UPDATE produit SET quantite = quantite - :quantite_commandee WHERE id_produit = :id_produit";
                        $stmtUpdate = $this->pdo->prepare($sqlUpdate);

                        $successUpdate = $stmtUpdate->execute([
                            ':quantite_commandee' => $quantite_commandee,
                            ':id_produit' => $id
                        ]);

                        if (!$successUpdate) {
                            $errorInfo = $stmtUpdate->errorInfo();
                            error_log("Erreur lors de la mise à jour des produits: ".print_r($errorInfo, true));
                            $_SESSION['flash_error'] = "Erreur lors de la mise à jour des quantités des produits";
                            header('Location: commander.php');
                            exit;
                        }
                    }

                    // Rediriger l'utilisateur vers la boutique avec un message de succès
                    header('Location: boutique.php');
                    exit;
                } else {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Erreur SQL: ".print_r($errorInfo, true));
                    throw new Exception("Erreur lors de l'enregistrement de la commande");
                }

            } catch (PDOException $e) {
                error_log("PDOException: ".$e->getMessage());
                $_SESSION['flash_error'] = "Erreur technique: ".$e->getMessage();
                header('Location: commander.php');
                exit;
            } catch (Exception $e) {
                error_log("Exception: ".$e->getMessage());
                $_SESSION['flash_error'] = $e->getMessage();
                header('Location: commander.php');
                exit;
            }
        }
    }
}
?>
