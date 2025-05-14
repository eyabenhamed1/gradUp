<?php
session_start();
require_once(__DIR__ . "/../../controller/typeexamcontroller.php");
require_once(__DIR__ . "/../../controller/Correction1Controller.php");
require_once(__DIR__ . "/../../Config.php");

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$controller = new TypeExamController();
$pdo = config::getConnexion();
$correctionController = new Correction1Controller($pdo);

// Get user's corrections
$userCorrections = $correctionController->getCorrectionsByUser($_SESSION['user_id']);

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
            if (isset($_POST['type_id'])) {
                $typeId = (int)$_POST['type_id'];
                $controller->updateTypeExamImage3($typeId, $newFileName);
                header("Location: readtype.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "Erreur lors du téléchargement de l'image3.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Format d'image3 non valide (jpg, jpeg, png, gif).";
        $_SESSION['message_type'] = "danger";
    }
}

// Get all types for reference
$types = $controller->getAllTypes();

// Create a lookup array for type names
$typeNames = [];
foreach ($types as $type) {
    $typeNames[$type['id']] = $type['type_name'];
}

// Gestion du panier en session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartCount = array_reduce($_SESSION['cart'], function($carry, $item) {
    return $carry + $item['quantity'];
}, 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Examens - Gradup</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--text);
            font-size: 2.5rem;
            font-weight: 600;
            position: relative;
        }

        .page-title:after {
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

        /* Table Styles */
        .commandes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .commandes-table th, 
        .commandes-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        .commandes-table th {
            background-color: var(--primary);
            color: var(--white);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        .commandes-table tr:nth-child(even) {
            background-color: rgba(236, 240, 241, 0.5);
        }

        .commandes-table tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 5px;
        }

        .btn-edit {
            background-color: var(--accent);
            color: white;
        }

        .btn-edit:hover {
            background-color: #c0392b;
        }

        .btn-delete {
            background-color: var(--primary-dark);
            color: white;
        }

        .btn-delete:hover {
            background-color: #1a252f;
        }

        .btn-pdf {
            background-color: #e74c3c;
            color: white;
        }

        .btn-pdf:hover {
            background-color: #c0392b;
        }

        .btn-disabled {
            background-color: var(--medium-gray);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-en-cours {
            background-color: #f39c12;
            color: white;
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
        .status-validee {
            background-color: #2ecc71;
            color: white;
        }

        .no-orders {
            text-align: center;
            padding: 3rem;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .no-orders p {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
            display: inline-block;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-success {
            background: #E8F5E9;
            color: #2E7D32;
            border: 1px solid #A5D6A7;
        }

        .alert-danger {
            background: #FFEBEE;
            color: #C62828;
            border: 1px solid #FFCDD2;
        }

        .produit-image {
            max-width: 80px;
            height: auto;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .produit-image:hover {
            transform: scale(1.1);
        }

        .correction-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }

        .status-corrected {
            background-color: #d4edda;
            color: #155724;
        }

        .welcome-message {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .welcome-message h2 {
            color: var(--primary);
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="main">
        <div class="container">
            <h1 class="page-title">Mes Corrections</h1>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                    <?= $_SESSION['message'] ?>
                </div>
                <?php 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            <?php endif; ?>

            <div class="welcome-message">
                <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur'); ?></h2>
                <p>Voici vos examens et corrections</p>
            </div>
            
            <?php if (empty($userCorrections)): ?>
                <div class="no-orders">
                    <p>Vous n'avez pas encore de corrections.</p>
                </div>
            <?php else: ?>
                <table class="commandes-table">
                    <thead>
                        <tr>
                            <th>Type d'Examen</th>
                            <th>Image</th>
                            <th>Note</th>
                            <th>Remarque</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userCorrections as $correction): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($typeNames[$correction['id_exam']] ?? 'Type inconnu') ?>
                                </td>
                                <td>
                                    <?php if ($correction['image2']): ?>
                                        <img src="../Backoffice/material-dashboard-master/uploads/<?= htmlspecialchars($correction['image2']) ?>" 
                                             alt="Correction" 
                                             class="produit-image">
                                    <?php else: ?>
                                        <span class="text-muted">Pas d'image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <?= htmlspecialchars($correction['note']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($correction['remarque']) ?></td>
                                <td><?= htmlspecialchars($correction['date_cor'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="correction-status <?= $correction['note'] ? 'status-corrected' : 'status-pending' ?>">
                                        <?= $correction['note'] ? 'Corrigé' : 'En attente' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
    <div class="footer-container">
        <div class="footer-col">
            <h3>Gradup Shop</h3>
            <p>Votre boutique en ligne préférée pour des produits de qualité à des prix imbattables.</p>
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
                <li class="footer-link"><i class="fas fa-shopping-bag"></i> <a href="boutique.php">Boutique</a></li>
                <li class="footer-link"><i class="fas fa-graduation-cap"></i> <a href="elearning.php">E-learning</a></li>
                <li class="footer-link"><i class="fas fa-calendar-alt"></i> <a href="evenements.php">Événements</a></li>
                <li class="footer-link"><i class="fas fa-certificate"></i> <a href="certificats.php">Certificats</a></li>
            </ul>
        </div>
        
        <div class="footer-col">
            <h3>Informations</h3>
            <ul class="footer-links">
                <li class="footer-link"><i class="fas fa-info-circle"></i> <a href="a-propos.php">À propos de nous</a></li>
                <li class="footer-link"><i class="fas fa-truck"></i> <a href="livraison.php">Livraison</a></li>
                <li class="footer-link"><i class="fas fa-exchange-alt"></i> <a href="retours.php">Politique de retour</a></li>
                <li class="footer-link"><i class="fas fa-file-contract"></i> <a href="conditions.php">Conditions générales</a></li>
                <li class="footer-link"><i class="fas fa-lock"></i> <a href="confidentialite.php">Politique de confidentialité</a></li>
            </ul>
        </div>
        
        <div class="footer-col">
            <h3>Contactez-nous</h3>
            <ul class="footer-links">
                <li class="footer-link"><i class="fas fa-map-marker-alt"></i> 123 Rue de la République, Tunis</li>
                <li class="footer-link"><i class="fas fa-phone"></i> +216 12 345 678</li>
                <li class="footer-link"><i class="fas fa-envelope"></i> contact@gradupshop.tn</li>
                <li class="footer-link"><i class="fas fa-clock"></i> Lun-Ven: 9h-18h</li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Gradup Shop. Tous droits réservés.</p>
    </div>
</footer>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('navList').classList.toggle('show');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.querySelector('.nav');
            const navList = document.getElementById('navList');
            const menuToggle = document.getElementById('menuToggle');
            
            if (!nav.contains(event.target) && navList.classList.contains('show')) {
                navList.classList.remove('show');
            }
        });

        // Add smooth scroll padding for fixed header
        document.documentElement.style.setProperty('scroll-padding-top', '80px');
    });

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

    <!-- JS -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</body>
</html>