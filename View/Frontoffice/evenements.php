<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once(__DIR__ . "/../../controller/evenementcontroller.php");
require_once(__DIR__ . "/../../controller/participationcontroller.php");

// Configuration des chemins
define('BASE_UPLOADS_PATH', $_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/');
define('BASE_UPLOADS_URL', '/projettt/projettt/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/');
$defaultImage = "../assets/img/default-event.jpg";
$isLoggedIn = isset($_SESSION['user_id']);
// Récupération du numéro de page depuis l'URL (par défaut 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Nombre d'événements par page
$parPage = 3;

try {
    $eventController = new EvenementController();
    $participationController = new ParticipationController();
    $result = $eventController->listeEvenementPagines($page, $parPage);
    
    $events = $result['events'];
    $totalPages = $result['totalPages'];
    $currentPage = $result['page'];
} catch (Exception $e) {
    error_log("Erreur de chargement des événements: " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    die("Une erreur est survenue lors du chargement des événements.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Plateforme Éducative</title>
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
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--accent);
            color: var(--white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
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
        
        .page-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 0.5rem;
            text-align: center;
            font-weight: 600;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--accent);
            border-radius: 2px;
        }
        
        /* Event Grid */
        .event-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            padding: 20px 0;
        }
        
        .event-card {
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            position: relative;
            border: 1px solid var(--light-gray);
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: var(--medium-gray);
        }
        
        .event-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--accent);
            color: var(--white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
        }
        
        .event-image-container {
            height: 230px;
            overflow: hidden;
            background: var(--light);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .event-card:hover .event-image {
            transform: scale(1.05);
        }
        
        .no-image {
            background: linear-gradient(45deg, var(--light), #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            color: var(--medium-gray);
        }
        
        .event-info {
            padding: 20px;
            border-top: 1px solid var(--light-gray);
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .event-title {
    text-align: center;
    margin: 0 0 15px 0;
    flex-grow: 1;  /* Prend l'espace disponible */
    display: flex;
    align-items: center;
    justify-content: center;
}
        
        .event-description {
            font-size: 0.85rem;
            color: var(--medium-gray);
            margin-bottom: 15px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        
        event-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: flex-end;  /* Aligne les éléments en bas */
}
        
.event-meta-item {
    font-size: 0.9rem;
    color: var(--secondary);
    display: flex;
    align-items: center;
}

.event-meta-item i {
    margin-right: 5px;
    color: var(--accent);
}
        
        .event-type {
            background-color: var(--primary);
            color: var(--white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .btn-inscription {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: var(--secondary);
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
        
        .btn-inscription:hover {
            background-color: var(--accent);
            transform: translateY(-2px);
        }
        
        .btn-inscription.registered {
            background-color: var(--primary);
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
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: var(--medium-gray);
            grid-column: 1/-1;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--light-gray);
        }

        /* Styles pour la modale */
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
            max-width: 700px;
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
        
        .modal-event-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .modal-event-title {
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .modal-event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .modal-event-meta-item {
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            color: var(--secondary);
        }
        
        .modal-event-meta-item i {
            margin-right: 8px;
            color: var(--accent);
            font-size: 1.1rem;
        }
        
        .modal-event-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .modal-event-body {
            line-height: 1.7;
            color: var(--dark);
        }
        
        .modal-event-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }
        
        /* Responsive */
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
            
            .event-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .footer-container {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                padding: 20px;
            }
            
            .modal-event-title {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .event-container {
                grid-template-columns: 1fr;
            }
            
            .modal-event-meta {
                flex-direction: column;
                gap: 8px;
            }
            
            .modal-event-actions {
                flex-direction: column;
            }
        }
        /* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 40px;
    padding: 20px 0;
}

.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 6px;
    background-color: var(--white);
    color: var(--dark);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    border: 1px solid var(--light-gray);
}

.page-link:hover {
    background-color: var(--primary);
    color: var(--white);
    border-color: var(--primary);
}

.page-link.active {
    background-color: var(--primary);
    color: var(--white);
    border-color: var(--primary);
    font-weight: 600;
}

.page-link i {
    font-size: 0.9rem;
}

@media (max-width: 480px) {
    .page-link {
        width: 36px;
        height: 36px;
        font-size: 0.9rem;
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
                
                <div class="nav-icons">
                    <?php if ($isLoggedIn): ?>
                        <a href="profil.php" class="nav-icon"><i class="fas fa-user"></i></a>
                        <a href="deconnexion.php" class="nav-icon"><i class="fas fa-sign-out-alt"></i></a>
                    <?php else: ?>
                        <a href="connexion.php" class="nav-icon"><i class="fas fa-sign-in-alt"></i></a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <h1 class="page-title">Nos Événements</h1>
            
            <div class="event-container">
                <?php if (!empty($events)): ?>
                    <!-- Affichage des événements -->
                    <?php foreach ($events as $event): 
                        $isRegistered = $isLoggedIn && $participationController->checkParticipation($_SESSION['user_id'], $event['id']);
                        $eventDate = new DateTime($event['date_evenement']);
                        $isPastEvent = $eventDate < new DateTime();
                    ?>
                        <div class="event-card" onclick="openEventModal(<?= $event['id'] ?>)">
                            <?php if ($isPastEvent): ?>
                                <span class="event-badge">Terminé</span>
                            <?php elseif ($isRegistered): ?>
                                <span class="event-badge">Inscrit</span>
                            <?php endif; ?>
                            
                            <div class="event-image-container">
                                <?php if (!empty($event['image']) && file_exists(BASE_UPLOADS_PATH . $event['image'])): ?>
                                    <img src="<?= BASE_UPLOADS_URL . htmlspecialchars($event['image']) ?>" 
                                         class="event-image" 
                                         alt="<?= htmlspecialchars($event['titre']) ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-calendar-day fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="event-info">
                                <h3 class="event-title"><?= htmlspecialchars($event['titre']) ?></h3>
                                <div class="event-meta">
                                    <div class="event-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($event['lieu']) ?>
                                    </div>
                                    <div class="event-meta-item">
                                        <i class="far fa-calendar-alt"></i>
                                        <?= $eventDate->format('d/m/Y') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <!-- Première page -->
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=1" class="page-link" title="Première page">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Page précédente -->
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?>" class="page-link" title="Page précédente">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Pages autour de la current -->
                            <?php
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            
                            for ($i = $start; $i <= $end; $i++): 
                            ?>
                                <a href="?page=<?= $i ?>" class="page-link <?= $i == $currentPage ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Page suivante -->
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>" class="page-link" title="Page suivante">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Dernière page -->
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $totalPages ?>" class="page-link" title="Dernière page">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times fa-3x"></i>
                        <p>Aucun événement disponible pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal pour les détails de l'événement -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modalEventContent">
                <!-- Le contenu sera chargé dynamiquement via JavaScript -->
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

    <script>
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.event-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
            
            // Fermer la modale quand on clique sur la croix
            document.querySelector('.close-modal').addEventListener('click', function() {
                document.getElementById('eventModal').style.display = 'none';
            });
            
            // Fermer la modale quand on clique en dehors
            window.addEventListener('click', function(event) {
                if (event.target === document.getElementById('eventModal')) {
                    document.getElementById('eventModal').style.display = 'none';
                }
            });
        });

        // Fonction pour ouvrir la modale avec les détails de l'événement
        function openEventModal(eventId) {
            // Afficher un loader
            document.getElementById('modalEventContent').innerHTML = `
                <div style="text-align: center; padding: 50px 0;">
                    <i class="fas fa-spinner fa-spin fa-3x" style="color: var(--accent);"></i>
                    <p style="margin-top: 20px;">Chargement des détails...</p>
                </div>
            `;
            
            document.getElementById('eventModal').style.display = 'block';
            
            fetch(`evenement-details.php?id=${eventId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('modalEventContent').innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('modalEventContent').innerHTML = `
                    <div style="text-align: center; padding: 50px 0; color: var(--accent);">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                        <p style="margin-top: 20px;">Erreur lors du chargement des détails</p>
                        <a href="evenement-details.php?id=${eventId}" class="btn-inscription">
                            <i class="fas fa-external-link-alt"></i> Voir la page complète
                        </a>
                    </div>
                `;
            });
        }
    </script>
</body>
</html>