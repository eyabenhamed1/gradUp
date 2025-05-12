<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
$produitFront = new ProduitFront();

session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Récupérer la catégorie sélectionnée (par défaut "tous")
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'tous';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 8;

// Compter et récupérer les produits en fonction de la catégorie
// Récupérer le paramètre de tri
// Récupérer les paramètres
$selectedCategory = $_GET['category'] ?? 'tous';
$sortByRating = isset($_GET['sort']) && $_GET['sort'] === 'top_rated';

// Récupérer les produits selon les paramètres
if ($selectedCategory === 'tous') {
    $totalProduits = $produitFront->countProduits();
    $produits = $sortByRating 
        ? $produitFront->listeProduitsByRatingPagination(($page - 1) * $perPage, $perPage)
        : $produitFront->listeProduitsPagination(($page - 1) * $perPage, $perPage);
} else {
    $totalProduits = $produitFront->countProduitsByCategory($selectedCategory);
    $produits = $sortByRating
        ? $produitFront->listeProduitsByCategoryAndRatingPagination($selectedCategory, ($page - 1) * $perPage, $perPage)
        : $produitFront->listeProduitsByCategoryPagination($selectedCategory, ($page - 1) * $perPage, $perPage);
}

$totalPages = max(ceil($totalProduits / $perPage), 1);

if ($page > $totalPages && $totalPages > 0) {
    header("Location: ?page=$totalPages&category=$selectedCategory");
    exit;
}
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

// ... (le reste de votre code existant)


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
        .filter-btn i.fa-star {
    margin-right: 5px;
    color: #FFD700; /* Couleur or pour l'icône étoile */
}

.filter-btn.active i.fa-star {
    color: white;
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
        .filter-btn {
    padding: 10px 20px;
    background-color: var(--light-gray);
    color: var(--dark);
    border-radius: 4px;
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
}
/* Conteneur principal des filtres */
.category-filter {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 8px;  /* Réduit l'espace entre les boutons */
    margin-bottom: 20px;
    padding: 0 10px;
}

/* Style de base des boutons */
.filter-btn {
    padding: 6px 12px;  /* Taille réduite */
    font-size: 0.85rem;  /* Texte plus petit */
    border-radius: 4px;
    text-decoration: none;
    color: #2c3e50;
    background-color: #ecf0f1;
    border: 1px solid #bdc3c7;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;  /* Empêche le texte de passer à la ligne */
}

/* Bouton actif */
.filter-btn.active {
    background-color: #e74c3c;
    color: white;
    border-color: #c0392b;
}

/* Icônes dans les boutons */
.filter-btn i {
    font-size: 0.75rem;  /* Icônes plus petites */
    margin-right: 5px;
}

/* Couleur spécifique pour l'icône étoile */
.filter-btn .fa-star {
    color: #FFD700;
}

/* Couleur spécifique pour l'icône de tri */
.filter-btn .fa-sort {
    color: #95a5a6;
}

/* Au survol */
.filter-btn:hover {
    background-color: #dfe6e9;
    transform: translateY(-1px);
}

/* Bouton actif au survol */
.filter-btn.active:hover {
    background-color: #c0392b;
}

/* Version mobile */
@media (max-width: 768px) {
    .category-filter {
        gap: 6px;
        justify-content: flex-start;
        overflow-x: auto;  /* Permet le défilement horizontal si nécessaire */
        padding-bottom: 5px;
        -webkit-overflow-scrolling: touch;
    }
    
    .filter-btn {
        padding: 5px 10px;
        font-size: 0.8rem;
    }
}
.filter-btn:hover {
    background-color: var(--medium-gray);
    color: var(--white);
}

.filter-btn.active {
    background-color: var(--accent);
    color: var(--white);
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
            
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin_left:10%;
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
        .product-rating {
    color: #FFD700; /* Couleur dorée pour les étoiles */
    font-size: 0.9rem;
    cursor: pointer;
    position: relative;
}

.product-rating .star-rated {
    color: #FFD700;
}

.product-rating i {
    transition: all 0.2s ease;
}

.product-rating .rating-text {
    color: var(--medium-gray);
    font-size: 0.8rem;
    margin-left: 5px;
}

.rating-stars {
    display: inline-block;
    unicode-bidi: bidi-override;
    color: #ccc;
    font-size: 25px;
    height: 25px;
    width: 125px;
    margin: 0 auto;
    position: relative;
    padding: 0;
}

.rating-stars span {
    display: block;
    position: absolute;
    overflow: hidden;
    top: 0;
    left: 0;
}

.rating-stars span:before {
    content: "★★★★★";
    color: #FFD700;
    font-size: 25px;
    height: 25px;
    width: 125px;
}

.star-rating-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 10px 0;
}

.star-rating-value {
    margin-top: 5px;
    font-size: 0.9rem;
    color: var(--medium-gray);
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
        .filter-btn i.fas.fa-star {
    margin-right: 5px;
    color: #FFD700; /* Couleur or */
}

.filter-btn.active i.fas.fa-star {
    color: white;
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
        /* Style pour les boutons de tri */
.filter-btn i {
    margin-right: 5px;
}

.filter-btn i.fa-star {
    color: #FFD700; /* Couleur or pour l'étoile */
}

.filter-btn i.fa-sort {
    color: #95a5a6; /* Couleur grise pour l'icône de tri */
}

.filter-btn.active i {
    color: white;
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
        /* Style pour la barre de recherche */
.search-container {
    margin: 20px auto;
    max-width: 500px;
    padding: 0 15px;
    margin_right:10%;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 12px;
    color: #95a5a6;
}
/* Animation pour le filtrage */
@keyframes productFilter {
  0% { opacity: 1; transform: scale(1); }
  50% { opacity: 0; transform: scale(0.95); }
  100% { opacity: 1; transform: scale(1); }
}

/* Style des produits filtrés */
.product-card.filtered {
  display: none;
  animation: none;
}

.product-card.visible {
  display: block;
  animation: productFilter 0.4s ease-out;
  transition: all 0.3s ease;
}

/* Indicateur de résultats */
.search-results-count {
  text-align: center;
  margin: 15px 0;
  font-size: 0.9rem;
  color: #7f8c8d;
}

/* Style des produits en vedette (matching) */
.product-card.highlight-match {
  position: relative;
  border: 2px solid #e74c3c;
  box-shadow: 0 0 15px rgba(231, 76, 60, 0.2);
}

.product-card.highlight-match::before {
  content: "Correspondance";
  position: absolute;
  top: -12px;
  right: 15px;
  background: #e74c3c;
  color: white;
  padding: 3px 10px;
  border-radius: 15px;
  font-size: 0.7rem;
  font-weight: bold;
  z-index: 2;
}
/* Conteneur principal */
.product-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  transition: all 0.4s ease;
}

/* Masquage des produits filtrés */
.product-card.filtered {
  display: none !important;
}

/* Animation d'apparition */
@keyframes slideIn {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.product-card:not(.filtered) {
  animation: slideIn 0.3s ease forwards;
}

/* Message "Aucun résultat" */
.no-results {
  grid-column: 1/-1;
  text-align: center;
  padding: 40px;
  display: none;
}

.no-results.show {
  display: block;
  animation: fadeIn 0.5s ease;
}

.search-input {
    width: 100%;
    padding: 10px 40px 10px 35px;
    border: 1px solid #bdc3c7;
    border-radius: 25px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
}

.clear-search {
    position: absolute;
    right: 12px;
    color: #95a5a6;
    cursor: pointer;
    transition: all 0.3s ease;
}
/* Message "Aucun résultat" */
.no-results {
    text-align: center;
    padding: 40px;
    display: none;
    background: white;
    border-radius: 8px;
    margin: 20px auto;
    max-width: 500px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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

/* Navigation */
.nav {
    display: flex;
    align-items: center;
}

/* NOUVEAU STYLE POUR LA NAVIGATION */
.nav-list {
    display: flex;
    list-style: none;
    gap: 25px; /* Espacement entre les éléments */
    margin_right: 100px;
    padding: 0;
}
/* NOUVEAU STYLE POUR LA NAVIGATION PRINCIPALE */
.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-list {
    display: flex;
    list-style: none;
    gap: 15px;
    margin: 0;
    padding: 0;
}

.nav-item {
    margin: 0;
}

.nav-link {
    color: var(--dark);
    text-decoration: none;
    font-weight: 500;
    font-size: 1rem;
    padding: 10px 0;
    position: relative;
    transition: var(--transition);
}

.nav-link:hover {
    color: var(--accent);
}

.nav-link.active {
    color: var(--accent);
    font-weight: 600;
}

/* STYLE COMPACT POUR LA NAVIGATION */
.nav {
    display: flex;
    align-items: center;
    gap: 20px;
}

.nav-icons {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-icon {
    color: var(--dark);
    font-size: 1.2rem;
    transition: var(--transition);
    position: relative;
}

/* STYLE POUR LE LOGO */
.logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo i {
    color: var(--accent);
}

/* STYLE POUR LA BARRE DE RECHERCHE */
.search-container {
    margin-left: auto;
    margin-right: 20px;
    width: 300px;
}

.search-box {
    position: relative;
    width: 100%;
}

.search-input {
    width: 100%;
    padding: 8px 35px 8px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 0.9rem;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #95a5a6;
}

.clear-search {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #95a5a6;
    cursor: pointer;
}

/* STYLE RESPONSIVE */
@media (max-width: 992px) {
    .nav-list {
        gap: 10px;
    }
    
    .nav-link {
        font-size: 0.9rem;
    }
    
    .search-container {
        width: 200px;
    }
}

@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        padding: 10px;
    }
    
    .nav {
        width: 100%;
        justify-content: space-between;
        margin-top: 10px;
    }
    
    .search-container {
        order: 3;
        width: 100%;
        margin: 10px 0 0 0;
    }
}

/* ANIMATION POUR LES LIENS ACTIFS */
.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--accent);
    transition: width 0.3s ease;
}

.nav-link:hover::after,
.nav-link.active::after {
    width: 100%;
}

/* STYLE POUR LE COMPTEUR DE PANIER */
.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: var(--accent);
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.nav-item {
    margin: 0; /* Supprime toutes les marges */
}

.nav-link {
    color: var(--dark);
    text-decoration: none;
    font-weight: 500;
    font-size: 1.1rem; /* Taille légèrement augmentée */
    padding: 10px 0;
    position: relative;
    transition: var(--transition);
    border-bottom: 3px solid transparent;
}

.nav-link:hover {
    color: var(--accent);
}

.nav-link.active {
    color: var(--accent);
    border-bottom: 3px solid var(--accent);
    font-weight: 600;
}

/* SUPPRIMER l'ancien effet ::after */
.nav-link::after {
    content: none;
}

/* Icônes */
.nav-icons {
    display: flex;
    align-items: center;
    margin-left: 40px;
    gap: 20px;
}

.nav-icon {
    color: var(--dark);
    font-size: 1.3rem;
    transition: var(--transition);
    cursor: pointer;
    position: relative;
    text-decoration: none;
}

.no-results h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.no-results p {
    color: #7f8c8d;
    font-size: 0.9rem;
}

.clear-search:hover {
    color: #e74c3c;
}

/* Style pour les résultats filtrés */
.product-card.filtered {
    display: none;
}

.product-card.visible {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
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
                    <li class="nav-item"><a href="macommande.php" class="nav-link"> Commande</a></li>
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
                <div class="search-container" style="margin: 20px auto; max-width: 500px;">
    <div class="search-box">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="productSearch" placeholder="Rechercher produits (chemise, cahier, chargeur...)" class="search-input">
        <i class="fas fa-times-circle clear-search" id="clearSearch" style="display: none;"></i>
    </div>
</div>
            </nav>
        </div>
    </header>
    <main class="main">
    <div class="container">
    <h1 class="page-title">Notre Boutique</h1>
    
    <!-- Ajoutez ce menu de filtrage -->
    <div class="category-filter" style="margin-bottom: 30px; display: flex; justify-content: center; gap: 15px;">
        <a href="?category=tous" class="filter-btn <?= $selectedCategory === 'tous' ? 'active' : '' ?>">Tous</a>
        <a href="?category=vetement" class="filter-btn <?= $selectedCategory === 'vetement' ? 'active' : '' ?>">Vêtements</a>
        <a href="?category=fourniture" class="filter-btn <?= $selectedCategory === 'fourniture' ? 'active' : '' ?>">Fournitures</a>
        <a href="?category=informatique" class="filter-btn <?= $selectedCategory === 'informatique' ? 'active' : '' ?>">Informatique</a>
    </div>
    <a href="?category=<?= $selectedCategory ?>" 
       class="filter-btn <?= !isset($_GET['sort']) ? 'active' : '' ?>"
       title="Tri par défaut">
       <i class="fas fa-sort"></i> Par défaut
    </a>
    <a href="?sort=top_rated&category=<?= $selectedCategory ?>" 
   class="filter-btn <?= (isset($_GET['sort']) && $_GET['sort'] === 'top_rated') ? 'active' : '' ?>">
   <i class="fas fa-star"></i> Meilleures notes
</a>
</div>
            
            <!-- Slider de produits -->
            <div class="product-slider">
                <div class="slider-container" id="sliderContainer">
                    <?php foreach ($produits as $produit): ?>
                        <div class="slider-product-card">
                            <div class="product-card visible">
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
    <?php 
    $avgRating = $produitFront->getAverageRating($produit['id_produit']);
    $fullStars = floor($avgRating);
    $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
    
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $fullStars) {
            echo '<i class="fas fa-star star-rated" data-rating="'.$i.'"></i>';
        } elseif ($hasHalfStar && $i == $fullStars + 1) {
            echo '<i class="fas fa-star-half-alt star-rated" data-rating="'.$i.'"></i>';
        } else {
            echo '<i class="far fa-star" data-rating="'.$i.'"></i>';
        }
    }
    ?>
    <span class="rating-text">(<?= number_format($avgRating, 1) ?>)</span>
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
        // Gestion du système d'évaluation
document.querySelectorAll('.product-rating').forEach(ratingContainer => {
    const stars = ratingContainer.querySelectorAll('i');
    const productCard = ratingContainer.closest('.product-card');
    const productId = productCard.querySelector('.add-to-cart').getAttribute('href').split('=')[1];
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            
            // Envoyer la note au serveur
            fetch('save_rating.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&rating=${rating}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour l'affichage
                    const avgRating = data.average;
                    const fullStars = Math.floor(avgRating);
                    const hasHalfStar = (avgRating - fullStars) >= 0.5;
                    
                    stars.forEach((s, i) => {
                        if (i < fullStars) {
                            s.classList.remove('far', 'fa-star-half-alt');
                            s.classList.add('fas');
                        } else if (hasHalfStar && i === fullStars) {
                            s.classList.remove('far', 'fas');
                            s.classList.add('fa-star-half-alt');
                        } else {
                            s.classList.remove('fas', 'fa-star-half-alt');
                            s.classList.add('far');
                        }
                    });
                    
                    // Mettre à jour le texte
                    const ratingText = ratingContainer.querySelector('.rating-text');
                    if (ratingText) {
                        ratingText.textContent = '(' + avgRating.toFixed(1) + ')';
                    }
                }
            });
        });
        
        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            stars.forEach((s, i) => {
                if (i < rating) {
                    s.style.color = '#FFD700';
                }
            });
        });
        
        star.addEventListener('mouseout', function() {
            stars.forEach(s => {
                if (s.classList.contains('far')) {
                    s.style.color = '';
                }
            });
        });
    });
});
    </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearch');
    const clearSearch = document.getElementById('clearSearch');
    const sliderContainer = document.getElementById('sliderContainer');
    const noResultsMsg = document.createElement('div');
    
    // Créer le message "Aucun résultat"
    noResultsMsg.className = 'no-results';
    noResultsMsg.innerHTML = `
        <i class="fas fa-search" style="font-size:2rem;color:#bdc3c7;margin-bottom:15px"></i>
        <h3>Aucun produit trouvé</h3>
        <p>Essayez d'autres termes comme "chargeur", "cahier"...</p>
    `;
    sliderContainer.parentNode.insertBefore(noResultsMsg, sliderContainer.nextSibling);
    
    function searchProducts() {
        const term = searchInput.value.toLowerCase().trim();
        let hasResults = false;
        
        document.querySelectorAll('.slider-product-card').forEach(card => {
            const title = card.querySelector('.product-title').textContent.toLowerCase();
            const desc = card.querySelector('.product-description').textContent.toLowerCase();
            const matches = term === '' || title.includes(term) || desc.includes(term);
            
            card.style.display = matches ? 'block' : 'none';
            if (matches) hasResults = true;
        });
        
        // Afficher/masquer le message et le slider
        noResultsMsg.style.display = hasResults || term === '' ? 'none' : 'block';
        sliderContainer.style.display = hasResults ? 'flex' : 'none';
        clearSearch.style.display = term === '' ? 'none' : 'block';
    }
    
    // Événements
    searchInput.addEventListener('input', searchProducts);
    
    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        searchProducts();
        searchInput.focus();
    });
});
</script>
</body>
</html>