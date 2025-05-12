<?php
require_once(__DIR__ . "/../../Controller/certificatcontroller.php");
$controller = new CertificatController();
$certificats = $controller->listeCertificat();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes Certificats</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f8;
    }
    header {
      background-color: #3498db;
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
    .certificats-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 1.5rem;
      padding: 1rem 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    .certificat-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform 0.2s;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .certificat-card:hover {
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
    .certificat-image {
      width: 100%;
      height: 100%;
      object-fit: contain;
      background: white;
      padding: 10px;
    }
    .certificat-info {
      padding: 1.2rem;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
      flex-grow: 1;
    }
    .certificat-info h4 {
      margin: 0;
      font-size: 1.1rem;
      color: #3498db;
      font-weight: 600;
    }
    .certificat-info p {
      margin: 0;
      font-size: 0.9rem;
      color: #555;
      line-height: 1.4;
    }
    .certificat-info p strong {
      color: #333;
      font-weight: 500;
    }
    .certificat-status {
      display: inline-block;
      padding: 0.4rem 0.9rem;
      border-radius: 50rem;
      font-weight: bold;
      font-size: 0.85rem;
      margin-top: 0.8rem;
      text-align: center;
      width: fit-content;
    }
    .status-valide {
      background-color: #d1fae5;
      color: #065f46;
    }
    .status-attente {
      background-color: #fef3c7;
      color: #92400e;
    }
    .status-refuse {
      background-color: #fee2e2;
      color: #991b1b;
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
      color: #3498db;
    }
    footer {
      background-color: #3498db;
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
      <h1>Gradup Certificats</h1>
    </div>
    <nav>
      <a href="#">Accueil</a>
      <a href="#">Mes Certificats</a>
      <a href="#">Demander un Certificat</a>*
      <a href="cadeau.php?">Demander un Cadeau</a>
    </nav>
  </header>

  <div class="hero">
    Gérer vos certificats académiques
  </div>

  <h2 class="section-title">Mes Certificats</h2>

  <div class="certificats-container" id="certificats-list">
    <?php if (!empty($certificats)): ?>
      <?php foreach ($certificats as $p): ?>
        <?php
        // Définir les chemins absolus
        $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/';
        $uploadDir = $baseDir . 'View/Backoffice/material-dashboard-master/uploads/';
        $webPath = '/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/';
        
        $imageFile = htmlspecialchars($p['image'] ?? '');
        $fullPath = $uploadDir . $imageFile;
        $webUrl = $webPath . $imageFile;
        ?>
        
        <div class="certificat-card">
          <div class="image-container">
            <?php if (!empty($imageFile) && file_exists($fullPath)): ?>
              <img src="<?= $webUrl ?>" class="certificat-image" alt="Certificat <?= htmlspecialchars($p['nom'] ?? '') ?>">
            <?php else: ?>
              <span class="material-symbols-rounded placeholder-icon">description</span>
            <?php endif; ?>
          </div>
          
          <div class="certificat-info">
            <h4><?= htmlspecialchars($p['nom'] ?? '') ?></h4>
            <p><strong>Type:</strong> <?= htmlspecialchars($p['type'] ?? '') ?></p>
            <p><strong>Objet:</strong> <?= htmlspecialchars($p['objet'] ?? '') ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($p['date_demande'] ?? '') ?></p>
            <p><strong>Niveau:</strong> <?= htmlspecialchars($p['niveau'] ?? '') ?></p>
            
            <span class="certificat-status 
              <?= match(strtolower($p['status'] ?? '')) {
                'valide' => 'status-valide',
                'en attente' => 'status-attente',
                'refusé' => 'status-refuse',
                default => 'status-attente'
              } ?>">
              <?= htmlspecialchars($p['status'] ?? '') ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="no-data">
        <span class="material-symbols-rounded">info</span>
        <h5>Aucun certificat disponible</h5>
        <p>Vous n'avez aucun certificat pour le moment</p>
      </div>
    <?php endif; ?>
  </div>

  <footer>
    &copy; 2025 Gradup. Tous droits réservés. | Contact : gradup@edu.tn | +216 99 999 999
  </footer>
</body>
</html>