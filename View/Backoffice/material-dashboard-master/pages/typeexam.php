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
</head>
<body>
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
          <a class="nav-link active bg-gradient-dark text-white" href="type_exam.html">
            <i class="material-symbols-rounded"></i>
            <span>Type Exam</span>
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
            <i class="material-symbols-rounded"></i> Ajouter un type
          </a>
        </div>

        <div class="card-body">
          <!-- Table to display the types -->
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Nom du Type</th>
                <th>Image</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($types as $index => $type): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($type['type_name']) ?></td>
                  <td>
                    <img src="../uploads/<?= htmlspecialchars($type['image']) ?>" 
                         alt="Image" 
                         class="product-image" 
                         style="width: 80px; height: auto; object-fit: cover; border-radius: 6px;">
                  </td>
                  <td class="action-buttons">
                  <a href="typeexam_update.php?type=<?= urlencode($type['type_name']) ?>" class="btn btn-warning btn-sm">edit</a>
                  <a href="typeexam_delete.php?type=<?= urlencode($type['type_name']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce type ?');">delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- JS -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
</body>
</html>