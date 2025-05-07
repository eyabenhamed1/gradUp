<?php
// Inclure la librairie PHP QRcode
include('C:/xampp/htdocs/ProjetWeb2A/lib/phpqrcode/qrlib.php'); // Assurez-vous que le chemin est correct

// Récupérer les certificats via le contrôleur
require_once(__DIR__ . "/../../../../controller/certificatcontroller.php");
$controller = new CertificatController();
$certificats = $controller->listeCertificat();

// Créer un dossier pour stocker les QR codes s'il n'existe pas
$qrcodeDir = __DIR__ . "/qrcodes/";
if (!file_exists($qrcodeDir)) {
    if (!mkdir($qrcodeDir, 0755, true)) {
        die('Erreur: Impossible de créer le dossier qrcodes.');
    }
}

// Générer les QR codes pour chaque certificat
foreach ($certificats as $certificat) {
    // Créer une chaîne de texte contenant les informations du certificat
    $certificatInfo = "Nom: " . $certificat['nom'] . "\n" . 
                      "Type: " . $certificat['type'] . "\n" . 
                      "Objet: " . $certificat['objet'] . "\n" . 
                      "Date de demande: " . $certificat['date_demande'] . "\n" . 
                      "Status: " . $certificat['status'] . "\n" . 
                      "Niveau: " . $certificat['niveau'];

    // Générer le QR code avec ces informations
    $fileName = "qrcode_" . $certificat['id'] . ".png";
    $filePath = $qrcodeDir . $fileName; // Chemin où les QR codes seront sauvegardés

}
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
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

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
    .qrcode-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }
    .qrcode-modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 5px;
      text-align: center;
    }
    .qrcode canvas {
      width: 150px;
      height: 150px;
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
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/certificat.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">Certificats</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/cadeau.php">
            <i class="material-symbols-rounded opacity-5">card_giftcard</i>
            <span class="nav-link-text ms-1">Cadeaux</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

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

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                <h6 class="text-white text-capitalize">Liste des certificats</h6>
                <a href="addcertificat.php" class="btn btn-success">
                  <i class="material-symbols-rounded">add</i> Ajouter un certificat
                </a>
              </div>
            </div>

            <div class="card-body px-0 pb-2">
              <div class="d-flex justify-content-end px-3">
                <button class="btn btn-danger mb-3" onclick="generatePDF()">
                  <i class="fas fa-file-pdf"></i> Générer PDF
                </button>
              </div>

              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="productsTable">
                  <thead>
                    <tr>
                      <th>Nom</th>
                      <th class="text-center">Image</th>
                      <th>Type</th>
                      <th class="text-center">Objet</th>
                      <th class="text-center">Date demande</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Niveau</th>
                      <th class="text-center">Actions</th>
                      <th class="text-center">Cadeau</th>
                      <th class="text-center">QR Code</th>
                    </tr>
                  </thead>
                  <tbody>
                  <tbody>
<?php foreach ($certificats as $certificat): ?>
<tr>
  <td><h6 class="mb-0 text-sm"><?= htmlspecialchars($certificat['nom']) ?></h6></td>

  <td class="align-middle text-center">
    <?php if (!empty($certificat['image'])): ?>
      <img src="<?= '../Uploads/' . htmlspecialchars($certificat['image']) ?>" class="product-image" alt="Image certificat">
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
  <button class="btn btn-info"
        onclick="showQRCodeModal(this)"
        data-info="<?= htmlspecialchars(json_encode([
          'nom' => $certificat['nom'],
          'type' => $certificat['type'],
          'objet' => $certificat['objet'],
          'date_demande' => $certificat['date_demande'],
          'status' => $certificat['status'],
          'niveau' => $certificat['niveau']
        ])) ?>">
  <i class="fas fa-qrcode"></i> Voir QR Code
</button>

  </td>

  <td class="align-middle text-center">
    <a href="addcadeau.php?id=<?= htmlspecialchars($certificat['id']) ?>" class="btn btn-success">
      <i class="material-symbols-rounded">add</i> Ajouter un cadeau
    </a>
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

  <!-- Contenu PDF caché -->
<div id="pdf-content" style="display:none;">
  <div class="text-center">
    <img src="logo.jpeg" alt="Logo" style="width: 100px; margin-bottom: 10px;">
    <h2 style="margin-bottom: 20px;">Liste des Certificats</h2>
  </div>

  <table style="width:100%; border-collapse: collapse;">
    <thead>
      <tr style="background-color: #f2f2f2;">
        <th style="border:1px solid #999; padding:6px;">Nom</th>
        <th style="border:1px solid #999; padding:6px;">Type</th>
        <th style="border:1px solid #999; padding:6px;">Objet</th>
        <th style="border:1px solid #999; padding:6px;">Date</th>
        <th style="border:1px solid #999; padding:6px;">Status</th>
        <th style="border:1px solid #999; padding:6px;">Niveau</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($certificats as $certificat): ?>
      <tr>
        <td style="border:1px solid #999; padding:6px;"><?= htmlspecialchars($certificat['nom']) ?></td>
        <td style="border:1px solid #999; padding:6px;"><?= htmlspecialchars($certificat['type']) ?></td>
        <td style="border:1px solid #999; padding:6px;"><?= htmlspecialchars($certificat['objet']) ?></td>
        <td style="border:1px solid #999; padding:6px;"><?= htmlspecialchars($certificat['date_demande']) ?></td>
        <td style="border:1px solid #999; padding:6px;"><?= htmlspecialchars($certificat['status']) ?></td>
        <td style="border:1px solid #999; padding:6px;"><?= htmlspecialchars($certificat['niveau']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

  <!-- Modal pour afficher le QR code -->
  <div id="qrcodeModal" class="qrcode-modal">
    <div class="qrcode-modal-content">
      <canvas id="qrcodeCanvas"></canvas>
      <button onclick="closeQRCodeModal()">Fermer</button>
    </div>
  </div>

  <script>

function generatePDF() {
  const element = document.getElementById('pdf-content');
  if (!element) {
    alert('Le contenu du PDF est introuvable.');
    return;
  }

  // Afficher temporairement le contenu
  element.style.display = 'block';

  const opt = {
    margin:       10,
    filename:     'certificats_' + new Date().toISOString().slice(0,10) + '.pdf',
    image:        { type: 'jpeg', quality: 0.98 },
    html2canvas:  { scale: 2 },
    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
  };

  html2pdf().set(opt).from(element).save().then(() => {
    // Re-cacher le contenu après génération
    element.style.display = 'none';
  }).catch(err => {
    console.error('Erreur PDF:', err);
    element.style.display = 'none';
  });
}



function showQRCodeModal(buttonElement) {
    const infoJson = buttonElement.getAttribute('data-info');
    const info = JSON.parse(infoJson);

    const certificatInfo = 
        "Nom: " + info.nom + "\n" +
        "Type: " + info.type + "\n" +
        "Objet: " + info.objet + "\n" +
        "Date de demande: " + info.date_demande + "\n" +
        "Status: " + info.status + "\n" +
        "Niveau: " + info.niveau;

    // Vide l'ancien QR code si besoin
    const canvas = document.getElementById('qrcodeCanvas');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Génère le nouveau QR code
    QRCode.toCanvas(canvas, certificatInfo, function(error) {
        if (error) console.error(error);
    });

    document.getElementById('qrcodeModal').style.display = "flex";
}


    function closeQRCodeModal() {
      document.getElementById('qrcodeModal').style.display = "none";
    }
  </script>
</body>
</html>
