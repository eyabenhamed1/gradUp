<?php
require_once(__DIR__ . "/../../../../controller/typeexamcontroller.php");

$controller = new TypeExamController();
$types = $controller->getAllTypes(); // Get all types
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Examens</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900">
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
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
        }
        .product-image:hover {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
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
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs bg-white fixed-start">
    <div class="sidenav-header">
      <a class="navbar-brand m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" alt="logo">
        <span class="ms-1 font-weight-bold">Gestion type exam</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <ul class="navbar-nav">
      <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="typeexam.php">
            <i class="material-symbols-rounded"></i>
            <span>Main</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="correction1.php">
            <i class="material-symbols-rounded"></i>
            <span>correction</span>
          </a>
      </li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="main-content border-radius-lg">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active" aria-current="page">Types d'Examens</li>
          </ol>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <h6>Liste des Types d'Examens</h6>
          <a href="addtypeexam.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Ajouter un type
          </a>
        </div>

        <div class="card-body">
          <?php if (empty($types)): ?>
            <div class="alert alert-info">Aucun type d'examen trouvé.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom du Type</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Image</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Examen de notre client</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($types as $index => $type): ?>
                    <tr>
                      <td class="ps-4"><?= $index + 1 ?></td>
                      <td><?= htmlspecialchars($type['type_name']) ?></td>
                      <td>
                        <?php if (!empty($type['image'])): ?>
                          <img src="../uploads/<?= htmlspecialchars($type['image']) ?>" 
                               alt="Image" 
                               class="product-image">
                        <?php else: ?>
                          <span class="text-muted">Aucune image</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (!empty($type['image3'])): ?>
                          <a href="../uploads/<?= htmlspecialchars($type['image3']) ?>" target="_blank">
                            <img src="../uploads/<?= htmlspecialchars($type['image3']) ?>" 
                                 alt="Examen client" 
                                 class="product-image">
                          </a>
                        <?php else: ?>
                          <span class="text-muted">Aucun examen client</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a href="typeexam_update.php?id=<?= $type['id'] ?>" class="btn btn-warning btn-sm">
                          <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="typeexam_delete.php?id=<?= $type['id'] ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type ?');">
                          <i class="fas fa-trash"></i> Supprimer
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
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