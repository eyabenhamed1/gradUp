<?php
require_once(__DIR__ . "/../../../../controller/typeexamcontroller.php");

$controller = new typeexamcontroller();

$successMessage = "";
$errorMessage = "";

// Liste des types d'examen mis à jour
$typesExistants = [
    ['id' => 1, 'type_name' => 'QCM (Questionnaire à Choix Multiples)'],
    ['id' => 2, 'type_name' => 'TP (Travaux Pratiques)'],
    ['id' => 3, 'type_name' => 'Question-Réponse'],
    ['id' => 4, 'type_name' => 'Writing'],
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedTypeId = $_POST['type'] ?? '';
    $selectedType = '';
    
    foreach ($typesExistants as $type) {
        if ($type['id'] == $selectedTypeId) {
            $selectedType = $type['type_name'];
            break;
        }
    }
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageExtension, $allowedExtensions)) {
            $targetDir = __DIR__ . "/../uploads/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $newFileName = uniqid() . '.' . $imageExtension;
            $targetFilePath = $targetDir . $newFileName;

            if (move_uploaded_file($imageTmp, $targetFilePath)) {
                $image = $newFileName;

                // Valider le type sélectionné
                if (!empty($selectedType)) {
                    $controller->insertType($selectedType, $image, null);
                    header("Location: typeexam.php?add=success");
                    exit();
                } else {
                    $errorMessage = "❌ Veuillez sélectionner un type d'examen.";
                }
            } else {
                $errorMessage = "❌ Erreur lors de l'upload de l'image.";
            }
        } else {
            $errorMessage = "❌ Format d'image non valide (jpg, jpeg, png, gif).";
        }
    } else {
        $errorMessage = "❌ Veuillez sélectionner une image.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un type</title>
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
        .form-select {
            border: 1px solid #d2d6da;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background-color: #f8f9fa;
        }
        .form-select:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 2px rgba(94, 114, 228, 0.2);
            background-color: #fff;
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
          <a class="nav-link" href="typeexam.php">
            <i class="fas fa-list"></i>
            <span class="ms-2">Liste des types</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-primary text-white" href="addtypeexam.php">
            <i class="fas fa-plus"></i>
            <span class="ms-2">Ajouter un type</span>
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
            <li class="breadcrumb-item active" aria-current="page">Ajouter un type</li>
          </ol>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-header pb-0">
          <h6>Ajouter un nouveau type d'examen</h6>
        </div>

        <div class="card-body">
          <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
            <div class="mb-3">
              <label for="type" class="form-label">Type d'examen</label>
              <select name="type" id="type" class="form-select" required>
                <option value="">-- Choisissez un type --</option>
                <?php foreach ($typesExistants as $type): ?>
                  <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['type_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="image" class="form-label">Image</label>
              <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
              <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF</div>
              <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image" style="display:none;">
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-save me-1"></i> Enregistrer
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
      const type = document.getElementById("type");
      const image = document.getElementById("image");

      if (!type.value) {
        alert("❌ Veuillez sélectionner un type d'examen.");
        return false;
      }

      if (!image.files || !image.files[0]) {
        alert("❌ Veuillez sélectionner une image.");
        return false;
      }

      return true;
    }

    // Image preview
    document.getElementById("image").addEventListener("change", function() {
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