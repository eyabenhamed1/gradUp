<?php

require_once(__DIR__ . "/../../../../controller/evenementcontroller.php");
require_once(__DIR__ . "/../../../../controller/participationcontroller.php");

$eventController = new EvenementController();
$stats = $eventController->getEventStatistics();
$participationStats = $eventController->getParticipationStats();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Statistiques des Événements | Backoffice</title>
  
  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  
  <!-- Polices et icônes -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
    }
    .stat-card {
      transition: all 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .stat-icon {
      font-size: 1.5rem;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="Logo Backoffice">
        <span class="ms-1 text-sm text-dark font-weight-bold">Backoffice</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/dashboard.html">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Tableau de bord</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/tables.html">
            <i class="material-symbols-rounded opacity-5">Group</i>
            <span class="nav-link-text ms-1">Utilisateurs</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/evenement.php">
            <i class="material-symbols-rounded opacity-5">Event</i>
            <span class="nav-link-text ms-1">Événements</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/calendrier.php">
            <i class="material-symbols-rounded opacity-5">calendar_month</i>
            <span class="nav-link-text ms-1">Calendrier</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/statistiques.php">
            <i class="material-symbols-rounded text-white">bar_chart</i>
            <span class="nav-link-text ms-1">Statistiques</span>
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
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Admin</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Statistiques</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Statistiques des événements</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
              <label class="form-label">Filtrer...</label>
              <input type="text" class="form-control" id="filterInput">
            </div>
          </div>
          <ul class="navbar-nav justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <span class="text-sm font-weight-bold"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container-fluid py-4">
      <!-- Message d'alerte -->
      <?php if (!empty($alertMessage)): ?>
        <div class="alert alert-<?= $alertClass ?> alert-dismissible fade show">
          <?= htmlspecialchars($alertMessage) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Statistiques des événements</h6>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <!-- Cartes de statistiques -->
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                  <div class="card stat-card">
                    <div class="card-body p-3">
                      <div class="row">
                        <div class="col-8">
                          <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Événements</p>
                            <h5 class="font-weight-bolder mb-0">
                              <?= $stats['total_events'] ?>
                            </h5>
                          </div>
                        </div>
                        <div class="col-4 text-end">
                          <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">event</i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                  <div class="card stat-card">
                    <div class="card-body p-3">
                      <div class="row">
                        <div class="col-8">
                          <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Événements Passés</p>
                            <h5 class="font-weight-bolder mb-0">
                              <?= $stats['past_events'] ?>
                            </h5>
                          </div>
                        </div>
                        <div class="col-4 text-end">
                          <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">history</i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                  <div class="card stat-card">
                    <div class="card-body p-3">
                      <div class="row">
                        <div class="col-8">
                          <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Événements à Venir</p>
                            <h5 class="font-weight-bolder mb-0">
                              <?= $stats['upcoming_events'] ?>
                            </h5>
                          </div>
                        </div>
                        <div class="col-4 text-end">
                          <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">upcoming</i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-xl-3 col-sm-6">
                  <div class="card stat-card">
                    <div class="card-body p-3">
                      <div class="row">
                        <div class="col-8">
                          <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Participation Moyenne</p>
                            <h5 class="font-weight-bolder mb-0">
                              <?= $participationStats['avg_participation'] ?> participants
                            </h5>
                          </div>
                        </div>
                        <div class="col-4 text-end">
                          <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">group</i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Graphiques -->
              <div class="row mt-4">
                <div class="col-lg-6">
                  <div class="card h-100">
                    <div class="card-header pb-0">
                      <h6>Répartition par Type</h6>
                    </div>
                    <div class="card-body p-3">
                      <div class="chart-container">
                        <canvas id="pieChart"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-6">
                  <div class="card h-100">
                    <div class="card-header pb-0">
                      <h6>Top 5 Événements</h6>
                      <p class="text-sm">Avec le plus de participants</p>
                    </div>
                    <div class="card-body p-3">
                      <div class="chart-container">
                        <canvas id="barChart"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
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
    // Initialisation du scrollbar
    if (document.querySelector('#sidenav-scrollbar')) {
      new PerfectScrollbar('#sidenav-scrollbar', {
        wheelSpeed: 0.5,
        suppressScrollX: true
      });
    }

    // Initialisation des graphiques
    document.addEventListener('DOMContentLoaded', function() {
      // Pie Chart - Répartition par type
      const ctxPie = document.getElementById('pieChart').getContext('2d');
      const pieChart = new Chart(ctxPie, {
          type: 'pie',
          data: {
              labels: <?= json_encode(array_column($stats['by_type'], 'type_evenement')) ?>,
              datasets: [{
                  data: <?= json_encode(array_column($stats['by_type'], 'count')) ?>,
                  backgroundColor: [
                      '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                  ],
                  hoverBackgroundColor: [
                      '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'
                  ],
                  hoverBorderColor: "rgba(234, 236, 244, 1)",
              }],
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                  legend: {
                      position: 'right',
                  }
              }
          }
      });

      // Bar Chart - Top événements
      const ctxBar = document.getElementById('barChart').getContext('2d');
      const barChart = new Chart(ctxBar, {
          type: 'bar',
          data: {
              labels: <?= json_encode(array_column($participationStats['top_events'], 'titre')) ?>,
              datasets: [{
                  label: "Participants",
                  backgroundColor: "#4e73df",
                  hoverBackgroundColor: "#2e59d9",
                  borderColor: "#4e73df",
                  data: <?= json_encode(array_column($participationStats['top_events'], 'participants')) ?>,
              }],
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                  y: {
                      beginAtZero: true,
                      ticks: {
                          precision: 0
                      }
                  }
              }
          }
      });
    });
  </script>
</body>
</html>