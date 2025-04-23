<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
$produitFront = new ProduitFront();
$produits = $produitFront->listeProduits();

// Gestion du panier en session
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ajouter au panier
if (isset($_GET['add_to_cart'])) {
    $productId = (int)$_GET['add_to_cart'];
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$productId] = [
            'quantity' => 1,
            'added_at' => time()
        ];
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Récupérer le nombre d'articles dans le panier
$cartCount = array_reduce($_SESSION['cart'], function($carry, $item) {
    return $carry + $item['quantity'];
}, 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique - Gradup Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container {
    max-width: 800px;
}

.card {
    border-radius: 10px;
}

.was-validated .form-control:invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: none;
    color: #dc3545;
    font-size: 0.875em;
}

.was-validated .form-control:invalid ~ .invalid-feedback {
    display: block;
}
        :root {
            --primary: #1a73e8;
            --primary-light: #4285f4;
            --primary-dark: #0d47a1;
            --secondary: #34a853;
            --danger: #ea4335;
            --warning: #fbbc05;
            --light: #f8f9fa;
            --light-gray: #e9ecef;
            --medium-gray: #adb5bd;
            --dark: #212529;
            --dark-blue: #0a2647;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--warning);
        }
        
        /* Navigation */
        .nav {
            display: flex;
            align-items: center;
        }
        
        .nav-list {
            display: flex;
            list-style: none;
        }
        
        .nav-item {
            margin-left: 1.5rem;
            position: relative;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
        }
        
        .nav-link:hover {
            color: var(--warning);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--warning);
            transition: var(--transition);
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .nav-icons {
            display: flex;
            align-items: center;
            margin-left: 2rem;
        }
        
        .nav-icon {
            color: white;
            font-size: 1.2rem;
            margin-left: 1.2rem;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            text-decoration: none;
        }
        
        .nav-icon:hover {
            color: var(--warning);
            transform: translateY(-2px);
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Main Content */
        .main {
            padding: 2rem 0;
            min-height: calc(100vh - 120px);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .page-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--primary-dark);
            position: relative;
            padding-bottom: 0.5rem;
            text-align: center;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 2px;
        }
        
        /* Product Grid */
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            padding: 20px 0;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--danger);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
        }
        
        .product-image-container {
            height: 230px;
            overflow: hidden;
            background: var(--light-gray);
            position: relative;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: var(--transition);
            padding: 20px;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 20px;
            border-top: 1px solid var(--light-gray);
        }
        
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0 0 10px;
            color: var(--dark);
        }
        
        .product-description {
            font-size: 0.85rem;
            color: var(--medium-gray);
            margin-bottom: 15px;
            line-height: 1.5;
            height: 40px;
            overflow: hidden;
        }
        
        .product-price {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        
        .current-price {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .original-price {
            color: var(--medium-gray);
            text-decoration: line-through;
            font-size: 0.9rem;
            margin-left: 8px;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        
        .product-stock {
            font-size: 0.85rem;
            color: var(--secondary);
            font-weight: 500;
        }
        
        .product-rating {
            color: var(--warning);
            font-size: 0.9rem;
        }
        
        .add-to-cart {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            text-align: center;
        }
        
        .add-to-cart:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--primary));
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark-blue);
            color: white;
            padding: 3rem 0 1.5rem;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .footer-col h3 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-link {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-link a {
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }
        
        .footer-link a:hover {
            color: var(--primary-light);
            padding-left: 5px;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-link {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .social-link:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            color: var(--medium-gray);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                padding: 1rem;
            }
            
            .nav {
                width: 100%;
                margin-top: 1rem;
                justify-content: space-between;
            }
            
            .nav-list {
                display: none;
            }
            
            .nav-icons {
                margin-left: auto;
            }
            
            .product-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .footer-container {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .product-container {
                grid-template-columns: 1fr;
            }
        }

        /* Styles pour les modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close-modal:hover {
            color: black;
        }
        
        #cartItems {
            margin: 20px 0;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item-info {
            display: flex;
            align-items: center;
        }
        
        .cart-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
        }
        
        .cart-item-quantity button {
            background: #f0f0f0;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .cart-item-quantity span {
            margin: 0 10px;
        }
        
        .cart-total {
            text-align: right;
            margin: 20px 0;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Gradup Shop
            </a>
            
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="index.php" class="nav-link">Accueil</a></li>
                    <li class="nav-item"><a href="boutique.php" class="nav-link">Boutique</a></li>
                    <li class="nav-item"><a href="macommande.php" class="nav-link">Ma Commande</a></li>
                    <li class="nav-item"><a href="promotions.php" class="nav-link">Promotions</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
                </ul>
                
                <div class="nav-icons">
                    <a href="#" class="nav-icon"><i class="fas fa-search"></i></a>
                    <a href="compte.php" class="nav-icon"><i class="fas fa-user"></i></a>
                    <a href="#" class="nav-icon" id="cartIcon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-count"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <h1 class="page-title">Notre Boutique</h1>
            
            <div class="product-container">
                <?php if (empty($produits)): ?>
                    <div style="grid-column:1/-1; text-align:center; padding:40px;">
                        <h3>Aucun produit disponible pour le moment</h3>
                    </div>
                <?php else: ?>
                    <?php foreach ($produits as $produit): ?>
                        <div class="product-card">
                            <?php if ($produit['stock'] < 5): ?>
                                <span class="product-badge">Bientôt épuisé!</span>
                            <?php endif; ?>
                            
                            <div class="product-image-container">
                                <img src="<?= htmlspecialchars(basename($produit['image_path'])) ?>" 
                                    alt="<?= htmlspecialchars($produit['name']) ?>"
                                    class="product-image"
                                    onerror="this.src='https://via.placeholder.com/280x230?text=Image+Indisponible'">
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-title"><?= htmlspecialchars($produit['name']) ?></h3>
                                <p class="product-description"><?= htmlspecialchars($produit['description']) ?></p>
                                
                                <div class="product-price">
                                    <span class="current-price"><?= number_format($produit['prix'], 2) ?> DT</span>
                                    <?php if (isset($produit['old_price']) && $produit['old_price'] > $produit['prix']): ?>
                                        <span class="original-price"><?= number_format($produit['old_price'], 2) ?> DT</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-meta">
                                    <span class="product-stock">Stock: <?= (int)$produit['stock'] ?></span>
                                    <span class="product-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </span>
                                </div>
                                
                                <a href="?add_to_cart=<?= $produit['id_produit'] ?>" class="add-to-cart">
                                    <i class="fas fa-cart-plus"></i> Ajouter au panier
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <h3>Gradup Shop</h3>
                <p>Votre boutique en ligne préférée pour des produits de qualité à des prix imbattables.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h3>Liens rapides</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="index.php">Accueil</a></li>
                    <li class="footer-link"><a href="boutique.php">Boutique</a></li>
                    <li class="footer-link"><a href="nouveautes.php">Nouveautés</a></li>
                    <li class="footer-link"><a href="promotions.php">Promotions</a></li>
                    <li class="footer-link"><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Informations</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="a-propos.php">À propos de nous</a></li>
                    <li class="footer-link"><a href="livraison.php">Livraison</a></li>
                    <li class="footer-link"><a href="retours.php">Politique de retour</a></li>
                    <li class="footer-link"><a href="conditions.php">Conditions générales</a></li>
                    <li class="footer-link"><a href="confidentialite.php">Politique de confidentialité</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Contactez-nous</h3>
                <ul class="footer-links">
                    <li class="footer-link"><i class="fas fa-map-marker-alt"></i> 123 Rue de la République, Tunis</li>
                    <li class="footer-link"><i class="fas fa-phone"></i> +216 12 345 678</li>
                    <li class="footer-link"><i class="fas fa-envelope"></i> contact@gradupshop.tn</li>
                    <li class="footer-link"><i class="fas fa-clock"></i> Lun-Ven: 9h-18h</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Gradup Shop. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Modal Panier -->
     <div id="cartModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Votre Panier</h2>
            <div id="cartItems">
                <!-- Les produits du panier seront chargés ici -->
            </div>
            <div class="cart-total">
                <h3>Total: <span id="cartTotal">0.00</span> DT</h3>
            </div>
            <button id="validateOrder" class="add-to-cart">Valider la commande</button>
        </div>
    </div>

    <!-- Modal Commande -->
    <div id="orderModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <?php include('commander.php'); ?>
        </div>
    </div>

    <script>
        // Animation pour les boutons "Ajouter au panier"
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.getAttribute('href')?.includes('add_to_cart')) {
                    e.preventDefault();
                    const productCard = this.closest('.product-card');
                    const productName = productCard.querySelector('.product-title').textContent;
                    
                    // Animation du bouton
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Ajouté!';
                    this.style.background = 'var(--secondary)';
                    
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                        this.style.background = '';
                    }, 2000);
                    
                    // Redirection après l'animation
                    setTimeout(() => {
                        window.location.href = this.href;
                    }, 1000);
                }
            });
        });

        // Gestion du panier
        document.addEventListener('DOMContentLoaded', function() {
            const cartModal = document.getElementById('cartModal');
            const orderModal = document.getElementById('orderModal');
            const cartIcon = document.getElementById('cartIcon');
            const closeModals = document.querySelectorAll('.close-modal');
            const validateOrderBtn = document.getElementById('validateOrder');
            
            // Ouvrir le modal du panier
            cartIcon.addEventListener('click', function(e) {
                e.preventDefault();
                updateCartModal();
                cartModal.style.display = 'block';
            });
            
            // Fermer les modals
            closeModals.forEach(btn => {
                btn.addEventListener('click', function() {
                    cartModal.style.display = 'none';
                    orderModal.style.display = 'none';
                });
            });
            
            // Valider la commande
            validateOrderBtn?.addEventListener('click', function() {
                cartModal.style.display = 'none';
                orderModal.style.display = 'block';
            });
            
            // Fermer en cliquant en dehors
            window.addEventListener('click', function(e) {
                if (e.target === cartModal) {
                    cartModal.style.display = 'none';
                }
                if (e.target === orderModal) {
                    orderModal.style.display = 'none';
                }
            });
        });
        
        function updateCartModal() {
            fetch('get_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    const cartItemsContainer = document.getElementById('cartItems');
                    const cartTotalElement = document.getElementById('cartTotal');
                    const cartCountElement = document.querySelector('.cart-count');
                    
                    if (data.items.length === 0) {
                        cartItemsContainer.innerHTML = '<p>Votre panier est vide</p>';
                        cartTotalElement.textContent = '0.00';
                        return;
                    }
                    
                    let html = '';
                    let total = 0;
                    
                    data.items.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        total += itemTotal;
                        
                        html += `
                            <div class="cart-item" data-id="${item.id}">
                                <div class="cart-item-info">
                                    <img src="${item.image}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/50?text=Image+Indisponible'">
                                    <div>
                                        <h4>${item.name}</h4>
                                        <p>${item.price.toFixed(2)} DT</p>
                                    </div>
                                </div>
                                <div class="cart-item-quantity">
                                    <button onclick="updateQuantity(${item.id}, -1)">-</button>
                                    <span>${item.quantity}</span>
                                    <button onclick="updateQuantity(${item.id}, 1)">+</button>
                                </div>
                            </div>
                        `;
                    });
                    
                    cartItemsContainer.innerHTML = html;
                    cartTotalElement.textContent = total.toFixed(2);
                    
                    // Mettre à jour le compteur du panier
                    if (cartCountElement) {
                        if (data.cartCount > 0) {
                            cartCountElement.textContent = data.cartCount;
                            cartCountElement.style.display = 'flex';
                        } else {
                            cartCountElement.style.display = 'none';
                        }
                    }
                });
        }
        
        function updateQuantity(productId, change) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity_change', change);
            
            fetch('update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartModal();
                }
            });
        }
    </script>
</body>
</html>
</body>
</html>