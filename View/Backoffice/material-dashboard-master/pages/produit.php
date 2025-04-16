<?php
require_once(__DIR__ . "/../../../../controller/produitcontroller.php");
$controller = new ProduiController();
$produits = $controller->listeProduit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Gestion des Produits</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .product-image {
      max-width: 80px;
      max-height: 80px;
      border-radius: 4px;
      object-fit: cover;
    }
    .action-buttons .btn {
      margin: 2px;
      padding: 0.3rem 0.6rem;
    }
    .product-image {
    background-color: red;
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
        <span class="ms-1 text-sm text-dark">Gestion Produits</span>
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
          <a class="nav-link text-dark" href="../pages/tables.html">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Utilisateurs</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/produit.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">Produits</span>
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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Produits</li>
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
                <h6 class="text-white text-capitalize ps-3">Liste des produits</h6>
                <div class="container mt-2">
                  <a href="addproduit.php" class="btn btn-success">
                    <i class="material-symbols-rounded">add</i> Ajouter un produit
                  </a>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="productsTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Image</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Prix</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stock</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($produits as $p): ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($p['name']) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <?php if (!empty($p['image'])): ?>
                          <img src="<?php echo '../uploads/' . htmlspecialchars($p['image']); ?>" class="product-image" alt="Image produit">
                        <?php else: ?>
                          <span class="text-muted">Aucune image</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($p['description']) ?></p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold"><?= number_format($p['prix'], 2) ?> €</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="badge badge-sm <?= $p['stock'] > 0 ? 'bg-gradient-success' : 'bg-gradient-danger' ?>">
                          <?= htmlspecialchars($p['stock']) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center action-buttons">
                        <a href="produit_update.php?id=<?= $p['id_produit'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                          <i class="material-symbols-rounded">edit</i>
                        </a>
                        <a href="produit_delete.php?id=<?= $p['id_produit'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                          <i class="material-symbols-rounded">delete</i>
                        </a>
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
      const rows = document.querySelectorAll('#productsTable tbody tr');
      
      rows.forEach(row => {
        const name = row.querySelector('td:first-child h6').textContent.toLowerCase();
        const description = row.querySelector('td:nth-child(3) p').textContent.toLowerCase();
        
        if (name.includes(input) || description.includes(input)) {
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
