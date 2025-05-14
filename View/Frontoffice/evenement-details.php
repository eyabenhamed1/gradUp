<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$isLoggedIn = isset($_SESSION['id']);
$userId = $isLoggedIn ? $_SESSION['id'] : null;
require_once(__DIR__ . "/../../controller/evenementcontroller.php");
require_once(__DIR__ . "/../../controller/participationcontroller.php");

// Validation de l'ID de l'événement
if (!isset($_GET['id'])) {
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header("HTTP/1.1 400 Bad Request");
        header("Location: evenements.php");
        exit();
    }
    // Pour les requêtes AJAX, on retourne une erreur en JSON
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID manquant']);
    exit();
}

$eventId = (int)$_GET['id'];
$eventController = new EvenementController();
$participationController = new ParticipationController();

// Récupération de l'événement
try {
    $event = $eventController->getEvenementById($eventId);
    
    if (!$event) {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header("HTTP/1.1 404 Not Found");
            header("Location: evenements.php");
            exit();
        }
        // Pour les requêtes AJAX
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Événement non trouvé']);
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur lors de la récupération de l'événement: " . $e->getMessage());
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header("Location: evenements.php");
        exit();
    }
    // Pour les requêtes AJAX
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}
// Récupérer les informations de l'étudiant connecté
$user = [];
if (isset($_SESSION['id'])) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/UtilisateurC.php');
    $userController = new UtilisateurC();
    $user =  $userController->getUtilisateurById($_SESSION['id']);
}
// Initialisation des variables
$isRegistered = false;
$message = '';
$alertClass = '';

/////////
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_utilisateur'])) {
    header('Content-Type: application/json');
    try {
        // Configuration du fuseau horaire
        date_default_timezone_set('Europe/Paris');

        // Validation des données
        $studentId = (int)$_POST['id_utilisateur'];
        $nom = trim($_POST['nom']);
        $email = trim($_POST['email']);
        $telephone = trim($_POST['telephone'] ?? '');
        $statut = $_POST['statut'] ?? 'confirme';
        $comments = trim($_POST['commentaire'] ?? '');

        // Vérifications
        if (empty($nom)) {
            throw new Exception('Le nom est requis');
        }
        if (empty($telephone)) {
            throw new Exception('Le numéro de téléphone est requis');
        }
        if (!preg_match("/^[0-9+\-\s()]*$/", $telephone)) {
            throw new Exception('Le numéro de téléphone n\'est pas valide');
        }
        
        // Vérifier si l'utilisateur est déjà inscrit
        if ($participationController->checkParticipation($studentId, $eventId)) {
            throw new Exception('Vous êtes déjà inscrit à cet événement');
        }
        
        // Créer la participation avec les données complètes
        $participationData = [
            'id' => $studentId,
            'id_evenement' => $eventId,
            'email' => $email,
            'telephone' => $telephone,
            'statut' => $statut,
            'commentaire' => $comments,
            'date_inscription' => date('Y-m-d H:i:s')
        ];

        // Créer la participation
        $participationId = $participationController->createParticipation($participationData);

        if (!$participationId) {
            throw new Exception('Erreur lors de la création de la participation');
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Inscription réussie ! Un email de confirmation vous a été envoyé.',
            'participationId' => $participationId
        ]);
        exit();

    } catch (Exception $e) {
        error_log("Erreur d'inscription: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage()
        ]);
        exit();
    }
}
// Vérification si déjà inscrit (pour affichage)
if (isset($_SESSION['registration_success'])) {
    $isRegistered = true;
    unset($_SESSION['registration_success']);
} elseif (isset($_POST['id_utilisateur'])) {
    $isRegistered = $participationController->checkParticipation((int)$_POST['id_utilisateur'], $eventId);
}


// Chemin des images
define('BASE_UPLOADS_PATH', 'C:/xampp/htdocs/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/');
define('BASE_UPLOADS_URL', '/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/');
$defaultImage = "../../assets/img/default-event.jpg";
$eventDate = new DateTime($event['date_evenement']);
$isPastEvent = $eventDate < new DateTime();

// Vérifier si c'est une requête AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax) {
    // Retourner juste le contenu de l'événement pour la modale
    ob_start();
    ?>
    <div class="modal-event-header">
        <h2 class="modal-event-title"><?= htmlspecialchars($event['titre']) ?></h2>
        <div class="modal-event-meta">
            <div class="modal-event-meta-item">
                <i class="far fa-calendar-alt"></i>
                <?= $eventDate->format('d/m/Y à H:i') ?>
            </div>
            <div class="modal-event-meta-item">
                <i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars($event['lieu']) ?>
            </div>
            <div class="modal-event-meta-item">
                <i class="fas fa-tag"></i>
                <?= htmlspecialchars($event['type_evenement']) ?>
            </div>
        </div>
    </div>
    
    <?php if (!empty($event['image']) && file_exists(BASE_UPLOADS_PATH . $event['image'])): ?>
        <img src="<?= BASE_UPLOADS_URL . htmlspecialchars($event['image']) ?>" 
             alt="<?= htmlspecialchars($event['titre']) ?>" 
             class="modal-event-image"
             onerror="this.onerror=null; this.src='<?= $defaultImage ?>'; this.classList.add('no-image');">
    <?php else: ?>
        <img src="<?= $defaultImage ?>" alt="Image par défaut" class="modal-event-image">
    <?php endif; ?>
    
    <div class="modal-event-body">
        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
    </div>
    
    <div class="modal-event-actions">
        <a href="evenement-details.php?id=<?= $eventId ?>" class="btn-inscription">
            <i class="fas fa-info-circle"></i> Plus de détails
        </a>
        <?php if (!$isPastEvent && !$isRegistered && isset($_SESSION['id'])): ?>
            <button onclick="document.getElementById('openRegistrationModal').click()" class="btn-inscription">
                <i class="fas fa-user-plus"></i> S'inscrire
            </button>
        <?php endif; ?>
    </div>
    <?php
    $content = ob_get_clean();
    echo $content;
    exit();
}
?>

<!DOCTYPE html>
<!-- Le reste de votre code HTML normal... -->
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['titre']) ?> - Plateforme Éducative</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        
        /* Header */
        .header {
            background-color: var(--white);
            color: var(--dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        /* Navigation */
        .nav {
            display: flex;
            align-items: center;
        }
        
        .nav-list {
            display: flex;
            list-style: none;
        }
        
        .nav-item {
            margin-left: 1.5rem;
            position: relative;
        }
        
        .nav-link {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
        }
        
        .nav-link:hover {
            color: var(--accent);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent);
            transition: var(--transition);
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .nav-icons {
            display: flex;
            align-items: center;
            margin-left: 2rem;
        }
        
        .nav-icon {
            color: var(--dark);
            font-size: 1.2rem;
            margin-left: 1.2rem;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            text-decoration: none;
        }
        
        .nav-icon:hover {
            color: var(--accent);
            transform: translateY(-2px);
        }
        
        /* Main Content */
        .main {
            padding: 2rem 0;
            min-height: calc(100vh - 120px);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .event-hero {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .event-hero-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        
        .event-hero-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 2rem;
            color: var(--white);
        }
        
        .event-hero-title {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .event-hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .event-hero-badge {
            display: flex;
            align-items: center;
            background-color: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .event-hero-badge i {
            margin-right: 0.5rem;
        }
        
        .event-content {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
        }
        
        .event-details {
            background-color: var(--white);
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .event-section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .event-section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--accent);
            border-radius: 2px;
        }
        
        .event-description {
            line-height: 1.7;
            color: var(--dark);
            margin-bottom: 2rem;
        }
        
        .event-info-list {
            list-style: none;
        }
        
        .event-info-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
        }
        
        .event-info-item:last-child {
            border-bottom: none;
        }
        
        .event-info-label {
            font-weight: 600;
            color: var(--dark);
        }
        
        .event-info-value {
            color: var(--secondary);
        }
        .form-control[readonly] {
    background-color: #f8f9fa;
    cursor: not-allowed;
    border-color: #e9ecef;
}
        .event-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .event-action-card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .event-action-icon {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }
        
        .btn-event-action {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-event-action:hover {
            background-color: var(--accent);
        }
        
        .btn-event-action.registered {
            background-color: var(--secondary);
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark);
            color: var(--white);
            padding: 3rem 0 1.5rem;
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
        
        .footer-links {
            list-style: none;
        }
        
        .footer-link {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-link a {
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }
        
        .footer-link a:hover {
            color: var(--accent);
            padding-left: 5px;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
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
            transition: var(--transition);
        }
        
        .social-link:hover {
            background-color: var(--accent);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            color: var(--medium-gray);
        }

        /* Styles pour les modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .close-modal {
            color: var(--medium-gray);
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .close-modal:hover {
            color: var(--accent);
        }
        
        .modal-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.2);
        }
        
        .form-text {
            font-size: 0.8rem;
            color: var(--medium-gray);
            margin-top: 0.25rem;
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: var(--accent);
            color: var(--white);
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-submit:hover {
            background-color: var(--primary-dark);
        }
        
        .registration-success {
            text-align: center;
            padding: 2rem 0;
        }
        
        .registration-success-icon {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .event-content {
                grid-template-columns: 1fr;
            }
            
            .event-hero-title {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                padding: 1rem;
            }
            
            .nav {
                width: 100%;
                margin-top: 1rem;
                justify-content: space-between;
            }
            
            .nav-list {
                display: none;
            }
            
            .nav-icons {
                margin-left: auto;
            }
            
            .event-hero-image {
                height: 300px;
            }
            
            .event-hero-title {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .event-hero-image {
                height: 250px;
            }
            
            .event-hero-title {
                font-size: 1.5rem;
            }
            
            .event-hero-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Plateforme Éducative
            </a>
            
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="index.php" class="nav-link">Accueil</a></li>
                    <li class="nav-item"><a href="evenements.php" class="nav-link active">Événements</a></li>
                    <li class="nav-item"><a href="mes-inscriptions.php" class="nav-link active">Mes Inscriptions</a></li>
                    <li class="nav-item"><a href="cours.php" class="nav-link">Cours</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
                </ul>
            </nav>
            <div class="nav-icons">
                <a href="profile.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    Profil
                </a>
                <a href="auth/logout.php" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </a>
            </div>
        
        </div>
    </header>

    <main class="main">
        <div class="container">
            <!-- Hero section de l'événement -->
            <div class="event-hero">
                <?php if (!empty($event['image']) && file_exists(BASE_UPLOADS_PATH . $event['image'])): ?>
                    <img src="<?= BASE_UPLOADS_URL . htmlspecialchars($event['image']) ?>" 
                         class="event-hero-image" 
                         alt="<?= htmlspecialchars($event['titre']) ?>"
                         loading="lazy">
                <?php else: ?>
                    <img src="<?= $defaultImage ?>" class="event-hero-image" alt="Image par défaut">
                <?php endif; ?>
                
                <div class="event-hero-overlay">
                    <h1 class="event-hero-title"><?= htmlspecialchars($event['titre']) ?></h1>
                    
                    <div class="event-hero-meta">
                        <div class="event-hero-badge">
                            <i class="far fa-calendar-alt"></i>
                            <?= $eventDate->format('d/m/Y à H:i') ?>
                        </div>
                        <div class="event-hero-badge">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($event['lieu']) ?>
                        </div>
                        <div class="event-hero-badge">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($event['type_evenement']) ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contenu principal -->
             
            <div class="event-content">
                <!-- Détails de l'événement -->
                <div class="event-details">
                    <h2 class="event-section-title">
                        <i class="fas fa-info-circle"></i> Description
                    </h2>
                    
                    <div class="event-description">
                        <?= nl2br(htmlspecialchars($event['description'])) ?>
                    </div>
                    
                    <h2 class="event-section-title">
                        <i class="fas fa-calendar-check"></i> Informations pratiques
                    </h2>
                    
                    <ul class="event-info-list">
                        <li class="event-info-item">
                            <span class="event-info-label"><i class="far fa-calendar-alt me-2"></i>Date et heure</span>
                            <span class="event-info-value"><?= $eventDate->format('d/m/Y à H:i') ?></span>
                        </li>
                        <li class="event-info-item">
                            <span class="event-info-label"><i class="fas fa-map-marker-alt me-2"></i>Lieu</span>
                            <span class="event-info-value"><?= htmlspecialchars($event['lieu']) ?></span>
                        </li>
                        <li class="event-info-item">
                            <span class="event-info-label"><i class="fas fa-tag me-2"></i>Type d'événement</span>
                            <span class="event-info-value"><?= htmlspecialchars($event['type_evenement']) ?></span>
                        </li>
                        <li class="event-info-item">
                            <span class="event-info-label"><i class="fas fa-user-tie me-2"></i>Organisateur</span>
                            <span class="event-info-value"><?= htmlspecialchars($event['organisateur'] ?? 'Esprit') ?></span>
                        </li>
                    </ul>
                </div>
                
                <!-- Sidebar avec actions -->
                <div class="event-sidebar">
                    <div class="event-action-card">
                        <div class="event-action-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <?php 
                        $currentStatus = $isLoggedIn ? $participationController->getParticipationStatus($userId, $eventId) : null;?>
                        <?php 
                        if ($isPastEvent): ?>
                            <p>Cet événement est terminé</p>
                            <a href="evenements.php" class="btn-event-action">
                                <i class="fas fa-arrow-left"></i> Voir les événements à venir
                            </a>
                        <?php elseif ($currentStatus === 'confirme'): ?>
                            <p>Vous êtes déjà inscrit à cet événement</p>
                            <a href="mes-inscriptions.php" class="btn-event-action registered">
                                <i class="fas fa-list"></i> Voir mes inscriptions
                            </a>
                            <!-- Formulaire corrigé -->
                            <form method="POST" 
                                  action="/projettt/projettt/ProjetWeb2A/View/Frontoffice/annuler_inscription.php" 
                                  class="d-grid">
                                <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                <button type="submit" class="btn-event-action btn-danger">
                                    <i class="fas fa-times"></i> Annuler l'inscription
                                </button>
                            </form>
                        <?php elseif ($currentStatus === 'annulé'): ?>
                            <p>Vous avez annulé votre inscription. Vous pouvez vous réinscrire.</p>
                            <button id="openRegistrationModal" class="btn-event-action">
                                <i class="fas fa-user-plus"></i> S'inscrire
                            </button>
                        <?php else: ?>
                            <p>Participez à cet événement</p>
                            <button id="openRegistrationModal" class="btn-event-action">
                                <i class="fas fa-user-plus"></i> S'inscrire
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="event-action-card">
                        <div class="event-action-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <div style="margin-top: 2rem;">
                            <a href="evenements.php" class="btn-event-action" style="margin-bottom: 1rem;">
                                <i class="fas fa-arrow-left"></i> Retour aux événements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal d'inscription avec Bootstrap -->
    <div class="modal fade" id="inscriptionModal" tabindex="-1" aria-labelledby="inscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inscriptionModalLabel">Inscription à l'événement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="inscriptionForm" method="POST">
                        <input type="hidden" name="id_utilisateur" value="<?= $_SESSION['user']['id'] ?>">
                        <input type="hidden" name="id_evenement" value="<?= $eventId ?>">
                        
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>">
                            <div class="form-text">Vous recevrez la confirmation d'inscription à cette adresse</div>
                        </div>

                        <!-- Téléphone -->
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                            <div class="form-text">Pour vous contacter en cas de besoin</div>
                        </div>

                        <!-- Commentaire -->
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3" placeholder="Vos attentes, questions ou besoins particuliers..."></textarea>
                        </div>

                        <input type="hidden" name="statut" value="confirme">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Confirmer l'inscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast de confirmation -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="confirmationToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="toastMessage">Inscription réussie !</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <h3>Plateforme Éducative</h3>
                <p>Votre plateforme d'apprentissage et de partage de connaissances.</p>
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
                    <li class="footer-link"><a href="index.php">Accueil</a></li>
                    <li class="footer-link"><a href="evenements.php">Événements</a></li>
                    <li class="footer-link"><a href="cours.php">Cours</a></li>
                    <li class="footer-link"><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Informations</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="a-propos.php">À propos</a></li>
                    <li class="footer-link"><a href="mentions-legales.php">Mentions légales</a></li>
                    <li class="footer-link"><a href="confidentialite.php">Confidentialité</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Contact</h3>
                <ul class="footer-links">
                    <li class="footer-link"><i class="fas fa-map-marker-alt"></i> 123 Rue de l'Éducation, Paris</li>
                    <li class="footer-link"><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                    <li class="footer-link"><i class="fas fa-envelope"></i> contact@plateforme-educative.fr</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Plateforme Éducative. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour ouvrir le modal d'inscription
        function openInscriptionModal() {
            const modal = new bootstrap.Modal(document.getElementById('inscriptionModal'));
            modal.show();
        }

        // Gestionnaire de soumission du formulaire
        document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(window.location.href, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fermer le modal
                    bootstrap.Modal.getInstance(document.getElementById('inscriptionModal')).hide();
                    
                    // Redirection immédiate vers mes-inscriptions.php
                    window.location.href = 'mes-inscriptions.php';
                } else {
                    // Afficher l'erreur dans une alerte stylisée
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    alertDiv.innerHTML = `
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.error || 'Une erreur est survenue'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('.modal-body').insertBefore(alertDiv, document.querySelector('#inscriptionForm'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la communication avec le serveur');
            });
        });

        // Remplacer le bouton d'inscription existant par celui qui utilise Bootstrap
        document.getElementById('openRegistrationModal').onclick = openInscriptionModal;
    </script>
</body>
</html>