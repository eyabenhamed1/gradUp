<?php
session_start();
require_once(__DIR__ . "/../../../../Controller/Correction1Controller.php");
require_once(__DIR__ . "/../../../../Controller/typeexamcontroller.php");
require_once(__DIR__ . "/../../../../Config.php");

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../Frontoffice/auth/login.php");
    exit;
}

$pdo = config::getConnexion();
$controller = new Correction1($pdo);

$typeExamController = new TypeExamController();
$allTypes = $typeExamController->getAllTypes();

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
    $remarque = $_POST['remarque'] ?? '';
    $note = $_POST['note'] ?? '';
    $id_exam = $_POST['id_exam'] ?? null;
    $image2 = '';
    $user_id = $_SESSION['user_id']; // Get user ID from session
    
    // Validate id_exam first
    if (empty($id_exam)) {
        $errorMessage = "Veuillez sélectionner un type d'examen.";
    } else {
        // Validate that id_exam exists in typeexam table
        $examExists = false;
        foreach ($allTypes as $type) {
            if ($type['id'] == $id_exam) {
                $examExists = true;
                break;
            }
        }
        
        if (!$examExists) {
            $errorMessage = "Type d'examen invalide sélectionné.";
        } else {
            // Check if this exam already has a correction
            $existingCorrection = $controller->getOneByExamId($id_exam);
            if ($existingCorrection) {
                $errorMessage = "Une correction existe déjà pour cet examen (ID: $id_exam)";
            } else {
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
                    // Handle file upload
                    if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
                        $imageTmp = $_FILES['image2']['tmp_name'];
                        $imageName = basename($_FILES['image2']['name']);
                        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array($imageExtension, $allowedExtensions)) {
                            $targetDir = __DIR__ . "/../../uploads/";
                            if (!file_exists($targetDir)) {
                                mkdir($targetDir, 0777, true);
                            }

                            $newFileName = uniqid() . '.' . $imageExtension;
                            $targetFilePath = $targetDir . $newFileName;

                            if (move_uploaded_file($imageTmp, $targetFilePath)) {
                                $image2 = $newFileName;
                                
                                // Create the correction with user_id
                                $result = $controller->create($id_exam, $image2, $remarque, $note, $user_id);
                                
                                if (is_string($result) && strpos($result, 'Error') === false) {
                                    unset($_SESSION['exam_id']); // Clear the session ID after successful creation
                                    header("Location: correction1.php?add=success");
                                    exit();
                                } else {
                                    $errorMessage = "Erreur lors de l'ajout: " . $result;
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
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Ajouter une Correction | Dashboard</title>
  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-200">
  <?php include("../includes/sidebar.php"); ?>
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include("../includes/navbar.php"); ?>
    
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Ajouter une Nouvelle Correction</h6>
              </div>
            </div>
            
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <?php if ($errorMessage): ?>
                  <div class="alert alert-danger text-white mx-4">
                    <?= htmlspecialchars($errorMessage) ?>
                  </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="p-4">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="input-group input-group-outline mb-4">
                        <select name="id_exam" class="form-control" required>
                          <option value="" disabled selected>Sélectionnez un examen</option>
                          <?php foreach ($allTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type['id']) ?>">
                              Examen #<?= htmlspecialchars($type['id']) ?> - <?= htmlspecialchars($type['type_name']) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="input-group input-group-outline mb-4">
                        <input type="text" 
                               name="note" 
                               class="form-control" 
                               placeholder="Note (ex: 15/20 ou A+)"
                               required
                               pattern="^([0-9]{1,2}(\/20)?|[A-Za-z][+-]?)$">
                      </div>
                    </div>
                  </div>

                  <div class="input-group input-group-outline mb-4">
                    <textarea name="remarque" 
                              class="form-control" 
                              rows="4" 
                              placeholder="Commentaires de correction"
                              required></textarea>
                  </div>

                  <div class="input-group input-group-outline mb-4">
                    <input type="file" 
                           name="image2" 
                           class="form-control" 
                           accept="image/*"
                           required>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">Enregistrer la Correction</button>
                    <a href="correction1.php" class="btn btn-outline-primary w-100">Retour à la Liste</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
</body>
</html>