<?php
// Démarrer la session en PREMIER
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Commande.php');

$produitFront = new ProduitFront();
$panierDetails = [];
$total = 0;
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Commande.php');require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Commande.php');

$produitFront = new ProduitFront();
$panierDetails = [];
$total = 0;

// Récupérer le panier depuis la session
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $item) {
        $product = $produitFront->getProduit($productId);
        if ($product) {
            $itemTotal = $product['prix'] * $item['quantity'];
            $total += $itemTotal;
            
            $panierDetails[] = [
                'id' => $productId,
                'name' => $product['name'],
                'price' => $product['prix'],
                'quantity' => $item['quantity'],
                'total' => $itemTotal
            ];
        }
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commander'])) {
    // Validation
    $errors = [];
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');

    if (empty($nom)) $errors[] = "Nom requis";
    if (empty($prenom)) $errors[] = "Prénom requis";
    if (empty($tel) || !preg_match('/^[2579]\d{7}$/', $tel)) $errors[] = "Téléphone invalide";
    if (empty($adresse)) $errors[] = "Adresse requise";
    if (empty($panierDetails)) $errors[] = "Panier vide";

    if (empty($errors)) {
        try {
            // Préparer les produits au format JSON
            $produitsJSON = json_encode($panierDetails);
            
            // Créer et enregistrer la commande
            $commande = new Commande();
            $commande->setNom($nom);
            $commande->setPrenom($prenom);
            $commande->setTlf($tel);
            $commande->setAdresse($adresse);
            $commande->setProduits($produitsJSON);
            $commande->setPrixTotal($total);
            $commande->setEtat('en cours');
            
            $commandeId = $commande->save();
            
            if ($commandeId) {
                // Vider le panier et rediriger
                unset($_SESSION['cart']);
                $_SESSION['flash_success'] = "Commande #$commandeId validée!";
                header('Location: confirmation.php');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = "Erreur: " . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['flash_error'] = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commander</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4">Formulaire de Commande</h2>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['flash_error'] ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Votre Panier</h3>
            </div>
            <div class="card-body">
                <?php if (empty($panierDetails)): ?>
                    <p class="text-center">Votre panier est vide</p>
                <?php else: ?>
                    <?php foreach ($panierDetails as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                                <small><?= number_format($item['price'], 2) ?> DT × <?= $item['quantity'] ?></small>
                            </div>
                            <span class="fw-bold"><?= number_format($item['total'], 2) ?> DT</span>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <h4 class="mb-0">Total</h4>
                        <h4 class="mb-0 text-primary"><?= number_format($total, 2) ?> DT</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <form method="POST" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="tel" class="form-label">Téléphone</label>
                    <input type="tel" class="form-control" id="tel" name="tel" value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>" pattern="[2579]\d{7}" required>
                </div>
                
                <div class="col-12">
                    <label for="adresse" class="form-label">Adresse</label>
                    <textarea class="form-control" id="adresse" name="adresse" rows="3" required><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" name="commander" class="btn btn-primary btn-lg w-100">
                        Valider la commande
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Validation côté client
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html>