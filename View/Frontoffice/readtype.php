<?php
require_once(__DIR__ . "/../../controller/typeexamcontroller.php");

$controller = new TypeExamController();
$types = $controller->getAllTypes(); // Get all types
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gradup Shop - Gestion des Examens</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet">
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
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
    .main-content {
      padding: 30px;
      background-color: #f8f9fa;
      min-height: calc(100vh - 200px);
    }
    .card-body {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      padding: 30px;
      margin-top: 20px;
    }
    .table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
      border-spacing: 0;
      border-radius: 8px;
      overflow: hidden;
    }
    .table th,
    .table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .table th {
      background-color: #3498db;
      color: #fff;
      font-weight: bold;
      border-bottom: 2px solid #2980b9; /* Darker blue line under headers */
    }
    .table td {
      background-color: #fff;
      font-size: 14px;
      color: #333;
    }
    .table td img {
      border-radius: 6px;
      max-width: 80px;
      height: auto;
      object-fit: cover;
    }
    .table tbody tr:hover {
      background-color: #f1f1f1;
    }
    .product-image {
      max-width: 80px;
      height: auto;
      border-radius: 6px;
      cursor: pointer;
      transition: transform 0.2s ease;
    }
    .product-image:hover {
      transform: scale(1.1);
    }
    .section-title {
      text-align: center;
      margin: 2rem 0 1rem;
      font-size: 1.8rem;
      color: #333;
    }
    footer {
      background-color: #3498db;
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
    }
    .breadcrumb {
      background-color: transparent;
      padding: 0;
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 20px;
    }
    .breadcrumb-item a {
      color: #3498db;
      text-decoration: none;
    }
    .breadcrumb-item a:hover {
      text-decoration: underline;
    }
    .breadcrumb-item.active {
      color: #6c757d;
    }
    .text-muted {
      color: #6c757d;
      font-style: italic;
    }
  </style>
</head>

<body>
  <header>
    <div class="logo">
      <img src="logo.jpeg" alt="logo">
      <h1>Gradup Shop</h1>
    </div>
    <nav>
      <a href="#">Accueil</a>
      <a href="#">Boutique</a>
      <a href="#">Cours</a>
      <a href="#">Forum</a>
      <a href="#">Événements</a>
      <a href="#">Dons</a>
      <a href="read_corection1.php?"#>Corrections</a>
    </nav>
  </header>

  <div class="main-content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active" aria-current="page">Types d'Examens</li>
      </ol>
    </nav>

    <h2 class="section-title">Gestion des Types d'Examens</h2>

    <div class="card-body">
      <!-- Table to display the types -->
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nom du Type</th>
            <th>Image</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($types as $index => $type): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($type['type_name']) ?></td>
              <td>
                <?php if (!empty($type['image'])): ?>
                  <a href="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($type['image']) ?>" target="_blank">
                    <img src="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($type['image']) ?>" 
                         alt="Image" 
                         class="product-image" 
                         style="width: 80px; height: auto; object-fit: cover; border-radius: 6px; cursor: pointer;">
                  </a>
                <?php else: ?>
                  <span class="text-muted">Aucune image</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer>
    &copy; 2025 Gradup Shop. Tous droits réservés. | Contact : gradup@edu.tn | +216 99 999 999
  </footer>

  <!-- JS -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
</body>
</html>