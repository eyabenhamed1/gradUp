<?php
require_once(__DIR__ . "/../../../../controller/evenementcontroller.php");
$controller = new evenementController();
$evenement = $controller->listeEvenement();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Gestion des Événements</title>
  
  <!-- Polices et icônes -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  
  <!-- Styles -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    :root {
      --primary-color: #4e73df;
      --primary-light: #7b9bf5;
      --primary-dark: #2e59d9;
      --secondary-color: #f8f9fc;
      --accent-color: #1a56ff;
      --success-color: #1cc88a;
      --warning-color: #f6c23e;
      --danger-color: #e74a3b;
    }
    
    .event-image {
      width: 100px;
      height: 70px;
      border-radius: 6px;
      object-fit: cover;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    
    .event-image:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .no-image {
      width: 100px;
      height: 70px;
      background: linear-gradient(135deg, #f8f9fc 0%, #e2e6f0 100%);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6c757d;
    }
    
    .action-buttons .btn {
      margin: 2px;
      padding: 0.4rem 0.7rem;
      border-radius: 6px;
      transition: all 0.2s;
    }
    
    .action-buttons .btn:hover {
      transform: translateY(-2px);
    }
    
    .card-header {
      border-radius: 12px 12px 0 0 !important;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
    }
    
    .bg-gradient-primary {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
    }
    
    .bg-gradient-info {
      background: linear-gradient(135deg, #36b9cc 0%, #258391 100%) !important;
    }
    
    .table-responsive {
      border-radius: 0 0 12px 12px;
    }
    
    .table thead th {
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      border-bottom: 2px solid #e3e6f0 !important;
      color: var(--primary-dark) !important;
    }
    
    .table tbody tr {
      transition: all 0.2s;
    }
    
    .table tbody tr:hover {
      background-color: rgba(78, 115, 223, 0.03);
    }
    
    .badge {
      font-weight: 500;
      padding: 0.35em 0.65em;
      font-size: 0.75em;
      border-radius: 8px;
    }
    
    .bg-gradient-success {
      background: linear-gradient(135deg, var(--success-color) 0%, #17a673 100%) !important;
    }
    
    .bg-gradient-warning {
      background: linear-gradient(135deg, var(--warning-color) 0%, #dda20a 100%) !important;
    }
    
    .bg-gradient-danger {
      background: linear-gradient(135deg, var(--danger-color) 0%, #be2617 100%) !important;
    }
    
    .btn-success {
      background: var(--success-color);
      border: none;
      box-shadow: 0 2px 6px rgba(28, 200, 138, 0.4);
    }
    
    .btn-success:hover {
      background: #17a673;
      box-shadow: 0 4px 10px rgba(28, 200, 138, 0.6);
    }
    
    #searchInput {
      border-radius: 8px;
      padding: 8px 15px;
      border: 1px solid #d1d3e2;
      transition: all 0.3s;
    }
    
    #searchInput:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .nav-link.active {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
      box-shadow: 0 4px 10px rgba(78, 115, 223, 0.4);
    }
    
    .material-symbols-rounded {
      color: inherit;
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
        <span class="ms-1 text-sm text-dark font-weight-bold">Gestion Événements</span>
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
          <a class="nav-link active bg-gradient-primary text-white" href="../pages/evenement.php">
            <i class="material-symbols-rounded text-white">event</i>
            <span class="nav-link-text ms-1">Événements</span>
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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Événements</li>
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
          <div class="card my-4 overflow-hidden">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Liste des événements</h6>
                <div class="container mt-3">
                  <a href="addEvenement.php" class="btn btn-success btn-sm">
                    <i class="material-symbols-rounded">add</i> Ajouter un Événement
                  </a>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="productsTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Titre</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Image</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Lieu</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Type</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($evenement as $p): ?>
                    <tr>
                      <td>
                        <div class="d-flex px-3 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm font-weight-bold"><?= htmlspecialchars($p['titre']) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <?php if (!empty($p['image'])): ?>
                          <img src="<?= '../uploads/' . htmlspecialchars($p['image']) ?>" 
                               class="event-image" 
                               alt="Image événement">
                        <?php else: ?>
                          <div class="no-image">
                            <i class="fas fa-image fa-lg"></i>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td class="align-middle">
                        <p class="text-xs font-weight-bold mb-0 text-truncate" style="max-width: 200px;">
                          <?= htmlspecialchars($p['description']) ?>
                        </p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-xs font-weight-bold">
                          <?= date('d/m/Y', strtotime($p['date_evenement'])) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="badge bg-gradient-info">
                          <?= htmlspecialchars($p['lieu']) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="badge bg-gradient-warning">
                          <?= htmlspecialchars($p['type_evenement']) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center action-buttons">
                        <a href="update_evenement.php?id=<?= $p['id'] ?>" 
                           class="btn btn-sm btn-warning" 
                           title="Modifier"
                           data-bs-toggle="tooltip">
                          <i class="material-symbols-rounded">edit</i>
                        </a>
                        <a href="delete_evenement.php?id=<?= $p['id'] ?>" 
                           class="btn btn-sm btn-danger" 
                           title="Supprimer" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');"
                           data-bs-toggle="tooltip">
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
    
    // Activation des tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
  </script>
</body>
</html>