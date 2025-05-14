<?php
// Démarrer la session en premier
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if user is not logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
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
            $commande->setIdUser($_SESSION['user']['id']);
            
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
    :root {
        --primary: #2c3e50;
        --primary-light: #34495e;
        --primary-dark: #1a252f;
        --secondary: #7f8c8d;
        --accent: #e74c3c;
        --light: #ecf0f1;
        --light-gray: #bdc3c7;
        --medium-gray: #95a5a6;
        --dark: #2c3e50;
        --dark-gray: #34495e;
        --white: #ffffff;
        --black: #000000;
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--light);
        color: var(--dark);
        line-height: 1.6;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .page-title {
        font-size: 2rem;
        margin-bottom: 2rem;
        color: var(--dark);
        position: relative;
        padding-bottom: 0.5rem;
        text-align: center;
        font-weight: 600;
    }
    
    .page-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background-color: var(--accent);
        border-radius: 2px;
    }
    
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        border: none;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        background-color: var(--primary);
        color: white;
        padding: 1rem 1.5rem;
        border-bottom: none;
    }
    
    .card-header h3 {
        margin: 0;
        font-size: 1.5rem;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    
    .table th {
        background-color: var(--primary);
        color: var(--white);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
        padding: 12px 15px;
        border-bottom: 2px solid var(--primary-dark);
    }
    
    .table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid var(--light-gray);
    }
    
    .table tr:nth-child(even) {
        background-color: rgba(236, 240, 241, 0.5);
    }
    
    .table tr:hover {
        background-color: rgba(52, 152, 219, 0.1);
    }
    
    .cart-item-img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border-radius: 4px;
        background-color: var(--light);
        padding: 5px;
        border: 1px solid var(--light-gray);
    }
    
    .quantity-input {
        width: 60px;
        text-align: center;
        border: 1px solid var(--light-gray);
        border-radius: 4px;
        padding: 5px;
    }
    
    .btn-quantity {
        width: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--light);
        border: 1px solid var(--light-gray);
        color: var(--dark);
    }
    
    .btn-quantity:hover {
        background-color: var(--light-gray);
    }
    
    .remove-item {
        color: var(--accent);
        cursor: pointer;
        transition: var(--transition);
        font-size: 1.1rem;
    }
    
    .remove-item:hover {
        color: #c0392b;
        transform: scale(1.1);
    }
    
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: var(--transition);
        font-weight: 500;
    }
    
    .btn-primary {
        background-color: var(--primary);
        color: var(--white);
    }
    
    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .btn-lg {
        padding: 12px 24px;
        font-size: 1.1rem;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid var(--light-gray);
        border-radius: 4px;
        font-family: 'Poppins', sans-serif;
        transition: var(--transition);
        margin-bottom: 5px;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(44, 62, 80, 0.2);
    }
    
    .invalid-feedback {
        color: var(--accent);
        font-size: 0.875em;
        margin-top: -5px;
        margin-bottom: 10px;
    }
    
    .was-validated .form-control:invalid, 
    .was-validated .form-control:invalid:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 0.25rem rgba(231, 76, 60, 0.25);
    }
    
    .text-muted {
        color: var(--medium-gray) !important;
        font-size: 0.85rem;
    }
    
    .text-end {
        text-align: right !important;
    }
    
    .fw-bold {
        font-weight: 600 !important;
    }
    
    .text-primary {
        color: var(--primary) !important;
    }
    
    .py-5 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    
    .py-3 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    .me-2 {
        margin-right: 0.5rem !important;
    }
    
    .w-100 {
        width: 100% !important;
    }
    
    .d-flex {
        display: flex !important;
    }
    
    .align-items-center {
        align-items: center !important;
    }
    
    .justify-content-center {
        justify-content: center !important;
    }
    
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            width: 100%;
            margin-bottom: 1rem;
            overflow-y: hidden;
            -ms-overflow-style: -ms-autohiding-scrollbar;
            border: 1px solid var(--light-gray);
        }
        
        .page-title {
            font-size: 1.5rem;
        }
        
        .card-header h3 {
            font-size: 1.2rem;
        }
        
        .table th, 
        .table td {
            padding: 8px 10px;
            font-size: 0.9rem;
        }
        
        .cart-item-img {
            width: 60px;
            height: 60px;
        }
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
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&remove=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Erreur de suppression');
        });
    }
}

// Validation personnalisée
function validateForm() {
    const form = document.getElementById('orderForm');
    let isValid = true;
    
    // Validation du nom
    const nom = form.querySelector('#nom');
    if (!/^[A-Za-zÀ-ÿ\s\-']{2,50}$/.test(nom.value.trim())) {
        nom.classList.add('is-invalid');
        isValid = false;
    } else {
        nom.classList.remove('is-invalid');
        nom.classList.add('is-valid');
    }
    
    // Validation du prénom
    const prenom = form.querySelector('#prenom');
    if (!/^[A-Za-zÀ-ÿ\s\-']{2,50}$/.test(prenom.value.trim())) {
        prenom.classList.add('is-invalid');
        isValid = false;
    } else {
        prenom.classList.remove('is-invalid');
        prenom.classList.add('is-valid');
    }
    
    // Validation du téléphone
    const tel = form.querySelector('#tel');
    if (!/^[24579]\d{7}$/.test(tel.value.trim())) {
        tel.classList.add('is-invalid');
        isValid = false;
    } else {
        tel.classList.remove('is-invalid');
        tel.classList.add('is-valid');
    }
    
    // Validation de l'adresse
    const adresse = form.querySelector('#adresse');
    if (adresse.value.trim().length < 10 || adresse.value.trim().length > 255) {
        adresse.classList.add('is-invalid');
        isValid = false;
    } else {
        adresse.classList.remove('is-invalid');
        adresse.classList.add('is-valid');
    }
    
    return isValid;
}

// Gestion de la soumission du formulaire
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        return false;
    }
    
    const formData = new FormData(this);
    formData.append('commander', '1');
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            const errors = data.errors.join('<br>');
            alert('Erreurs :\n' + errors.replace(/<br>/g, '\n'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la soumission du formulaire.');
    });
});

// Validation en temps réel
document.querySelectorAll('#orderForm input, #orderForm textarea').forEach(input => {
    input.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            validateForm();
        }
    });
});

// Gestion des boutons + et - du panier
document.querySelectorAll('.plus-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.closest('tr').dataset.id;
        updateQuantity(productId, 1);
    });
});

document.querySelectorAll('.minus-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.closest('tr').dataset.id;
        updateQuantity(productId, -1);
    });
});

document.querySelectorAll('.remove-item').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.dataset.id;
        removeItem(productId);
    });
});
</script>
</body>
</html>