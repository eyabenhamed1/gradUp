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
            $result = $correctionModel->update($id_cor, $image2, $remarque, $note);
            
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
        /* Enhanced Form Styling */
        .form-label {
            font-weight: 600;
            color: #344767;
            margin-bottom: 8px;
            display: block;
            font-size: 0.875rem;
        }
        
        .form-control {
            border: 1px solid #d2d6da;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 2px rgba(94, 114, 228, 0.2);
            background-color: #fff;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        /* File Upload Styling */
        .file-upload-container {
            margin-bottom: 1.5rem;
        }
        
        .file-upload-label {
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .file-upload-input {
            width: 100%;
        }
        
        .file-upload-info {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        
        /* Image Preview */
        .image-preview-container {
            margin-top: 1rem;
            text-align: center;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 0.5rem;
            border: 1px dashed #d2d6da;
            padding: 0.5rem;
            background-color: #f8f9fa;
        }
        
        /* Button Styling */
        .btn-action {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        /* Alert Styling */
        .alert {
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
        }
        
        /* Card Styling */
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Section Styling */
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #344767;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
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
          <h5 class="section-title">Modification de correction</h5>
        </div>

        <div class="card-body pt-0">
          <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success text-white">
              <i class="fas fa-check-circle me-2"></i>
              <?= htmlspecialchars($successMessage) ?>
            </div>
          <?php endif; ?>
          
          <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger text-white">
              <i class="fas fa-exclamation-circle me-2"></i>
              <?= htmlspecialchars($errorMessage) ?>
            </div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="mb-4">
              <label for="id_exam" class="form-label">Identifiant de l'Examen (clé étrangère)</label>
              <input type="text" name="id_exam" id="id_exam" class="form-control" 
                     value="<?= htmlspecialchars($correction['id_exam'] ?? '') ?>" 
                     required
                     placeholder="EX123456">
              <small class="text-muted">Entrez l'identifiant de l'examen associé</small>
            </div>

            <div class="mb-4">
              <label for="remarque" class="form-label">Commentaires de correction</label>
              <textarea name="remarque" id="remarque" class="form-control" required><?= htmlspecialchars($correction['remarque'] ?? '') ?></textarea>
              <small class="text-muted">Saisissez vos remarques détaillées sur cette correction</small>
            </div>

            <div class="mb-4">
              <label for="note" class="form-label">Évaluation</label>
              <input type="text" name="note" id="note" class="form-control" 
                     value="<?= htmlspecialchars($correction['note'] ?? '') ?>" 
                     required
                     placeholder="Donnez une note (ex: 15/20)">
              <small class="text-muted">Note attribuée à cette correction</small>
            </div>

            <div class="mb-4 file-upload-container">
              <label for="image2" class="form-label">Fichier de correction</label>
              <div class="custom-file">
                <input type="file" name="image2" id="image2" 
                       class="form-control file-upload-input" 
                       accept="image/*"
                       onchange="previewImage(this)">
                <div class="file-upload-info">Formats supportés: JPG, PNG, GIF (Max. 5MB)</div>
              </div>
              
              <div class="image-preview-container mt-3">
                <?php if (!empty($correction['image2'])): ?>
                  <img id="imagePreview" class="image-preview" 
                       src="../uploads/<?= htmlspecialchars($correction['image2']) ?>" 
                       alt="Aperçu de la correction actuelle">
                  <div class="text-muted mt-2">Image actuelle</div>
                <?php else: ?>
                  <img id="imagePreview" class="image-preview" 
                       src="#" 
                       alt="Aperçu de la nouvelle image" 
                       style="display: none;">
                <?php endif; ?>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <a href="correction1.php" class="btn btn-light btn-action me-3">
                <i class="fas fa-times me-1"></i> Annuler
              </a>
              <button type="submit" class="btn btn-primary btn-action">
                <i class="fas fa-save me-1"></i> Enregistrer
              </button>
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
    // Form validation
    (function() {
      'use strict';
      
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      const forms = document.querySelectorAll('.needs-validation');
      
      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
        .forEach(function(form) {
          form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
              event.preventDefault();
              event.stopPropagation();
            }
            
            form.classList.add('was-validated');
          }, false);
        });
    })();
    
    // Image preview function
    function previewImage(input) {
      const preview = document.getElementById('imagePreview');
      const file = input.files[0];
      
      if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
      }
    }
  </script>
</body>
</html>