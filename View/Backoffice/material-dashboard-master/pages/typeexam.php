<?php
require_once(__DIR__ . "/../../../../controller/typeexamcontroller.php");

$controller = new TypeExamController();
$types = $controller->getAllTypes(); // Get all types
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Gestion des Types d'Examens</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .flash-message {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
      animation: fadeOut 2s forwards;
      animation-delay: 2s;
    }
    @keyframes fadeOut {
      to { opacity: 0; display: none; }
    }
    .product-image {
      width: 80px;
      height: auto;
      object-fit: cover;
      border-radius: 6px;
      transition: transform 0.3s ease;
    }
    .product-image:hover {
      transform: scale(1.1);
    }
    .action-buttons .btn {
      margin: 2px;
      padding: 0.3rem 0.6rem;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <!-- Flash Messages -->
  <?php if (isset($_GET['delete']) && $_GET['delete'] === 'success'): ?>
    <div class="alert alert-success flash-message">
      Type supprimé avec succès!
    </div>
  <?php elseif (isset($_GET['delete_error'])): ?>
    <div class="alert alert-danger flash-message">
      <?= htmlspecialchars(urldecode($_GET['delete_error'])) ?>
    </div>
  <?php endif; ?>

  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="ms-1 text-sm text-dark">Gestion Examens</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="typeexam.php">
            <i class="material-symbols-rounded opacity-5">description</i>
            <span class="nav-link-text ms-1">Types d'Examens</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="correction1.php">
            <i class="material-symbols-rounded opacity-5">grading</i>
            <span class="nav-link-text ms-1">Corrections</span>
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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Types d'Examens</li>
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
                <h6 class="text-white text-capitalize ps-3">Liste des Types d'Examens</h6>
                <div class="container mt-2">
                  <a href="addtypeexam.php" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i> Ajouter un type
                  </a>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="typesTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom du Type</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Image</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Examen Client</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($types)): ?>
                      <tr>
                        <td colspan="5" class="text-center py-4">
                          <p class="text-secondary mb-0">Aucun type d'examen trouvé</p>
                        </td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($types as $index => $type): ?>
                        <tr>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">
                              <?= $index + 1 ?>
                            </span>
                          </td>
                          <td>
                            <p class="text-xs text-secondary mb-0">
                              <?= htmlspecialchars($type['type_name']) ?>
                            </p>
                          </td>
                          <td class="align-middle text-center">
                            <?php if (!empty($type['image'])): ?>
                              <img src="../uploads/<?= htmlspecialchars($type['image']) ?>" 
                                   alt="Image" 
                                   class="product-image">
                            <?php else: ?>
                              <span class="text-xs text-secondary">Aucune image</span>
                            <?php endif; ?>
                          </td>
                          <td class="align-middle text-center">
                            <?php if (!empty($type['image3'])): ?>
                              <a href="../uploads/<?= htmlspecialchars($type['image3']) ?>" target="_blank">
                                <img src="../uploads/<?= htmlspecialchars($type['image3']) ?>" 
                                     alt="Examen client" 
                                     class="product-image">
                              </a>
                            <?php else: ?>
                              <span class="text-xs text-secondary">Aucun examen client</span>
                            <?php endif; ?>
                          </td>
                          <td class="align-middle text-center action-buttons">
                            <a href="typeexam_update.php?id=<?= $type['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                              <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                            <a href="typeexam_delete.php?id=<?= $type['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               title="Supprimer"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type ?');">
                              <i class="fas fa-trash me-1"></i> Supprimer
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
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
      const rows = document.querySelectorAll('#typesTable tbody tr');
      
      rows.forEach(row => {
        const typeName = row.querySelector('td:nth-child(2) p').textContent.toLowerCase();
        
        if (typeName.includes(input)) {
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

    // Auto-hide flash messages after 2 seconds
    setTimeout(function() {
      const messages = document.querySelectorAll('.flash-message');
      messages.forEach(message => {
        message.style.display = 'none';
      });
    }, 2000);
  </script>
</body>
</html>