<?php
session_start();
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit();
}

$conn = config::getConnexion();

// Récupérer des statistiques, par exemple, le nombre d'utilisateurs
$sql = "SELECT COUNT(*) as total_users FROM user WHERE deleted_at IS NULL";
$stmt = $conn->prepare($sql);
$stmt->execute();
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Autres statistiques que vous souhaitez afficher
$sqlActiveUsers = "SELECT COUNT(*) as active_users FROM user WHERE deleted_at IS NULL";
$stmtActive = $conn->prepare($sqlActiveUsers);
$stmtActive->execute();
$activeUsers = $stmtActive->fetch(PDO::FETCH_ASSOC)['active_users'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard – GradUp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --sidebar-bg: #2f3e4e;
      --sidebar-text: #fff;
      --accent: #ff8c42;
    }
    body {
      background-color: #f4f6f9;
    }
    .sidebar {
      width: 220px;
      background: var(--sidebar-bg);
      color: var(--sidebar-text);
      padding: 2rem 1rem;
      position: fixed;
      height: 100vh;
    }
    .sidebar .brand {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 2rem;
      text-align: center;
    }
    .sidebar nav a {
      display: flex;
      align-items: center;
      padding: .75rem 1rem;
      border-radius: 12px;
      color: var(--sidebar-text);
      text-decoration: none;
      margin-bottom: .5rem;
    }
    .sidebar nav a.active,
    .sidebar nav a:hover {
      background: var(--accent);
    }
    .main-content {
      margin-left: 220px;
      padding: 2rem;
      width: calc(100% - 220px);
    }
    .card-header {
      background-color: var(--accent);
      color: #fff;
    }
    .stat-card {
      background-color: #fff;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="brand">GradUp</div>
    <nav>
      <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
      <a href="tables.php"><i class="fas fa-users me-2"></i> Utilisateurs</a>
      <a href="profile.php"><i class="fas fa-user-circle me-2"></i> Profil</a>
      <a href="../../../Frontoffice/auth/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="card shadow mb-4">
      <div class="card-header">
        <h5 class="mb-0">Statistiques du Dashboard</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <!-- Card pour Total Utilisateurs -->
          <div class="col-md-3">
            <div class="stat-card">
              <h4>Total Utilisateurs</h4>
              <p><?= $totalUsers ?></p>
            </div>
          </div>

          <!-- Card pour Utilisateurs Actifs -->
          <div class="col-md-3">
            <div class="stat-card">
              <h4>Utilisateurs Actifs</h4>
              <p><?= $activeUsers ?></p>
            </div>
          </div>

          <!-- Autres cartes pour d'autres statistiques -->
          <!-- Ajouter ici des cartes supplémentaires -->
        </div>

        <!-- Chart.js pour graphiques -->
        <div class="row">
          <div class="col-md-12">
            <canvas id="userStatsChart" width="400" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Données pour les graphiques (exemple)
    const ctx = document.getElementById('userStatsChart').getContext('2d');
    const userStatsChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
          label: 'Utilisateurs Actifs',
          data: [12, 19, 3, 5, 2],
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          borderColor: 'rgba(255, 99, 132, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>
</html>
