<?php
require_once(__DIR__ . "/../../../../controller/typeexamcontroller.php");

$controller = new typeexamController();

$successMessage = "";
$errorMessage = "";

if (!isset($_GET['id'])) {
    die("ID manquant pour la mise à jour.");
}

$id = (int)$_GET['id'];
$produit = null;
try {
    $db = config::getConnexion();
    $query = $db->prepare("SELECT * FROM typeexam WHERE id = :id");
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $produit = $query->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération du type : " . $e->getMessage());
}

if (!$produit) {
    die("type introuvable.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;
    if ($id === null) {
        die("ID manquant pour la mise à jour.");
    }

    $uploadDir = __DIR__ . "/../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $updateImage = false;
    $newFileName = null;

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($imageExtension, $allowedExtensions)) {
            $newFileName = uniqid() . '.' . $imageExtension;
            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $updateImage = true;
            } else {
                $errorMessage = "Erreur lors du téléchargement de l'exam.";
            }
        } else {
            $errorMessage = "Format d'image non supporté.";
        }
    }

    if ($updateImage) {
        $updateResult = $controller->updateTypeExamImage((int)$id, $newFileName);
    } else {
        $errorMessage = "Veuillez sélectionner une image à télécharger.";
    }

    if (isset($updateResult) && $updateResult) {
        header("Location: typeexam.php?update=success");
        exit();
    } elseif (!isset($errorMessage)) {
        $errorMessage = "Erreur lors de la mise à jour de l'image en base de données.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un type</title>
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
            <li class="breadcrumb-item"><a href="typeexam.php">Types d'Examens</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier un type</li>
          </ol>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-header pb-0">
          <h6>Modifier le type</h6>
        </div>

        <div class="card-body">
          <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
            <div class="mb-3">
              <label for="type" class="form-label">Nom du type</label>
              <input name="type" id="type" class="form-control" value="<?= htmlspecialchars($produit['type_name']) ?>" disabled>
              <input type="hidden" name="old_type" value="<?= htmlspecialchars($produit['type_name']) ?>">
              <input type="hidden" name="id" value="<?= htmlspecialchars($produit['id']) ?>">
            </div>

            <div class="mb-3">
              <label for="image" class="form-label">Image :</label>
              <input type="file" name="image" id="image" class="form-control" accept="image/*">
              <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF</div>
              <?php if (!empty($produit['image'])): ?>
                <img id="imagePreview" class="image-preview" src="../uploads/<?= htmlspecialchars($produit['image']) ?>" alt="Aperçu de l'image">
              <?php else: ?>
                <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image" style="display:none;">
              <?php endif; ?>
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-save me-1"></i> Mettre à jour
              </button>
              <a href="typeexam.php" class="btn btn-secondary">
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
    function validateForm() {
      const name = document.getElementById("type").value.trim();
      const nameRegex = /^[A-Za-zÀ-ÿ\s\-']+$/;

      if (!name) {
        alert("Tous les champs sont obligatoires.");
        return false;
      }

      return true;
    }

    // Aperçu dynamique si on change l'image
    document.getElementById("image").addEventListener("change", function () {
      const file = this.files[0];
      const preview = document.getElementById("imagePreview");

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = "block";
        };
        reader.readAsDataURL(file);
      } else {
        preview.style.display = "none";
      }
    });
  </script>
</body>
</html>