<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
$produitFront = new ProduitFront();

$type = $_GET['type'] ?? 'sales';

if ($type === 'rating') {
    $topProducts = $produitFront->getTopRatedProducts(3);
} else {
    $topProducts = $produitFront->getTopSellingProducts(3);
}

foreach ($topProducts as $product): 
?>
<div class="top-product-card">
    <div class="top-product-badge">
        <i class="fas <?= $type === 'rating' ? 'fa-star' : 'fa-crown' ?>"></i> 
        <?= $type === 'rating' ? 'Top Note' : 'Top Vente' ?>
    </div>
    <div class="top-product-image-container">
        <img src="<?= htmlspecialchars($product['image_path']) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>"
             class="top-product-image"
             onerror="this.src='https://via.placeholder.com/280x230?text=Image+Indisponible'">
    </div>
    <div class="top-product-info">
        <h3 class="top-product-title"><?= htmlspecialchars($product['name']) ?></h3>
        <?php if ($type === 'rating'): ?>
            <div class="top-product-rating">
                <?php 
                $fullStars = floor($product['average_rating']);
                $hasHalfStar = ($product['average_rating'] - $fullStars) >= 0.5;
                
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $fullStars) {
                        echo '<i class="fas fa-star star-rated"></i>';
                    } elseif ($hasHalfStar && $i == $fullStars + 1) {
                        echo '<i class="fas fa-star-half-alt star-rated"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
                ?>
                <span>(<?= number_format($product['average_rating'], 1) ?>)</span>
            </div>
        <?php endif; ?>
        <div class="top-product-price"><?= number_format($product['prix'], 2) ?> DT</div>
        <a href="?add_to_cart=<?= $product['id_produit'] ?>" class="add-to-cart">
            <i class="fas fa-cart-plus"></i> Ajouter au panier
        </a>
    </div>
</div>
<?php endforeach; ?>