<?php
require_once(__DIR__ . "/../../../../controller/certificatcontroller.php");
$controller = new CertificatController();
$certificats = $controller->listeCertificat();
?>

<!DOCTYPE html>
<html lang="en">
<head>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>


  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Gestion des Certificats</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .product-image {
      max-width: 80px;
      max-height: 80px;
      border-radius: 4px;
      object-fit: cover;
    }
    .action-buttons .btn {
      margin: 2px;
      padding: 0.3rem 0.6rem;
    }
    .product-image {
      background-color: red;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="ms-1 text-sm text-dark">Gestion Certificat</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/dashboard.html">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/tables.html">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Utilisateurs</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/certificat.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">Certificats</span>
          </a>
        </li>
        <li>
          <a class="nav-link text-dark" href="../pages/cadeau.php">
            <i class="material-symbols-rounded opacity-5">card_giftcard</i>
            <span class="nav-link-text ms-1">Cadeaux</span>
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
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Certificats</li>
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
              <label class="form-label">Rechercher...</label>
              <input type="text" class="form-control" id="searchInput">
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Liste des certificats</h6>
                <div class="container mt-2">
                  <a href="addcertificat.php" class="btn btn-success">
                    <i class="material-symbols-rounded">add</i> Ajouter un certificat
                  </a>
                </div>
              </div>
            </div>
















            

            <div class="card-body" id="pdf-container">
  <!-- Table to display the certificates -->
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>ID</th>
        <th>Nom</th>
        <th>Type</th>
        <th>Objet</th>
        <th>Date Demande</th>
        <th>Statut</th>
        <th>Niveau</th>
        <th>Image</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($certificats)): ?>
        <?php $index = 1; ?>
        <?php foreach ($certificats as $certificat): ?>
          <tr>
            <td><?= $index ?></td>
            <td><?= htmlspecialchars($certificat['id']) ?></td>
            <td><?= htmlspecialchars($certificat['nom']) ?></td>
            <td><?= htmlspecialchars($certificat['type']) ?></td>
            <td><?= htmlspecialchars($certificat['objet']) ?></td>
            <td>
              <?php
              try {
                $date = new DateTime($certificat['date_demande']);
                echo htmlspecialchars($date->format('Y-m-d'));
              } catch (Exception $e) {
                echo htmlspecialchars($certificat['date_demande']);
              }
              ?>
            </td>
            <td><?= htmlspecialchars($certificat['status']) ?></td>
            <td><?= htmlspecialchars($certificat['niveau']) ?></td>
            <td>
              <?php if (!empty($certificat['image'])): ?>
                <a href="/Uploads/<?= htmlspecialchars($certificat['image']) ?>" target="_blank">
                  <img src="/Uploads/<?= htmlspecialchars($certificat['image']) ?>" 
                       alt="Certificat Image" 
                       class="certificat-image" 
                       style="max-width: 100px;">
                </a>
              <?php else: ?>
                <span class="text-muted">Aucune image</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php $index++; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="9" class="no-certificats">Aucun certificat disponible pour le moment</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Bouton pour générer le PDF -->
  <div class="pdf-btn-container">
    <button class="pdf-btn" onclick="generatePDF()">
      <i class="fas fa-file-pdf"></i> Générer PDF
    </button>
  </div>
</div>

<script>
  // Fonction pour générer le PDF
  function generatePDF() {
    const element = document.getElementById('pdf-container');
    
    if (element) {
      // Options du PDF
      const opt = {
        margin: 10,
        filename: 'certificats_' + new Date().toISOString().split('T')[0] + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
          scale: 2,
          useCORS: true // Enable CORS for images
        },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };
      
      // Générer le PDF
      html2pdf().set(opt).from(element).save();
    } else {
      alert("L'élément à convertir en PDF n'a pas été trouvé.");
    }
  }
</script>


















            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="productsTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Image</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Objet</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Date demande</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Niveau</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Cadeau</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($certificats as $certificat): ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($certificat['nom']) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <?php if (!empty($certificat['image'])): ?>
                          <img src="<?= '../uploads/' . htmlspecialchars($certificat['image']) ?>" class="product-image" alt="Image certificat">
                        <?php else: ?>
                          <span class="text-muted">Aucune image</span>
                        <?php endif; ?>
                      </td>
                      <td class="align-middle text-center"><?= htmlspecialchars($certificat['type']) ?></td>
                      <td class="align-middle text-center"><?= htmlspecialchars($certificat['objet']) ?></td>
                      <td class="align-middle text-center"><?= htmlspecialchars($certificat['date_demande']) ?></td>
                      <td class="align-middle text-center"><?= htmlspecialchars($certificat['status']) ?></td>
                      <td class="align-middle text-center"><?= htmlspecialchars($certificat['niveau']) ?></td>
                      <td class="align-middle text-center action-buttons">
                        <a href="certificat_update.php?id=<?= $certificat['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                          <i class="material-symbols-rounded">edit</i>
                        </a>
                        <a href="certificat_delete.php?id=<?= $certificat['id'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce certificat ?');">
                          <i class="material-symbols-rounded">delete</i>
                        </a>
                      </td>
                      <td class="align-middle text-center">
                        <?php if (!empty($certificat['id'])): ?>
                          <a href="addcadeau.php?id=<?= htmlspecialchars($certificat['id']) ?>" class="btn btn-success">
                            <i class="material-symbols-rounded">add</i> Ajouter un cadeau
                          </a>
                        <?php else: ?>
                          <span class="text-danger">ID manquant</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
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
</body>
</html>