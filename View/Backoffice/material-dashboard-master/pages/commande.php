<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'projetweb2a';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

// Traitement de la modification de statut et date de livraison
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_commande'])) {
    $id = $_POST['id_commande'];
    $nouvel_etat = $_POST['etat'];
    $date_livraison = $_POST['date_livraison'] ?? null;
    
    $stmt = $db->prepare("UPDATE commande SET etat = ?, date_livraison = ? WHERE id_commande = ?");
    $stmt->execute([$nouvel_etat, $date_livraison, $id]);
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Récupération des commandes (avec filtre par date si spécifié)
$whereClause = '';
if (isset($_GET['date'])) {
    $whereClause = " WHERE date_livraison = '".$_GET['date']."'";
}

$query = "SELECT * FROM commande $whereClause ORDER BY id_commande DESC";
$stmt = $db->query($query);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Gestion des Commandes</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
    }
    
    .status-en-cours {
      background-color: #f39c12;
      color: white;
    }
    
    .status-validee {
      background-color: #2ecc71;
      color: white;
    }
    
    .status-null {
      background-color: #95a5a6;
      color: white;
    }
    
    .status-select {
      padding: 6px 10px;
      border-radius: 4px;
      border: 1px solid #ddd;
      margin-right: 10px;
      font-size: 14px;
    }
    
    .inline-form {
      display: inline-block;
      margin: 0;
    }
    
    .price {
      font-weight: 600;
      color: #3498db;
    }
    
    .action-buttons .btn {
      margin: 2px;
      padding: 0.3rem 0.6rem;
    }
    
    .produit-item {
      padding: 5px 0;
      border-bottom: 1px solid #eee;
    }
    
    .produit-item:last-child {
      border-bottom: none;
    }
    
    .produit-image {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 10px;
    }
    
    .produit-details {
      display: flex;
      align-items: center;
      margin-bottom: 5px;
    }
    
    .produit-info {
      flex-grow: 1;
    }
    
    .table-responsive {
      overflow-x: auto;
    }
    
    .date-input {
      display: inline-block;
      width: auto;
      padding: 6px 10px;
      border-radius: 4px;
      border: 1px solid #ddd;
      margin-right: 10px;
      font-size: 14px;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="ms-1 text-sm text-dark">Gestion Commandes</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/dashboard.html">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="commande.php">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Commandes</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/produit.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">Produits</span>
          </a>
        </li>
        <!-- Ajout du lien vers le calendrier -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="calendrier.php">
            <i class="material-symbols-rounded opacity-5">calendar_today</i>
            <span class="nav-link-text ms-1">Calendrier Livraisons</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Commandes</li>
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
              <label class="form-label">Rechercher...</label>
              <input type="text" class="form-control" id="searchInput">
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Liste des commandes</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <?php if (isset($_GET['date'])): ?>
                <div class="alert alert-info mx-3">
                  Affichage des commandes pour le <?= date('d/m/Y', strtotime($_GET['date'])) ?>
                  <a href="commande.php" class="float-end">Voir toutes les commandes</a>
                </div>
              <?php endif; ?>
              
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="commandesTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Client</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contact</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Adresse</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produits</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Total</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Date Livraison</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Statut</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($commandes as $commande): 
                      $produits = json_decode($commande['produits'], true);
                    ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($commande['id_commande']) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($commande['nom']) ?> <?= htmlspecialchars($commande['prenom']) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($commande['tlf']) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($commande['adresse']) ?></p>
                      </td>
                      <td>
                        <div class="d-flex flex-column px-2 py-1">
                          <?php if (is_array($produits)): ?>
                            <?php foreach ($produits as $produit): ?>
                              <div class="produit-details">
                                <?php 
                                // Récupérer l'image du produit
                                $stmt = $db->prepare("SELECT image FROM produit WHERE id_produit = ?");
                                $stmt->execute([$produit['id_produit']]);
                                $image = $stmt->fetchColumn();
                                ?>
                                <?php if ($image): ?>
                                  <img src="../uploads/<?= htmlspecialchars($image) ?>" class="produit-image" alt="<?= htmlspecialchars($produit['name']) ?>">
                                <?php endif; ?>
                                <div class="produit-info">
                                  <span class="text-xs font-weight-bold">
                                    ID: <?= htmlspecialchars($produit['id_produit']) ?> - 
                                    <?= htmlspecialchars($produit['name']) ?> 
                                    (<?= htmlspecialchars($produit['quantity']) ?> × 
                                    <?= number_format($produit['price'], 2) ?> €)
                                  </span>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <span class="text-xs font-weight-bold">Aucun produit</span>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold price"><?= number_format($commande['prix_total'], 2) ?> €</span>
                      </td>
                      <td class="align-middle text-center">
                        <?php if ($commande['date_livraison']): ?>
                          <?= date('d/m/Y', strtotime($commande['date_livraison'])) ?>
                        <?php else: ?>
                          <span class="text-muted">Non définie</span>
                        <?php endif; ?>
                      </td>
                      <td class="align-middle text-center">
                        <span class="status-badge <?= ($commande['etat'] == 'validée' ? 'status-validee' : ($commande['etat'] == 'en cours' ? 'status-en-cours' : 'status-null')) ?>">
                          <?= htmlspecialchars($commande['etat'] ?? 'Non défini') ?>
                        </span>
                      </td>
                      <td class="align-middle text-center action-buttons">
                        <form method="post" class="inline-form">
                          <input type="hidden" name="id_commande" value="<?= $commande['id_commande'] ?>">
                          <select name="etat" class="status-select">
                            <option value="en cours" <?= $commande['etat'] == 'en cours' ? 'selected' : '' ?>>En cours</option>
                            <option value="validée" <?= $commande['etat'] == 'validée' ? 'selected' : '' ?>>Validée</option>
                          </select>
                          <input type="date" name="date_livraison" class="date-input" 
                                 value="<?= $commande['date_livraison'] ?>">
                          <button type="submit" class="btn btn-sm btn-success" title="Modifier">
                            <i class="material-symbols-rounded">check</i>
                          </button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Scripts -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    // Recherche en temps réel
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const input = this.value.toLowerCase();
      const rows = document.querySelectorAll('#commandesTable tbody tr');
      
      rows.forEach(row => {
        const id = row.querySelector('td:first-child h6').textContent.toLowerCase();
        const nom = row.querySelector('td:nth-child(2) h6').textContent.toLowerCase();
        const prenom = row.querySelector('td:nth-child(3) h6').textContent.toLowerCase();
        const adresse = row.querySelector('td:nth-child(4) p').textContent.toLowerCase();
        const produits = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
        
        if (id.includes(input) || nom.includes(input) || prenom.includes(input) || adresse.includes(input) || produits.includes(input)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // Initialisation du scrollbar
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
</body>
</html>