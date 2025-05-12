<?php
// Démarrer la session en premier
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../../Controller/ProduitFront.php');
require_once(__DIR__ . '/../../Model/Commande.php');

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
                'id_produit' => $productId,
                'name' => $product['name'],
                'price' => $product['prix'],
                'quantity' => $item['quantity'],
                'total' => $itemTotal,
                'image' => $product['image_path'] ?? 'https://via.placeholder.com/50?text=Image+Indisponible'
            ];
        }
    }
}

// Traitement du formulaire de commande
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
            
            if ($produitsJSON === false) {
                throw new Exception("Erreur lors de l'encodage des produits en JSON");
            }
            
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
                // Vider le panier
                unset($_SESSION['cart']);
                
                // Préparer la réponse JSON
                $response = [
                    'success' => true,
                    'message' => "Votre commande #$commandeId a été validée avec succès!",
                    'redirect' => 'essaiee.php?order_success=1&id='.$commandeId
                ];
                
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                throw new Exception("Erreur lors de l'enregistrement de la commande");
            }
        } catch (Exception $e) {
            $errors[] = "Erreur: " . $e->getMessage();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit;
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commander - Gradup Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 4px;
            background-color: #f8f9fa;
            padding: 5px;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
        }
        .btn-quantity {
            width: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remove-item {
            color: #dc3545;
            cursor: pointer;
            transition: color 0.2s;
        }
        .remove-item:hover {
            color: #bb2d3b;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            background-color: #2c3e50;
            color: white;
        }
        .btn-primary {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }
        .btn-primary:hover {
            background-color: #1a252f;
            border-color: #1a252f;
        }
        .table th {
            border-top: none;
            background-color: #f8f9fa;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875em;
        }
        .was-validated .form-control:invalid, .was-validated .form-control:invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220,53,69,.25);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4">Finaliser votre commande</h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h3 class="mb-0">Récapitulatif de votre panier</h3>
            </div>
            <div class="card-body">
                <?php if (empty($panierDetails)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5>Votre panier est vide</h5>
                        <a href="essaiee.php" class="btn btn-primary mt-3">Retour à la boutique</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($panierDetails as $item): ?>
                                    <tr data-id="<?= $item['id_produit'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($item['image']) ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                                     class="cart-item-img me-3"
                                                     onerror="this.src='https://via.placeholder.com/80?text=Image+Indisponible'">
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle"><?= number_format($item['price'], 2) ?> DT</td>
                                        <td class="align-middle">
                                            <div class="d-flex">
                                                <input type="hidden" name="product_id" value="<?= $item['id_produit'] ?>">
                                                <button type="button" class="btn btn-outline-secondary btn-quantity minus-btn">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <span class="form-control quantity-input mx-1 text-center"><?= $item['quantity'] ?></span>
                                                <button type="button" class="btn btn-outline-secondary btn-quantity plus-btn">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="align-middle"><?= number_format($item['total'], 2) ?> DT</td>
                                        <td class="align-middle">
                                            <a href="#" class="remove-item" data-id="<?= $item['id_produit'] ?>" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                    <td colspan="2" class="fw-bold text-primary"><?= number_format($total, 2) ?> DT</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($panierDetails)): ?>
       <!-- Remplacez la partie formulaire par ce code corrigé -->
<?php if (!empty($panierDetails)): ?>
<form method="POST" id="orderForm" class="needs-validation" novalidate>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" class="form-control" name="nom" id="nom" 
                       required pattern="[A-Za-zÀ-ÿ\s\-']{2,50}"
                       title="Le nom doit contenir entre 2 et 50 caractères alphabétiques"
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                <div class="invalid-feedback">
                    Veuillez entrer un nom valide (2-50 caractères alphabétiques).
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" class="form-control" name="prenom" id="prenom"
                       required pattern="[A-Za-zÀ-ÿ\s\-']{2,50}"
                       title="Le prénom doit contenir entre 2 et 50 caractères alphabétiques"
                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                <div class="invalid-feedback">
                    Veuillez entrer un prénom valide (2-50 caractères alphabétiques).
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="tel">Téléphone *</label>
                <input type="tel" class="form-control" id="tel" name="tel" 
                       required pattern="[24579]\d{7}"
                       title="Numéro tunisien valide (8 chiffres commençant par 2,4,5,7 ou 9)"
                       value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>">
                <small class="text-muted">Format: 2xxxxxxx, 4xxxxxxx, 5xxxxxxx, 7xxxxxxx ou 9xxxxxxx</small>
                <div class="invalid-feedback">
                    Veuillez entrer un numéro de téléphone valide (8 chiffres).
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="form-group">
                <label for="adresse">Adresse complète *</label>
                <textarea class="form-control" name="adresse" id="adresse" rows="3"
                          required minlength="10" maxlength="255"
                          title="L'adresse doit contenir entre 10 et 255 caractères"><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
                <div class="invalid-feedback">
                    L'adresse doit contenir entre 10 et 255 caractères.
                </div>
            </div>
        </div>
        
        <div class="col-12 mt-4">
            <button type="submit" name="commander" class="btn btn-primary btn-lg w-100 py-3">
                <i class="fas fa-check-circle me-2"></i> Valider la commande
            </button>
        </div>
    </div>
</form>
<?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
// Fonctions pour le panier
function updateQuantity(productId, change) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity_change=${change}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
        else alert('Erreur: ' + (data.message || 'Erreur de mise à jour'));
    });
}

function removeItem(productId) {
    if (confirm('Supprimer ce produit ?')) {
        fetch('update_cart.php', {
            method: 'POST',
            body: `product_id=${productId}&remove=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Erreur de suppression');
        });
    }
}

// Gestion du formulaire - Version corrigée
document.addEventListener('submit', function(e) {
    if (e.target && e.target.id === 'orderForm') {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        fetch(e.target.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || 'confirmation.php';
            } else {
                alert(data.errors?.join('\n') || 'Erreur de commande');
            }
        });
    }
});

// Validation Bootstrap
// Validation Bootstrap
(() => {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
// Validation en temps réel
document.querySelectorAll('#orderForm input, #orderForm textarea').forEach(input => {
    input.addEventListener('input', function() {
        if (this.checkValidity()) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
    });
    
    input.addEventListener('blur', function() {
        if (!this.checkValidity()) {
            this.classList.add('is-invalid');
        }
    });
});

// Empêcher l'envoi du formulaire si invalide
document.getElementById('orderForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        
        // Afficher les messages d'erreur pour tous les champs invalides
        this.querySelectorAll(':invalid').forEach(invalidElem => {
            invalidElem.classList.add('is-invalid');
        });
    }
    
    this.classList.add('was-validated');
});
</script>
</body>
</html>