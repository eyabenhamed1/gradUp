<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Model/correction1.php');

$conn = config::getConnexion();
$correctionModel = new Correction1($conn);
$corrections = $correctionModel->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gradup Shop - Liste des Corrections</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet">
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f8;
    }
    header {
      background-color: #3498db;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      display: flex;
      align-items: center;
    }
    .logo img {
      height: 40px;
      margin-right: 10px;
    }
    nav a {
      color: white;
      margin: 0 1rem;
      text-decoration: none;
      font-weight: 500;
    }
    .main-content {
      padding: 30px;
      background-color: #f8f9fa;
      min-height: calc(100vh - 200px);
      position: relative;
      padding-bottom: 70px;
    }
    .card-body {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      padding: 30px;
      margin-top: 20px;
    }
    .table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
      border-spacing: 0;
      border-radius: 8px;
      overflow: hidden;
    }
    .table th, .table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .table th {
      background-color: #3498db;
      color: #fff;
      font-weight: bold;
    }
    .table td {
      background-color: #fff;
      font-size: 14px;
      color: #333;
    }
    .table td img {
      border-radius: 6px;
      max-width: 80px;
      height: auto;
      object-fit: cover;
    }
    .table tbody tr:hover {
      background-color: #f1f1f1;
    }
    .correction-image {
      max-width: 80px;
      height: auto;
      border-radius: 6px;
      cursor: pointer;
      transition: transform 0.2s ease;
    }
    .correction-image:hover {
      transform: scale(1.1);
    }
    .section-title {
      text-align: center;
      margin: 2rem 0 1rem;
      font-size: 1.8rem;
      color: #333;
    }
    footer {
      background-color: #3498db;
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
    }
    .breadcrumb {
      background-color: transparent;
      padding: 0;
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 20px;
    }
    .breadcrumb-item a {
      color: #3498db;
      text-decoration: none;
    }
    .breadcrumb-item a:hover {
      text-decoration: underline;
    }
    .breadcrumb-item.active {
      color: #6c757d;
    }
    .no-corrections {
      text-align: center;
      padding: 20px;
      color: #6c757d;
      font-style: italic;
    }
    .pdf-btn-container {
      position: absolute;
      bottom: 20px;
      right: 30px;
    }
    .pdf-btn {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      margin: 5px 0;
    }
    .pdf-btn i {
      margin-right: 5px;
    }
    .pdf-btn:hover {
      background-color: #2c80b4;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .image-container {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .action-buttons {
      display: flex;
      gap: 5px;
      margin-top: 5px;
    }
  </style>
</head>

<body>
  <header>
    <div class="logo">
      <img src="logo.jpeg" alt="logo">
      <h1>Gradup Shop</h1>
    </div>
    <nav>
      <a href="#">Accueil</a>
      <a href="#">Boutique</a>
      <a href="#">Cours</a>
      <a href="#">Forum</a>
      <a href="#">Événements</a>
      <a href="#">Dons</a>
      <a href="readtype.php?"#>exams</a>
      <a href="chat_client.php?"#>chatbot</a>
    </nav>
  </header>

  <div class="main-content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active" aria-current="page">Liste des Corrections</li>
      </ol>
    </nav>

    <h2 class="section-title">Liste des Corrections</h2>

    <div class="card-body" id="pdf-container">
      <table class="table">
        <thead>
          <tr>
            <th>ID Examen</th>
            <th>Image</th>
            <th>Remarque</th>
            <th>Note</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($corrections && $corrections->rowCount() > 0): ?>
            <?php while($row = $corrections->fetch(PDO::FETCH_ASSOC)): ?>
              <tr>
                <td><?= htmlspecialchars($row['id_exam']) ?></td>
                <td>
                  <div class="image-container">
                    <?php if (!empty($row['image2'])): ?>
                      <a href="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($row['image2']) ?>" target="_blank">
                        <img src="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($row['image2']) ?>" 
                             alt="Correction" 
                             class="correction-image"
                             data-image-src="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($row['image2']) ?>">
                      </a>
                    <?php else: ?>
                      <span class="text-muted">Aucune image</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td><?= htmlspecialchars($row['remarque']) ?></td>
                <td><?= htmlspecialchars($row['note']) ?></td>
                <td>
                  <?php if (!empty($row['image2'])): ?>
                    <button class="pdf-btn" onclick="generateSinglePDF('<?= htmlspecialchars($row['image2']) ?>', '<?= htmlspecialchars($row['id_exam']) ?>')">
                      <i class="fas fa-file-pdf"></i> Télécharger PDF
                    </button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="no-corrections">Aucune correction disponible pour le moment</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer>
    &copy; 2025 Gradup Shop. Tous droits réservés. | Contact : gradup@edu.tn | +216 99 999 999
  </footer>

  <!-- Scripts -->
  <script>
    async function generateSinglePDF(imagePath, examId) {
      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF();
      
      const fullImagePath = '/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/' + imagePath;
      const img = new Image();
      img.crossOrigin = "anonymous";
      img.src = fullImagePath;

      await new Promise(resolve => {
        img.onload = () => {
          const pageWidth = pdf.internal.pageSize.getWidth();
          const ratio = img.height / img.width;
          const pageHeight = pageWidth * ratio;

          pdf.addImage(img, 'JPEG', 0, 0, pageWidth, pageHeight);
          
          // Add exam ID as filename
          const dateStr = new Date().toISOString().slice(0, 10);
          pdf.save('correction_exam_' + examId + '_' + dateStr + '.pdf');
          resolve();
        };
        
        img.onerror = () => {
          alert("Erreur lors du chargement de l'image.");
          resolve();
        };
      });
    }
  </script>
</body>
</html>