<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Model/correction1.php');

// Get database connection
$conn = config::getConnexion();
$correctionModel = new Correction1($conn);

$successMessage = "";
$errorMessage = "";

// Check if correction ID is present
if (!isset($_GET['id_cor'])) {
    die("ID de correction manquant.");
}

$id_cor = $_GET['id_cor'];
$correction = $correctionModel->getOne($id_cor);

if (!$correction) {
    die("Correction introuvable.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $remarque = $_POST['remarque'] ?? '';
    $note = $_POST['note'] ?? '';
    
    // Handle file upload
    $image2 = $correction['image2']; // Keep existing image by default
    if (!empty($_FILES['image2']['name'])) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadFile = $uploadDir . basename($_FILES['image2']['name']);
        
        // Check if file is an image
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowedExtensions)) {
            if (move_uploaded_file($_FILES['image2']['tmp_name'], $uploadFile)) {
                $image2 = $_FILES['image2']['name'];
            } else {
                $errorMessage = "❌ Erreur lors du téléchargement de l'image.";
            }
        } else {
            $errorMessage = "❌ Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        }
    }
    
    if (empty($errorMessage)) {
        try {
            $id_exam = $_POST['id_exam'] ?? '';
            $result = $correctionModel->update($id_cor, $id_exam, $image2, $remarque, $note);
            
            if ($result === true) {
                header("Location: correction1.php?update=success");
                exit();
            } else {
                $errorMessage = "❌ Erreur lors de la mise à jour: " . $result;
            }
        } catch (Exception $e) {
            $errorMessage = "❌ Erreur: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Correction</title>
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
        .image-preview {
            margin-top: 10px;
            max-width: 200px;
            display: block;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .file-info {
            font-size: 13px;
            color: #666;
        }
    </style>
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
          <a class="nav-link" href="correction1.php">
            <i class="fas fa-list"></i>
            <span class="ms-2">Liste Corrections</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-primary text-white" href="updatecorrection1.php">
            <i class="fas fa-edit"></i>
            <span class="ms-2">Modifier Correction</span>
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
            <li class="breadcrumb-item"><a href="correction1.php">Corrections</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier Correction #<?= htmlspecialchars($id_cor) ?></li>
          </ol>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-header pb-0">
          <h6>Modification de correction</h6>
        </div>

        <div class="card-body">
          <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="id_exam" class="form-label">ID Examen</label>
              <input type="text" name="id_exam" id="id_exam" class="form-control" 
                     value="<?= htmlspecialchars($correction['id_exam'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
              <label for="remarque" class="form-label">Remarque</label>
              <textarea name="remarque" id="remarque" class="form-control" required><?= htmlspecialchars($correction['remarque'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
              <label for="note" class="form-label">Note</label>
              <input type="text" name="note" id="note" class="form-control" 
                     value="<?= htmlspecialchars($correction['note'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
              <label for="image2" class="form-label">Image de correction</label>
              <input type="file" name="image2" id="image2" class="form-control" accept="image/*">
              <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF</div>
              <?php if (!empty($correction['image2'])): ?>
                <img id="imagePreview" class="image-preview" 
                     src="../uploads/<?= htmlspecialchars($correction['image2']) ?>" 
                     alt="Aperçu de la correction actuelle">
              <?php else: ?>
                <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image" style="display:none;">
              <?php endif; ?>
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-save me-1"></i> Enregistrer
              </button>
              <a href="correction1.php" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Annuler
              </a>
            </div>
          </form>
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
    // Image preview function
    document.getElementById("image2").addEventListener("change", function() {
      const file = this.files[0];
      const preview = document.getElementById("imagePreview");

      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = "block";
        };
        reader.readAsDataURL(file);
      } else {
        preview.style.display = "none";
      }
    });

    // Auto-hide flash messages after 2 seconds
    setTimeout(function() {
        const messages = document.querySelectorAll('.alert');
        messages.forEach(message => {
            message.style.display = 'none';
        });
    }, 2000);
  </script>
</body>
</html>