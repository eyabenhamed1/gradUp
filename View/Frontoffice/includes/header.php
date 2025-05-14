<?php
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$cartCount = isset($_SESSION['cart']) ? array_reduce($_SESSION['cart'], function($carry, $item) {
    return $carry + $item['quantity'];
}, 0) : 0;
?>

<link rel="stylesheet" href="css/header.css">

<header class="header">
    <div class="header-container">
        <a href="index.php" class="logo">
            <i class="fas fa-graduation-cap"></i>
            Gradup Shop
        </a>
        
        <nav class="nav">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a href="essaiee.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'essaiee.php' ? 'active' : '' ?>">
                        <i class="fas fa-shopping-bag"></i> Boutique
                    </a>
                </li>
                <li class="nav-item">
                    <a href="evenements.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'evenements.php' ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i> Événements
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'e-learning.php' ? 'active' : '' ?>">
                        <i class="fas fa-graduation-cap"></i> E-learning
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'certificat.php' ? 'active' : '' ?>">
                        <i class="fas fa-certificate"></i> Certificat
                    </a>
                </li>
            </ul>
            
            <div class="nav-icons">
                <a href="profile.php" class="nav-icon" title="Mon profil">
                    <i class="fas fa-user"></i>
                </a>
                <a href="#" class="nav-icon" id="cartIcon" title="Panier">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-count"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="auth/logout.php" class="logout-link" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </a>
            </div>
        </nav>
    </div>
</header>

<script>
document.getElementById('menuToggle').addEventListener('click', function() {
    document.querySelector('.nav-list').classList.toggle('show');
});

document.addEventListener('click', function(event) {
    const nav = document.querySelector('.nav');
    const navList = document.querySelector('.nav-list');
    const menuToggle = document.getElementById('menuToggle');
    
    if (!nav.contains(event.target) && navList.classList.contains('show')) {
        navList.classList.remove('show');
    }
});
</script>

<style>
    .header-container {
        padding-left: 30px;
    }
    
    .nav-list {
        margin-left: 20px;
    }
    
    .nav-icons {
        margin-left: 30px;
        margin-right: 20px;
    }
    
    .nav-icon {
        margin: 0 10px;
    }
    
    .logout-link {
        margin-left: 15px;
    }
</style> 