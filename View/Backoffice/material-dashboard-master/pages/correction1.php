<?php
// Always include database connection first
include_once $_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Config.php';

class Correction1 {
    public static function getAll() {
        $conn = config::getConnexion();  // Get the PDO connection
        $sql = "SELECT * FROM correction1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public static function getOne($id_cor) {
        $conn = config::getConnexion();
        $sql = "SELECT * FROM correction1 WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_cor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($idcor, $image2, $remarque, $note) {
        $conn = config::getConnexion();
        $sql = "INSERT INTO correction1 (id, image2, remarque, note) VALUES (:id, :image2, :remarque, :note)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $idcor, PDO::PARAM_INT);
        $stmt->bindParam(':image2', $image2);
        $stmt->bindParam(':remarque', $remarque);
        $stmt->bindParam(':note', $note);
        return $stmt->execute();
    }
}

// Fetch all corrections
$stmt = Correction1::getAll();
$corrections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Corrections</title>
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
        <span class="ms-1 font-weight-bold">Gestion Corrections</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <ul class="navbar-nav">
      <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="correction1.php">
            <i class="material-symbols-rounded"></i>
            <span>Main</span>
          </a>
      </li>
      <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="typeexam.php">
            <i class="material-symbols-rounded"></i>
            <span>back to exams</span>
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
            <li class="breadcrumb-item active" aria-current="page">Corrections</li>
          </ol>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <h6>Liste des Corrections</h6>
          <a href="createcorrection1.php" class="btn btn-success">
            <i class="material-symbols-rounded"></i> Ajouter Correction
          </a>
        </div>

        <div class="card-body">
          <!-- Table to display the corrections -->
          <table class="table">
            <thead>
              <tr>
                <th>id_exam</th>
                <th>Image</th>
                <th>Remarque</th>
                <th>Note</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($corrections) > 0): ?>
                <?php foreach ($corrections as $correction): ?>
    <tr>
        <td><?= htmlspecialchars($correction['id_exam']) ?></td>
        <td>
            <img src="../uploads/<?= htmlspecialchars($correction['image2']) ?>" 
                 alt="Image" 
                 class="product-image" 
                 style="width: 80px; height: auto; object-fit: cover; border-radius: 6px;">
        </td>
        <td><?= htmlspecialchars($correction['remarque']) ?></td>
        <td><?= htmlspecialchars($correction['note']) ?></td>
        <td class="action-buttons">
            <a href="updatecorrection1.php?id_cor=<?= urlencode($correction['id_cor']) ?>" class="btn btn-warning btn-sm">edit</a>
            <a href="deletecorrection1.php?id_cor=<?= urlencode($correction['id_cor']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette correction ?');">delete</a>
        </td>
    </tr>
<?php endforeach; ?>
                
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center">Aucune correction trouvée</td>
                </tr>
              <?php endif; ?>
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