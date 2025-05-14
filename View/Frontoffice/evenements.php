<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once(__DIR__ . "/../../controller/evenementcontroller.php");
require_once(__DIR__ . "/../../controller/participationcontroller.php");

// Configuration des chemins avec chemin absolu vers material-dashboard-master
define('BASE_UPLOADS_PATH', 'C:/xampp/htdocs/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/');
define('BASE_UPLOADS_URL', '/ProjetWeb2A/View/Backoffice/material-dashboard-master/uploads/');
$defaultImage = "../../assets/img/default-event.jpg";
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
            font-size: 2.5rem;
            color: var(--dark);
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--accent);
            border-radius: 2px;
        }
        
        /* Event Grid */
        .event-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }
        
        .event-card {
            background: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            position: relative;
            border: 1px solid var(--light-gray);
            display: flex;
            flex-direction: column;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .event-card:hover .event-image {
            transform: scale(1.1);
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
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .event-title {
            font-size: 1.25rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 600;
            text-align: center;
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
        
        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: auto;
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--medium-gray);
            font-size: 0.9rem;
        }
        
        .event-meta-item i {
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--accent);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
            margin-top: 1rem;
            width: 100%;
        }
        
        .btn-inscription:hover {
            background-color: var(--primary);
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
            .event-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }
            
            .footer-container {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                padding: 20px;
            }
            
            .modal-event-title {
                font-size: 2rem;
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
            gap: 0.5rem;
            margin-top: 3rem;
        }

        .page-link {
            padding: 0.75rem 1rem;
            background-color: var(--white);
            color: var(--dark);
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            transition: var(--transition);
        }

        .page-link:hover,
        .page-link.active {
            background-color: var(--accent);
            color: var(--white);
            border-color: var(--accent);
        }

        .logout-link {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            background-color: var(--accent);
            color: var(--white);
            text-decoration: none;
            border-radius: 4px;
            transition: var(--transition);
            font-weight: 500;
            margin-left: 20px;
        }

        .logout-link:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .logout-link i {
            margin-right: 8px;
            font-size: 1.1em;
        }

        /* Style for the inscriptions link container */
        .inscriptions-link-container {
            max-width: 1200px;
            margin: 0 auto 20px;
            padding: 0 2rem;
            text-align: right;
        }

        .inscriptions-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--accent);
            color: var(--white);
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .inscriptions-link:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .inscriptions-link i {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="inscriptions-link-container">
        <a href="mes-inscriptions.php" class="inscriptions-link">
            <i class="fas fa-calendar-check"></i>
            Mes Inscriptions
        </a>
    </div>

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
                                <?php 
                                    $imagePath = str_replace('/', DIRECTORY_SEPARATOR, BASE_UPLOADS_PATH . $event['image']);
                                    $imageUrl = BASE_UPLOADS_URL . $event['image'];
                                    
                                    // Debug information
                                    if (isset($_GET['debug'])) {
                                        echo "<!-- Image Debug:
                                        Full Path: " . $imagePath . "
                                        URL Path: " . $imageUrl . "
                                        Image exists: " . (file_exists($imagePath) ? 'Yes' : 'No') . "
                                        Image name: " . $event['image'] . "
                                        -->";
                                    }
                                    
                                    if (!empty($event['image']) && file_exists($imagePath)): 
                                ?>
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                         class="event-image" 
                                         alt="<?= htmlspecialchars($event['titre']) ?>"
                                         onerror="this.onerror=null; this.src='<?= $defaultImage ?>'; this.parentElement.classList.add('no-image');"
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