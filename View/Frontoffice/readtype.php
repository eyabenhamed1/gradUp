<?php
// Define constants for paths
define('UPLOAD_DIR', __DIR__ . '/../Backoffice/material-dashboard-master/uploads/');
define('UPLOAD_URL', '../Backoffice/material-dashboard-master/uploads/');

require_once(__DIR__ . "/../../controller/typeexamcontroller.php");

$controller = new TypeExamController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image3'])) {
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }
    $fileTmpPath = $_FILES['image3']['tmp_name'];
    $fileName = basename($_FILES['image3']['name']);
    $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageExtension, $allowedExtensions)) {
        $newFileName = uniqid() . '.' . $imageExtension;
        $destPath = UPLOAD_DIR . $newFileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            if (isset($_POST['type_id'])) {
                $typeId = (int)$_POST['type_id'];
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
:root {
    --primary: #2c3e50;
    --primary-light: #34495e;
    --primary-dark: #1a252f;
    --secondary: #7f8c8d;
    --accent: #e74c3c;
    --light: #ecf0f1;
    --light-gray: #bdc3c7;
    --medium-gray: #95a5a6;
    --dark: #2c3e50;
    --dark-gray: #34495e;
    --white: #ffffff;
    --black: #000000;
    --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: var(--light);
    color: var(--dark);
    line-height: 1.6;
}

header {
    background-color: var(--primary);
    color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    transition: transform 0.3s ease;
}

.logo:hover {
    transform: translateY(-2px);
}

.logo img {
    height: 40px;
    margin-right: 10px;
    border-radius: 50%;
}

nav {
    display: flex;
    align-items: center;
    gap: 1rem;
}

nav a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    font-size: 1rem;
    padding: 0.5rem 0;
    transition: all 0.3s ease;
    position: relative;
}

nav a:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--accent);
    transition: width 0.3s ease;
}

nav a:hover {
    color: var(--accent);
}

nav a:hover:after {
    width: 100%;
}

.main-content {
    padding: 30px;
    background-color: var(--light);
    min-height: calc(100vh - 200px);
}

.card-body {
    background-color: var(--white);
    border-radius: 8px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-top: 20px;
}

.section-title {
    text-align: center;
    margin: 2rem 0 1rem;
    font-size: 1.8rem;
    color: var(--dark);
    position: relative;
}

.section-title:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: var(--primary);
    border-radius: 2px;
}

.table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
}

.table th, 
.table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid var(--light-gray);
}

.table th {
    background-color: var(--primary);
    color: var(--white);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
}

.table tr:nth-child(even) {
    background-color: rgba(236, 240, 241, 0.5);
}

.table tr:hover {
    background-color: rgba(52, 152, 219, 0.1);
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

.filter-container {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-container label {
    font-weight: 500;
    color: var(--dark);
}

.filter-container select {
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid var(--light-gray);
    background-color: white;
}

.filter-container button {
    padding: 8px 16px;
    background-color: var(--primary);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
}

.filter-container button:hover {
    background-color: var(--primary-dark);
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 20px;
}

.breadcrumb-item a {
    color: var(--primary);
    text-decoration: none;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: var(--medium-gray);
}

.text-muted {
    color: var(--medium-gray);
    font-style: italic;
}

footer {
    background-color: var(--primary);
    color: var(--white);
    text-align: center;
    padding: 1.5rem;
    margin-top: 2rem;
}

footer a {
    color: var(--white);
    text-decoration: none;
    transition: var(--transition);
}

footer a:hover {
    color: var(--accent);
}

/* Responsive Design */
@media (max-width: 992px) {
    nav {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    nav a {
        margin: 0.5rem;
    }
}
.footer {
    background-color: var(--primary-dark);
    color: var(--white);
    padding: 3rem 0 1.5rem;
    margin-top: 3rem;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.footer-col h3 {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.5rem;
    color: var(--white);
}

.footer-col h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background-color: var(--accent);
}

.footer-col p {
    color: var(--light-gray);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    line-height: 1.6;
}

.footer-links {
    list-style: none;
    margin: 0;
    padding: 0;
}

.footer-link {
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--light-gray);
    font-size: 0.9rem;
}

.footer-link i {
    color: var(--accent);
    width: 16px;
    text-align: center;
}

.footer-link a {
    color: var(--light-gray);
    text-decoration: none;
    transition: var(--transition);
}

.footer-link a:hover {
    color: var(--accent);
    padding-left: 5px;
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.social-link {
    color: var(--white);
    background-color: rgba(255, 255, 255, 0.1);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: var(--transition);
}

.social-link:hover {
    background-color: var(--accent);
    transform: translateY(-3px);
    color: var(--white);
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    margin-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 0.85rem;
    color: var(--medium-gray);
}

.footer-bottom a {
    color: var(--medium-gray);
    text-decoration: none;
    transition: var(--transition);
}

.footer-bottom a:hover {
    color: var(--accent);
}

@media (max-width: 768px) {
    .footer-container {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .footer-col h3::after {
        left: 50%;
        transform: translateX(-50%);
    }

    .footer-link {
        justify-content: center;
    }

    .social-links {
        justify-content: center;
    }
}
@media (max-width: 768px) {
    header {
        flex-direction: column;
        padding: 1rem;
    }
    
    .logo {
        margin-bottom: 1rem;
    }
    
    nav {
        width: 100%;
        justify-content: space-around;
    }
    
    .main-content {
        padding: 15px;
    }
    
    .card-body {
        padding: 20px;
    }
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--light);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--medium-gray);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--secondary);
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
      <a href="read_correction1.php?"#>Corrections</a>
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
                <a href="<?= UPLOAD_URL . htmlspecialchars($type['image']) ?>" target="_blank">
                  <img src="<?= UPLOAD_URL . htmlspecialchars($type['image']) ?>" 
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
                <a href="<?= UPLOAD_URL . htmlspecialchars($type['image3']) ?>" target="_blank">
                  <img src="<?= UPLOAD_URL . htmlspecialchars($type['image3']) ?>" 
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

<footer class="footer">
    <div class="footer-container">
        <div class="footer-col">
            <h3>Gradup Shop</h3>
            <p>Votre plateforme éducative préférée pour des cours et examens de qualité.</p>
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        
        <div class="footer-col">
            <h3>Liens rapides</h3>
            <ul class="footer-links">
                <li class="footer-link"><i class="fas fa-home"></i> <a href="index.php">Accueil</a></li>
                <li class="footer-link"><i class="fas fa-book"></i> <a href="cours.php">Cours</a></li>
                <li class="footer-link"><i class="fas fa-graduation-cap"></i> <a href="examens.php">Examens</a></li>
                <li class="footer-link"><i class="fas fa-comments"></i> <a href="forum.php">Forum</a></li>
            </ul>
        </div>
        
        <div class="footer-col">
            <h3>Ressources</h3>
            <ul class="footer-links">
                <li class="footer-link"><i class="fas fa-file-pdf"></i> <a href="ressources.php">Ressources PDF</a></li>
                <li class="footer-link"><i class="fas fa-video"></i> <a href="video-cours.php">Vidéos</a></li>
                <li class="footer-link"><i class="fas fa-question-circle"></i> <a href="faq.php">FAQ</a></li>
                <li class="footer-link"><i class="fas fa-calendar-alt"></i> <a href="evenements.php">Événements</a></li>
            </ul>
        </div>
        
        <div class="footer-col">
            <h3>Contact</h3>
            <ul class="footer-links">
                <li class="footer-link"><i class="fas fa-map-marker-alt"></i> 123 Rue de l'Éducation, Tunis</li>
                <li class="footer-link"><i class="fas fa-phone"></i> +216 12 345 678</li>
                <li class="footer-link"><i class="fas fa-envelope"></i> contact@gradupshop.tn</li>
                <li class="footer-link"><i class="fas fa-clock"></i> Lun-Ven: 8h-18h</li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Gradup Shop. Tous droits réservés. | 
           <a href="confidentialite.php">Confidentialité</a> | 
           <a href="conditions.php">Conditions d'utilisation</a>
        </p>
    </div>
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
      
      const fullImagePath = '<?= UPLOAD_URL ?>' + imagePath;
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

  