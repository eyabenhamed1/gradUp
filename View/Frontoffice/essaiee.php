<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
$produitFront = new ProduitFront();

// Démarrer la session pour le panier
session_start();

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 8;
$totalProduits = $produitFront->countProduits();
$totalPages = max(ceil($totalProduits / $perPage), 1);

if ($page > $totalPages && $totalPages > 0) {
    header("Location: ?page=$totalPages");
    exit;
}

// Récupérer les produits pour la page actuelle
$produits = $produitFront->listeProduitsPagination(($page - 1) * $perPage, $perPage);

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

// Calculer le nombre d'articles dans le panier
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
        .product-slider {
            position: relative;
            margin: 40px 0;
            overflow: hidden;
        }
        
        .slider-container {
            display: flex;
            transition: transform 0.5s ease;
            padding: 10px 0;
        }
        
        .slider-product-card {
            flex: 0 0 calc(25% - 30px);
            margin: 0 15px;
            box-sizing: border-box;
        }
        
        .slider-nav {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .slider-btn {
            background-color: var(--dark);
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .slider-btn:hover {
            background-color: var(--accent);
        }
        
        .slider-btn:disabled {
            background-color: var(--light-gray);
            cursor: not-allowed;
        }
        
        .slider-dots {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--light-gray);
            margin: 0 5px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .slider-dot.active {
            background-color: var(--accent);
        }
        
        @media (max-width: 992px) {
            .slider-product-card {
                flex: 0 0 calc(33.333% - 30px);
            }
        }
        
        @media (max-width: 768px) {
            .slider-product-card {
                flex: 0 0 calc(50% - 30px);
            }
        }
        
        @media (max-width: 480px) {
            .slider-product-card {
                flex: 0 0 calc(100% - 30px);
            }
        }
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
        .simple-pagination {
            display: flex;
            justify-content: center;
            margin: 40px 0;
            font-family: 'Poppins', sans-serif;
        }
        
        .simple-pagination a {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .simple-pagination a:hover {
            background-color: #e74c3c;
        }
        
        .simple-pagination .current-page {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #e74c3c;
            color: white;
            border-radius: 4px;
        }
        
        .simple-pagination .disabled {
            background-color: #95a5a6;
            pointer-events: none;
        }
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
            background-color: var(--white);
            color: var(--dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--accent);
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
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
        }
        
        .nav-link:hover {
            color: var(--accent);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent);
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
            color: var(--dark);
            font-size: 1.2rem;
            margin-left: 1.2rem;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            text-decoration: none;
        }
        
        .nav-icon:hover {
            color: var(--accent);
            transform: translateY(-2px);
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--accent);
            color: var(--white);
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
        
        /* Product Grid */
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            padding: 20px 0;
        }
        
        .product-card {
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            position: relative;
            border: 1px solid var(--light-gray);
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: var(--medium-gray);
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--accent);
            color: var(--white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
        }
        
        .product-image-container {
            height: 230px;
            overflow: hidden;
            background: var(--light);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-image {
            max-width: 80%;
            max-height: 80%;
            object-fit: contain;
            transition: var(--transition);
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
            color: var(--accent);
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
            color: var(--accent);
            font-size: 0.9rem;
        }
        
        .add-to-cart {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: var(--dark);
            color: var(--white);
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
            background-color: var(--accent);
        }
        
        /* Pagination simplifiée */
        .pagination {
            display: flex;
            justify-content: center;
            margin: 40px 0 20px;
        }
        
        .pagination-list {
            display: flex;
            list-style: none;
            gap: 15px;
            align-items: center;
        }
        
        .pagination-item {
            margin: 0;
        }
        
        .pagination-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 6px;
            background-color: var(--white);
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            border: 1px solid var(--light-gray);
            gap: 8px;
        }
        
        .pagination-link:hover:not(.disabled):not(.active) {
            background-color: var(--dark);
            color: var(--white);
            border-color: var(--dark);
        }
        
        .pagination-link.active {
            background-color: transparent;
            color: var(--dark);
            border: none;
            font-weight: 600;
            cursor: default;
        }
        
        .pagination-link.disabled {
            opacity: 0.5;
            pointer-events: none;
            cursor: not-allowed;
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark);
            color: var(--white);
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
            color: var(--white);
        }
        
        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--accent);
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
            color: var(--accent);
            padding-left: 5px;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-link {
            color: var(--white);
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
            background-color: var(--accent);
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
            
            .pagination-list {
                flex-wrap: wrap;
                justify-content: center;
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
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 25px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
        }
        
        .close-modal {
            color: var(--medium-gray);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .close-modal:hover {
            color: var(--accent);
        }
        
        #cartItems {
            margin: 20px 0;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .cart-item-info {
            display: flex;
            align-items: center;
        }
        
        .cart-item img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 15px;
            background-color: var(--light);
            padding: 5px;
            border-radius: 4px;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
        }
        
        .cart-item-quantity button {
            background: var(--light);
            border: 1px solid var(--light-gray);
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .cart-item-quantity button:hover {
            background-color: var(--dark);
            color: var(--white);
        }
        
        .cart-item-quantity span {
            margin: 0 10px;
            min-width: 20px;
            text-align: center;
        }
        
        .cart-total {
            text-align: right;
            margin: 25px 0 15px;
            padding-top: 15px;
            border-top: 2px solid var(--light-gray);
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: var(--medium-gray);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--light-gray);
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
                    <li class="nav-item"><a href="essaiee.php" class="nav-link active">Boutique</a></li>
                    <li class="nav-item"><a href="macommande.php" class="nav-link">Ma Commande</a></li>
                    <li class="nav-item"><a href="promotions.php" class="nav-link">Promotions</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
                </ul>
    
                <div class="nav-icons">
                    <a href="#" class="nav-icon"><i class="fas fa-search"></i></a>
                    <a href="evenements.php" class="nav-icon" title="Événements"><i class="fas fa-calendar-alt"></i></a>
                    <a href="certificats.php" class="nav-icon" title="Certificats"><i class="fas fa-certificate"></i></a>
                    <a href="quiz.php" class="nav-icon" title="Quiz"><i class="fas fa-question-circle"></i></a>
                    <a href="compte.php" class="nav-icon" title="Mon compte"><i class="fas fa-user"></i></a>
                    <a href="#" class="nav-icon" id="cartIcon" title="Panier">
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
            
            <!-- Slider de produits -->
            <div class="product-slider">
                <div class="slider-container" id="sliderContainer">
                    <?php foreach ($produits as $produit): ?>
                        <div class="slider-product-card">
                            <div class="product-card">
                                <?php if ($produit['stock'] < 5): ?>
                                    <span class="product-badge">Bientôt épuisé!</span>
                                <?php endif; ?>
                                <div class="product-image-container">
                                    <img src="<?= htmlspecialchars($produit['image_path']) ?>" 
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
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="slider-nav">
                    <button class="slider-btn" id="prevBtn" disabled>Précédent</button>
                    <button class="slider-btn" id="nextBtn">Suivant</button>
                </div>
                
                <div class="slider-dots" id="sliderDots">
                    <!-- Les points de navigation seront générés par JavaScript -->
                </div>
            </div>
            
            <!-- Pagination classique (vous pouvez la garder ou la supprimer) -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <ul class="pagination-list">
                    <li class="pagination-item">
                        <a href="?page=<?= max(1, $page - 1) ?>" class="pagination-link <?= $page <= 1 ? 'disabled' : '' ?>">
                            <i class="fas fa-chevron-left"></i> Précédent
                        </a>
                    </li>
                    
                    <li class="pagination-item">
                        <span class="pagination-link active">Page <?= $page ?> sur <?= $totalPages ?></span>
                    </li>
                    
                    <li class="pagination-item">
                        <a href="?page=<?= min($totalPages, $page + 1) ?>" class="pagination-link <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            Suivant <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
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
                    <li class="footer-link"><a href="essaiee.php">Boutique</a></li>
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
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Votre Panier</h2>
            <div id="cartItems">
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Ton panier plein de style et de bons plans te fait de l’œil… Clique sur ‘Valider’ et laisse la magie shopping de Gradup opérer !”</p>
                </div>
            </div>
            <div class="cart-total">
                <h3>GradUp <span id="cartTotal"></span> DT</h3>
            </div>
            <button id="validateOrder" class="add-to-cart">Valider </button>
        </div>
    </div>

    <!-- Modal Commande -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="orderContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sliderContainer = document.getElementById('sliderContainer');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const sliderDots = document.getElementById('sliderDots');
            
            const productCards = document.querySelectorAll('.slider-product-card');
            const cardWidth = productCards[0]?.offsetWidth || 280;
            const visibleCards = Math.min(4, productCards.length);
            const cardMargin = 30;
            const step = cardWidth + cardMargin;
            
            let currentPosition = 0;
            const maxPosition = (productCards.length - visibleCards) * step;
            
            // Créer les points de navigation
            if (productCards.length > visibleCards) {
                const dotCount = Math.ceil(productCards.length / visibleCards);
                for (let i = 0; i < dotCount; i++) {
                    const dot = document.createElement('div');
                    dot.classList.add('slider-dot');
                    if (i === 0) dot.classList.add('active');
                    dot.addEventListener('click', () => {
                        goToSlide(i * visibleCards);
                    });
                    sliderDots.appendChild(dot);
                }
            }
            
            // Fonction pour aller à une slide spécifique
            function goToSlide(index) {
                currentPosition = index * step;
                if (currentPosition > maxPosition) currentPosition = maxPosition;
                if (currentPosition < 0) currentPosition = 0;
                
                sliderContainer.style.transform = `translateX(-${currentPosition}px)`;
                
                // Mettre à jour l'état des boutons
                prevBtn.disabled = currentPosition === 0;
                nextBtn.disabled = currentPosition >= maxPosition;
                
                // Mettre à jour les points actifs
                const dots = document.querySelectorAll('.slider-dot');
                const activeDotIndex = Math.floor(currentPosition / (visibleCards * step));
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === activeDotIndex);
                });
            }
            
            // Écouteurs d'événements pour les boutons
            prevBtn.addEventListener('click', () => {
                goToSlide(Math.floor(currentPosition / step) - 1);
            });
            
            nextBtn.addEventListener('click', () => {
                goToSlide(Math.floor(currentPosition / step) + 1);
            });
            
            // Ajuster la position au redimensionnement
            window.addEventListener('resize', () => {
                const newCardWidth = productCards[0]?.offsetWidth || 280;
                const newStep = newCardWidth + cardMargin;
                const ratio = currentPosition / step;
                currentPosition = ratio * newStep;
                sliderContainer.style.transform = `translateX(-${currentPosition}px)`;
            });
        });
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
                    this.style.backgroundColor = 'var(--accent)';
                    
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                        this.style.backgroundColor = '';
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
            const orderContent = document.getElementById('orderContent');
            
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
                // Charger le contenu de commander.php via AJAX
                fetch('commander.php')
                    .then(response => response.text())
                    .then(html => {
                        orderContent.innerHTML = html;
                        cartModal.style.display = 'none';
                        orderModal.style.display = 'block';
                        
                        // Réattacher les événements après le chargement
                        attachOrderEvents();
                        
                        // Gestion du formulaire de commande
                        const orderForm = document.getElementById('orderForm');
                        if (orderForm) {
                            orderForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                
                                const formData = new FormData(this);
                                formData.append('commander', '1');
                                
                                fetch('commander.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.href = data.redirect;
                                    } else {
                                        // Afficher les erreurs
                                        alert(data.errors.join('\n'));
                                    }
                                });
                            });
                        }
                    });
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
        
        function attachOrderEvents() {
            // Gestion des boutons de quantité
            document.querySelectorAll('.btn-quantity').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.closest('tr').querySelector('input[name="product_id"]').value;
                    const change = this.querySelector('i').classList.contains('fa-minus') ? -1 : 1;
                    
                    updateQuantity(productId, change);
                });
            });
            
            // Gestion des liens de suppression
            document.querySelectorAll('.remove-item').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-id');
                    
                    if (confirm('Voulez-vous vraiment supprimer ce produit ?')) {
                        removeItem(productId);
                    }
                });
            });
        }
        
        function updateCartModal() {
            fetch('get_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    const cartItemsContainer = document.getElementById('cartItems');
                    const cartTotalElement = document.getElementById('cartTotal');
                    const cartCountElement = document.querySelector('.cart-count');
                    
                    if (data.items.length === 0) {
                        cartItemsContainer.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-shopping-cart"></i>
                                <p>Votre panier est vide</p>
                            </div>
                        `;
                        cartTotalElement.textContent = '0.00';
                        if (cartCountElement) cartCountElement.style.display = 'none';
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
                    // Recharger le contenu du panier
                    updateCartModal();
                    
                    // Si on est dans le modal de commande, recharger aussi ce contenu
                    const orderModal = document.getElementById('orderModal');
                    if (orderModal && orderModal.style.display === 'block') {
                        fetch('commander.php')
                            .then(response => response.text())
                            .then(html => {
                                document.getElementById('orderContent').innerHTML = html;
                                attachOrderEvents();
                            });
                    }
                }
            });
        }
        
        function removeItem(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('remove', '1');
            
            fetch('update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger le contenu du panier
                    updateCartModal();
                    
                    // Si on est dans le modal de commande, recharger aussi ce contenu
                    const orderModal = document.getElementById('orderModal');
                    if (orderModal && orderModal.style.display === 'block') {
                        fetch('commander.php')
                            .then(response => response.text())
                            .then(html => {
                                document.getElementById('orderContent').innerHTML = html;
                                attachOrderEvents();
                            });
                    }
                }
            });
        }
    </script>
</body>
</html>