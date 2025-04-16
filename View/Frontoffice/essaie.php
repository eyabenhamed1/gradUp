<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
$produitFront = new ProduitFront();
$produits = $produitFront->listeProduits();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique - Gradup Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --danger: #e74c3c;
            --light: #f9f9f9;
            --dark: #2c3e50;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light);
            color: var(--dark);
        }
        
        .header {
            background-color: var(--primary);
            color: white;
            padding: 1.5rem 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.2rem;
        }
        
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        
        .product-image-container {
            height: 230px;
            overflow: hidden;
            background: #f1f1f1;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0 0 10px;
            color: var(--dark);
        }
        
        .product-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.4;
            height: 40px;
            overflow: hidden;
        }
        
        .product-price {
            color: var(--danger);
            font-weight: bold;
            font-size: 1.2rem;
            margin: 10px 0;
        }
        
        .product-stock {
            font-size: 0.9rem;
            color: var(--secondary);
            font-weight: 500;
        }
        
        .add-to-cart {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .add-to-cart:hover {
            background-color: #2980b9;
        }
        
        @media (max-width: 768px) {
            .product-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Gradup Shop</h1>
    </header>

    <div class="product-container">
        <?php if (empty($produits)): ?>
            <div style="grid-column:1/-1; text-align:center; padding:40px;">
                <h3>Aucun produit disponible pour le moment</h3>
            </div>
        <?php else: ?>
            <?php foreach ($produits as $produit): ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="<?= htmlspecialchars($produit['image_path']) ?>" 
                             alt="<?= htmlspecialchars($produit['name']) ?>"
                             class="product-image"
                             onerror="this.src='https://via.placeholder.com/280x230?text=Image+Indisponible'">
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($produit['name']) ?></h3>
                        <p class="product-description"><?= htmlspecialchars($produit['description']) ?></p>
                        <div class="product-price"><?= number_format($produit['prix'], 2) ?> DT</div>
                        <div class="product-stock">Stock: <?= (int)$produit['stock'] ?></div>
                        <button class="add-to-cart">Ajouter au panier</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        // Fonctionnalité basique du panier
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const productName = productCard.querySelector('.product-title').textContent;
                const productPrice = productCard.querySelector('.product-price').textContent;
                
                alert(`${productName} (${productPrice}) ajouté au panier`);
                
                // Ici vous pourriez ajouter une vraie logique de panier
                // avec localStorage ou une requête AJAX
            });
        });
    </script>
</body>
</html>