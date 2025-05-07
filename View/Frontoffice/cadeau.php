<?php
require_once(__DIR__ . "/../../Controller/cadeaucontroller.php");
$controller = new CadeauController();
$cadeaux = $controller->listeCadeaux();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nos Cadeaux</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f8;
    }
    header {
      background-color:rgb(90, 107, 237);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      display: flex;
      align-items: center;
    }
    .logo img {
      height: 40px;
      margin-right: 10px;
    }
    nav a {
      color: white;
      margin: 0 1rem;
      text-decoration: none;
      font-weight: 500;
    }
    .hero {
      background-image: url('https://images.unsplash.com/photo-1607082349566-187342175e2c');
      background-size: cover;
      background-position: center;
      height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2rem;
      font-weight: bold;
      text-shadow: 2px 2px 4px #000;
    }
    .section-title {
      text-align: center;
      margin: 2rem 0 1rem;
      font-size: 1.8rem;
      color: #333;
    }
    .cadeaux-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 1.5rem;
      padding: 1rem 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    .cadeau-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform 0.2s;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .cadeau-card:hover {
      transform: scale(1.03);
    }
    .image-container {
      width: 100%;
      height: 220px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f0f0f0;
      overflow: hidden;
      position: relative;
    }
    .cadeau-image {
      width: 100%;
      height: 100%;
      object-fit: contain;
      background: white;
      padding: 10px;
    }
    .cadeau-info {
      padding: 1.2rem;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
      flex-grow: 1;
    }
    .cadeau-info h4 {
      margin: 0;
      font-size: 1.1rem;
      color:rgb(100, 107, 236);
      font-weight: 600;
    }
    .cadeau-info p {
      margin: 0;
      font-size: 0.9rem;
      color: #555;
      line-height: 1.4;
    }
    .no-data {
      text-align: center;
      padding: 3rem;
      grid-column: 1 / -1;
    }
    .no-data h5 {
      font-size: 1.25rem;
      color: #333;
      margin-top: 1rem;
    }
    .no-data p {
      color: #777;
    }
    .material-symbols-rounded {
      font-size: 3rem;
      color:rgb(89, 89, 228);
    }
    footer {
      background-color:rgb(87, 87, 236);
      color: white;
      text-align: center;
      padding: 1.5rem;
      margin-top: 3rem;
    }
    .placeholder-icon {
      font-size: 3rem;
      color: #aaa;
    }
  </style>
</head>
<body>

<header>
  <div class="logo">
    <img src="logo.jpeg" alt="logo">
    <h1>Gradup Cadeaux</h1>
  </div>
  <nav>
    <a href="#">Accueil</a>
    <a href="#">Nos Cadeaux</a>
    <a href="#">Mes Certificats</a>
  </nav>
</header>

<div class="hero">
  Découvrez nos récompenses exclusives
</div>

<h2 class="section-title">Nos Cadeaux</h2>

<div class="cadeaux-container" id="cadeaux-list">
  <?php if (!empty($cadeaux)): ?>
    <?php foreach ($cadeaux as $c): ?>
      <?php
      // Définir les chemins absolus
      $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/';
      $uploadDir = $baseDir . 'View/Backoffice/material-dashboard-master/uploads/';
      $webPath = '/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/';
      
      $imageFile = htmlspecialchars($c['image'] ?? '');
      $fullPath = $uploadDir . $imageFile;
      $webUrl = $webPath . $imageFile;
      ?>

      <div class="cadeau-card">
        <div class="image-container">
          <?php if (!empty($imageFile) && file_exists($fullPath)): ?>
            <img src="<?= $webUrl ?>" class="cadeau-image" alt="Cadeau <?= htmlspecialchars($c['type_cadeau'] ?? '') ?>">
          <?php else: ?>
            <span class="material-symbols-rounded placeholder-icon">redeem</span>
          <?php endif; ?>
        </div>
        
        <div class="cadeau-info">
          <h4><?= htmlspecialchars($c['type_cadeau'] ?? '') ?></h4>
          <p><strong>Date offerte:</strong> <?= htmlspecialchars($c['date_cadeau'] ?? '') ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="no-data">
      <span class="material-symbols-rounded">redeem</span>
      <h5>Aucun cadeau disponible</h5>
      <p>Il n’y a pas encore de cadeaux pour le moment</p>
    </div>
  <?php endif; ?>
</div>

<footer>
  &copy; 2025 Gradup. Tous droits réservés. | Contact : gradup@edu.tn | +216 99 999 999
</footer>

</body>
</html>
