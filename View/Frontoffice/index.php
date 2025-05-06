<?php
// Ligne à modifier - utilisez le chemin absolu correct
require_once($_SERVER['DOCUMENT_ROOT'] . 'C:\xampp\htdocs\ProjetWeb2A\Controller\produitfront.php');

session_start();

$controller = new EvenementController();
$derniersEvenements = $controller->getDerniersEvenements(3);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Plateforme Éducative</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --primary-light: #34495e;
            --primary-dark: #1a252f;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--light);
            color: var(--primary);
            line-height: 1.6;
        }
        
        /* Header */
        header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo i {
            color: var(--accent);
            margin-right: 10px;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(44, 62, 80, 0.8)), 
                        url('assets/images/hero-bg.jpg') center/cover;
            color: var(--white);
            padding: 5rem 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        /* Événements */
        .events-section {
            padding: 3rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent);
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .event-card {
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
        }
        
        .event-image {
            height: 200px;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
        }
        
        .event-info {
            padding: 1.5rem;
        }
        
        .event-date {
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        /* Footer */
        footer {
            background-color: var(--primary);
            color: var(--white);
            padding: 3rem 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <a href="index.php" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Plateforme Éducative
            </a>
            <nav>
                <?php if (isset($_SESSION['id_etudiant'])): ?>
                    <a href="profil.php" class="btn">Mon profil</a>
                <?php else: ?>
                    <a href="connexion.php" class="btn">Connexion</a>
                    <a href="inscription.php" class="btn btn-accent">Inscription</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Bienvenue sur la Plateforme Éducative</h1>
            <p>Découvrez nos événements éducatifs et formations</p>
            <a href="evenements.php" class="btn">Voir tous les événements</a>
        </div>
    </section>

    <section class="events-section">
        <div class="container">
            <h2 class="section-title">Prochains Événements</h2>
            
            <div class="events-grid">
                <?php if (!empty($derniersEvenements)): ?>
                    <?php foreach ($derniersEvenements as $event): ?>
                        <div class="event-card">
                            <div class="event-image">
                                <i class="fas fa-calendar-alt fa-3x"></i>
                            </div>
                            <div class="event-info">
                                <div class="event-date">
                                    <?= date('d/m/Y', strtotime($event['date_evenement'])) ?>
                                </div>
                                <h3><?= htmlspecialchars($event['titre']) ?></h3>
                                <p><?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...</p>
                                <a href="evenement-details.php?id=<?= $event['id'] ?>" class="btn">Voir détails</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun événement à venir pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Plateforme Éducative. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>