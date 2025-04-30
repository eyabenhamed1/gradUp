<?php
session_start();
require_once(__DIR__ . "/../../../../controller/Correction1Controller.php");
require_once(__DIR__ . "/../../../../Config.php");

$pdo = config::getConnexion();
$controller = new Correction1($pdo);

$successMessage = "";
$errorMessage = "";

// Get exam ID from session if available
$examId = $_SESSION['exam_id'] ?? null;

// Check if this exam already has a correction
if ($examId) {
    $existingCorrection = $controller->getOne($examId);
    if ($existingCorrection) {
        $errorMessage = "Une correction existe déjà pour cet examen (ID: $examId)";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_cor = $_POST['id_cor'] ?? $examId;
    $remarque = $_POST['remarque'] ?? '';
    $note = $_POST['note'] ?? '';
    $image2 = '';

    // Validate note format and value
    $noteValid = false;
    if (preg_match('/^([0-9]{1,2})(\/([0-9]{1,2}))?$/', $note, $matches)) {
        // Numeric format (15 or 15/20)
        $numerator = (float)$matches[1];
        $denominator = isset($matches[3]) ? (float)$matches[3] : 20;
        
        if ($numerator > $denominator) {
            $errorMessage = "La note ($numerator) ne peut pas être supérieure au dénominateur ($denominator)";
        } elseif ($denominator != 20) {
            $errorMessage = "Le dénominateur doit être 20 (format: XX/20)";
        } elseif ($numerator > 20) {
            $errorMessage = "La note ne peut pas dépasser 20";
        } else {
            $noteValid = true;
        }
    } elseif (preg_match('/^[A-Za-z][+-]?$/', $note)) {
        // Letter grade format (A, B+, C-)
        $noteValid = true;
    } else {
        $errorMessage = "Format de note invalide. Utilisez un format comme 15, 18/20, A+, B, etc.";
    }

    if ($noteValid) {
        // Verify this exam doesn't already have a correction
        $existingCorrection = $controller->getOne($id_cor);
        if ($existingCorrection) {
            $errorMessage = "Une correction existe déjà pour cet examen (ID: $id_cor)";
        } else {
            if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
                $imageTmp = $_FILES['image2']['tmp_name'];
                $imageName = basename($_FILES['image2']['name']);
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
                        $image2 = $newFileName;

                        if (!empty($id_cor)) {
                            $result = $controller->create($id_cor, $image2, $remarque, $note);
                            
                            if ($result === true) {
                                unset($_SESSION['exam_id']); // Clear the session ID after successful creation
                                header("Location: correction1.php?add=success");
                                exit();
                            } else {
                                $errorMessage = "Erreur lors de l'ajout: " . $result;
                            }
                        } else {
                            $errorMessage = "ID d'examen manquant.";
                        }
                    } else {
                        $errorMessage = "Erreur lors de l'upload de l'image.";
                    }
                } else {
                    $errorMessage = "Format d'image non valide (jpg, jpeg, png, gif).";
                }
            } else {
                $errorMessage = "Veuillez sélectionner une image.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Correction | Gradup</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet">
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <style>
        /* Enhanced Form Styling */
        .form-section {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-header {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #344767;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.875rem;
        }
        
        .form-control {
            border: 1px solid #d2d6da;
            border-radius: 8px;
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
        
        .form-control:disabled {
            background-color: #f0f2f5;
            opacity: 1;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-text {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        /* File Upload */
        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .file-upload-input {
            width: 100%;
            height: calc(2.75rem + 2px);
            opacity: 0;
            position: absolute;
            z-index: 2;
        }
        
        .file-upload-label {
            display: block;
            padding: 0.75rem 1rem;
            border: 1px solid #d2d6da;
            border-radius: 8px;
            background-color: #f8f9fa;
            cursor: pointer;
        }
        
        .file-upload-label::after {
            content: "Parcourir";
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: #5e72e4;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 0 8px 8px 0;
        }
        
        /* Image Preview */
        .image-preview-container {
            margin-top: 1rem;
            text-align: center;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            border: 1px dashed #d2d6da;
            padding: 0.5rem;
            background-color: #f8f9fa;
        }
        
        /* Buttons */
        .btn-action {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        /* Alerts */
        .alert {
            border-radius: 8px;
            padding: 1rem 1.25rem;
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs bg-white fixed-start">
    <div class="sidenav-header">
      <a class="navbar-brand m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" alt="Gradup Logo">
        <span class="ms-1 font-weight-bold">Gestion Corrections</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <ul class="navbar-nav">
      <li class="nav-item">
          <a class="nav-link" href="correction1.php">
            <i class="fas fa-list"></i>
            <span class="ms-2">Liste des Corrections</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-primary text-white" href="addcorrection1.php">
            <i class="fas fa-plus-circle"></i>
            <span class="ms-2">Ajouter une Correction</span>
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
            <li class="breadcrumb-item active" aria-current="page">Nouvelle Correction</li>
          </ol>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="form-section">
            <div class="form-header">
              <h1 class="form-title">
                <i class="fas fa-plus-circle me-2"></i>Ajouter une Nouvelle Correction
              </h1>
              <p class="text-muted mb-0">Remplissez les détails de la correction ci-dessous</p>
            </div>
            
            <?php if (!empty($errorMessage)): ?>
              <div class="alert alert-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($errorMessage) ?>
              </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="id_cor" class="form-label">Identifiant de l'Examen</label>
                    <input type="text" 
                           name="id_cor" 
                           id="id_cor" 
                           class="form-control <?= $examId ? 'bg-gray-200' : '' ?>" 
                           value="<?= htmlspecialchars($examId ?? '') ?>" 
                           <?= $examId ? 'readonly disabled' : 'required' ?>
                           placeholder="EX123456">
                    <?php if ($examId): ?>
                      <small class="form-text">Cet identifiant a été récupéré automatiquement depuis votre session</small>
                    <?php else: ?>
                      <small class="form-text">Entrez l'identifiant unique de l'examen à corriger</small>
                    <?php endif; ?>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="note" class="form-label">Note Attribuée</label>
                    <input type="text" 
                           name="note" 
                           id="note" 
                           class="form-control" 
                           required
                           pattern="^([0-9]{1,2}(\/20)?|[A-Za-z][+-]?)$"
                           title="Format accepté: 15, 18/20 (max 20), A+, B, etc."
                           placeholder="Ex: 15/20 ou A+"
                           oninput="validateNote(this)">
                    <small class="form-text">La note finale attribuée à cette copie (max 20, formats: 15, 18/20, A+, B)</small>
                    <div class="invalid-feedback">La note ne peut pas dépasser 20. Format accepté: 15, 18/20, A+, B, etc.</div>
                  </div>
                </div>
              </div>

              <div class="form-group mb-4">
                <label for="remarque" class="form-label">Commentaires de Correction</label>
                <textarea name="remarque" 
                          id="remarque" 
                          class="form-control" 
                          rows="5" 
                          required
                          placeholder="Détaillez les remarques sur cette correction..."></textarea>
                <small class="form-text">Vos observations détaillées sur la copie</small>
              </div>

              <div class="form-group mb-4">
                <label class="form-label">Fichier de Correction</label>
                <div class="file-upload-wrapper">
                  <input type="file" 
                         name="image2" 
                         id="image2" 
                         class="file-upload-input" 
                         accept="image/*" 
                         required
                         onchange="previewImage(this)">
                  <label for="image2" class="file-upload-label">Sélectionner un fichier...</label>
                </div>
                <small class="form-text">Formats acceptés: JPG, PNG, GIF (taille max: 5MB)</small>
                
                <div class="image-preview-container mt-3">
                  <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image" style="display:none;">
                  <small class="text-muted" id="fileNamePreview">Aucun fichier sélectionné</small>
                </div>
              </div>

              <div class="d-flex justify-content-end mt-4">
                <a href="correction1.php" class="btn btn-light btn-action me-3">
                  <i class="fas fa-times me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary btn-action" <?= $existingCorrection ?? false ? 'disabled' : '' ?>>
                  <i class="fas fa-save me-1"></i> Enregistrer la Correction
                </button>
              </div>
            </form>
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
    // Image preview function
    function previewImage(input) {
      const preview = document.getElementById('imagePreview');
      const fileNamePreview = document.getElementById('fileNamePreview');
      const file = input.files[0];
      
      if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
          fileNamePreview.textContent = file.name;
        }
        
        reader.readAsDataURL(file);
      } else {
        preview.style.display = 'none';
        fileNamePreview.textContent = 'Aucun fichier sélectionné';
      }
    }

    // Note validation function
    function validateNote(input) {
      const value = input.value;
      const noteField = document.getElementById('note');
      
      // Check for numeric format (15 or 15/20)
      if (/^[0-9]+\/?[0-9]*$/.test(value)) {
        const parts = value.split('/');
        const numerator = parseFloat(parts[0]);
        
        // If numerator exceeds 20, show error
        if (numerator > 20) {
          input.setCustomValidity("La note ne peut pas dépasser 20");
          noteField.classList.add('is-invalid');
          return;
        }
        
        // If there's a denominator, it must be 20
        if (parts.length > 1 && parseFloat(parts[1]) !== 20) {
          input.setCustomValidity("Le dénominateur doit être 20 (format: XX/20)");
          noteField.classList.add('is-invalid');
          return;
        }
      }
      
      // If we get here, the input is valid
      input.setCustomValidity('');
      noteField.classList.remove('is-invalid');
    }

    // Form validation
    (function() {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      
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
  </script>
</body>
</html>