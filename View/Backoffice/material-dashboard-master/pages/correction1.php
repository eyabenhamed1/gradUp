<?php
session_start();
require_once(__DIR__ . "/../../../../Config.php");
require_once(__DIR__ . "/../../../../controller/Correction1Controller.php");

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /View/Frontoffice/login.php");
    exit;
}

// Initialize controller
$pdo = config::getConnexion();
$controller = new Correction1Controller($pdo);

// Get all corrections instead of just user's corrections
$corrections = $controller->getAllCorrections();

// Handle status messages
$statusMessage = '';
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'success':
            $statusMessage = '<div class="alert alert-success">Opération réussie!</div>';
            break;
        case 'error':
            $statusMessage = '<div class="alert alert-danger">Une erreur est survenue.</div>';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Mes Corrections</title>
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
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="ms-1 text-sm text-dark">Mes Corrections</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="correction1.php">
            <i class="material-symbols-rounded opacity-5">list</i>
            <span class="nav-link-text ms-1">Mes Corrections</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="typeexam.php">
            <i class="material-symbols-rounded opacity-5">description</i>
            <span class="nav-link-text ms-1">Types d'Examens</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../../auth/logout.php">
            <i class="material-symbols-rounded opacity-5">logout</i>
            <span class="nav-link-text ms-1">Déconnexion</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Mes Corrections</li>
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
      <?php echo $statusMessage; ?>
      
      <div class="card">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <h6>Mes Corrections</h6>
          <a href="View\Backoffice\material-dashboard-master\pages\createcorrection1.php.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Correction
          </a>
        </div>

        <div class="card-body px-0 pb-0">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0" id="correctionsTable">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID Exam</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Image</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Remarque</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Note</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($corrections && count($corrections) > 0): ?>
                  <?php foreach ($corrections as $correction): ?>
                    <tr>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">
                          <?= htmlspecialchars($correction['id_exam']) ?>
                        </span>
                      </td>
                      <td>
                        <?php if ($correction['image2']): ?>
                          <img src="../uploads/<?= htmlspecialchars($correction['image2']) ?>" 
                               alt="Correction" 
                               class="avatar avatar-sm me-3 product-image">
                        <?php else: ?>
                          <span class="text-xs text-secondary">Pas d'image</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <p class="text-xs text-secondary mb-0">
                          <?= htmlspecialchars($correction['remarque']) ?>
                        </p>
                      </td>
                      <td>
                        <span class="badge badge-sm bg-gradient-success">
                          <?= htmlspecialchars($correction['note']) ?>
                        </span>
                      </td>
                      <td class="align-middle action-buttons">
                        <a href="updatecorrection1.php?id_cor=<?= urlencode($correction['id_cor']) ?>" 
                           class="btn btn-link text-warning px-3 mb-0">
                          <i class="fas fa-pencil-alt me-2"></i>Modifier
                        </a>
                        <a href="javascript:void(0)" 
                           onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette correction ?')) 
                           window.location.href='deletecorrection1.php?id_cor=<?= urlencode($correction['id_cor']) ?>'" 
                           class="btn btn-link text-danger px-3 mb-0">
                          <i class="fas fa-trash me-2"></i>Supprimer
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center py-4">
                      <p class="text-secondary mb-0">Aucune correction trouvée</p>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- JS -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    // Recherche en temps réel
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const input = this.value.toLowerCase();
      const rows = document.querySelectorAll('#correctionsTable tbody tr');
      
      rows.forEach(row => {
        const idExam = row.querySelector('td:first-child span').textContent.toLowerCase();
        const remarque = row.querySelector('td:nth-child(3) p').textContent.toLowerCase();
        
        if (idExam.includes(input) || remarque.includes(input)) {
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