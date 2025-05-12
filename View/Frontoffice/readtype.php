<?php
require_once(__DIR__ . "/../../controller/typeexamcontroller.php");

$controller = new TypeExamController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image3'])) {
    $uploadDir = __DIR__ . '/../Backoffice/material-dashboard-master/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileTmpPath = $_FILES['image3']['tmp_name'];
    $fileName = basename($_FILES['image3']['name']);
    $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageExtension, $allowedExtensions)) {
        $newFileName = uniqid() . '.' . $imageExtension;
        $destPath = $uploadDir . $newFileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Assuming you have a way to get the type id or name to update
            // For demonstration, let's assume type id is sent via POST as 'type_id'
            if (isset($_POST['type_id'])) {
                $typeId = (int)$_POST['type_id'];
                // Update image3 in DB
                $controller->updateTypeExamImage3($typeId, $newFileName);
                header("Location: readtype.php");
                exit();
            }
        } else {
            echo "<p style='color:red;'>Erreur lors du téléchargement de l'image3.</p>";
        }
    } else {
        echo "<p style='color:red;'>Format d'image3 non valide (jpg, jpeg, png, gif).</p>";
    }
}

$types = $controller->getAllTypes(); // Get all types
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gradup Shop - Gestion des Examens</title>
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
    .table th,
    .table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .table th {
      background-color: #3498db;
      color: #fff;
      font-weight: bold;
      border-bottom: 2px solid #2980b9; /* Darker blue line under headers */
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
    .product-image {
      max-width: 80px;
      height: auto;
      border-radius: 6px;
      cursor: pointer;
      transition: transform 0.2s ease;
    }
    .product-image:hover {
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
    .text-muted {
      color: #6c757d;
      font-style: italic;
    }
    /* Filter styles */
    .filter-container {
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .filter-container label {
      font-weight: 500;
      color: #333;
    }
    .filter-container select {
      padding: 8px 12px;
      border-radius: 4px;
      border: 1px solid #ddd;
      background-color: white;
    }
    .filter-container button {
      padding: 8px 16px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .filter-container button:hover {
      background-color: #2980b9;
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
      <a href="read_corection1.php?"#>Corrections</a>
      <a href="chat_client.php?"#>chatbot</a>
    </nav>
  </header>

  <div class="main-content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active" aria-current="page">Types d'Examens</li>
      </ol>
    </nav>

    <h2 class="section-title">Gestion des Types d'Examens</h2>

    <div class="card-body">
      <!-- Filter section -->
<div class="filter-container">
  <label for="typeFilter">Filtrer par type :</label>
  <select id="typeFilter">
    <option value="">Tous les types</option>
    <?php 
    $uniqueTypes = [];
    foreach ($types as $type) {
      if (!in_array($type['type_name'], $uniqueTypes)) {
        $uniqueTypes[] = $type['type_name'];
        echo '<option value="'.htmlspecialchars($type['type_name']).'">'.htmlspecialchars($type['type_name']).'</option>';
      }
    }
    ?>
  </select>
  <button onclick="filterExams()">Filtrer</button>
</div>

      <!-- Table to display the types -->
      <table class="table" id="examsTable">
        <thead>
      <tr>
      <th>Id Exam</th>
      <th>Nom du Type</th>
      <th>Image</th>
      <th>push ur exam</th>
      <th>Actions</th>
    </tr>
      </thead>
      <tbody>
        <?php foreach ($types as $index => $type): ?>
          <tr class="exam-row" data-type="<?= htmlspecialchars($type['type_name']) ?>">
            <td><?= htmlspecialchars($type['id']) ?></td>
            <td><?= htmlspecialchars($type['type_name']) ?></td>
            <td>
              <?php if (!empty($type['image'])): ?>
                <a href="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($type['image']) ?>" target="_blank">
                  <img src="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($type['image']) ?>" 
                       alt="Image" 
                       class="product-image" 
                       style="width: 80px; height: auto; object-fit: cover; border-radius: 6px; cursor: pointer;">
                </a>
              <?php else: ?>
                <span class="text-muted">Aucune image</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($type['image3'])): ?>
                <a href="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($type['image3']) ?>" target="_blank">
                  <img src="/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($type['image3']) ?>" 
                       alt="Image3" 
                       class="product-image" 
                       style="width: 80px; height: auto; object-fit: cover; border-radius: 6px; cursor: pointer;">
                </a>
              <?php else: ?>
                <span class="text-muted">Aucune examan</span>
              <?php endif; ?>
              <!-- Unified upload form -->
  <form method="POST" enctype="multipart/form-data" style="margin-top: 10px;">
    <input type="hidden" name="type_id" value="<?= htmlspecialchars($type['id']) ?>">
    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #333;">
      Ajouter ou Modifier ton examan :
      <input type="file" name="image3" accept="image/*" required style="display: block; margin-top: 4px;">
    </label>
    <button type="submit" style="background-color: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
      Uploader ur examan
    </button>
  </form>
            </td>
            <td>
              
              <?php if (!empty($type['image'])): ?>
<button class="pdf-btn" style="background-color:rgb(114, 105, 233); color: white; border: none; padding: 10px 16px; border-radius: 6px; font-size: 16px; cursor: pointer;" onclick="generateSinglePDF('<?= htmlspecialchars($type['image']) ?>', '<?= htmlspecialchars($type['id']) ?>')">
  <i class="fas fa-file-pdf" style="margin-right: 8px;"></i> Télécharger PDF
</button>
<?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

  <footer>
    &copy; 2025 Gradup Shop. Tous droits réservés. | Contact : gradup@edu.tn | +216 99 999 999
  </footer>

  <!-- JS -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  
  <script>
    function filterExams() {
      const filterValue = document.getElementById('typeFilter').value.toLowerCase();
      const rows = document.querySelectorAll('.exam-row');
      
      rows.forEach(row => {
        const rowType = row.getAttribute('data-type').toLowerCase();
        if (filterValue === '' || rowType.includes(filterValue)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }
    
    // Add event listener for Enter key in filter select
    document.getElementById('typeFilter').addEventListener('keyup', function(event) {
      if (event.key === 'Enter') {
        filterExams();
      }
    });
  </script>

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
          pdf.save('_exam_' + examId + '_' + dateStr + '.pdf');
          resolve();
        };
        
        img.onerror = () => {
          alert("Erreur lors du chargement de l'image.");
          resolve();
        };
      });
    }
  </script>

  
