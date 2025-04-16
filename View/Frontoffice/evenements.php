<?php
require_once(__DIR__ . "/../../controller/evenementcontroller.php");
$controller = new evenementController();
$evenements = $controller->listeEvenement();

$imageBasePath = "uploads/";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Notre École</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .event-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            background: white;
        }
        
        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .event-img-container {
            height: 220px;
            overflow: hidden;
            position: relative;
        }
        
        .event-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .event-card:hover .event-img {
            transform: scale(1.1);
        }
        
        .event-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .no-image {
            background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        
        .event-body {
            padding: 1.5rem;
        }
        
        .event-title {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .event-meta {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .event-description {
            color: #4a5568;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .btn-details {
            background-color: var(--primary-color);
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-details:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
        }
        
        .section-title {
            position: relative;
            margin-bottom: 2.5rem;
            font-weight: 700;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 50px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <!-- En-tête amélioré -->
    <header class="page-header">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Nos Événements</h1>
            <p class="lead">Découvrez les prochains événements de notre établissement</p>
        </div>
    </header>

    <div class="container py-5">
        <?php if (empty($evenements)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-calendar-times fa-2x mb-3"></i>
                <h4>Aucun événement prévu pour le moment</h4>
                <p class="mb-0">Revenez plus tard pour découvrir nos prochains événements</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($evenements as $event): ?>
                <div class="col">
                    <div class="event-card h-100">
                        <!-- Image de l'événement -->
                        <div class="event-img-container">
                            <?php if (!empty($event['image']) && file_exists($imageBasePath . $event['image'])): ?>
                                <img src="<?= $imageBasePath . htmlspecialchars($event['image']) ?>" 
                                     class="event-img" 
                                     alt="<?= htmlspecialchars($event['titre']) ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <div class="text-center p-4">
                                        <i class="fas fa-calendar-day fa-4x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucune image disponible</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <span class="event-badge">
                                <?= date('d/m/Y', strtotime($event['date_evenement'])) ?>
                            </span>
                        </div>
                        
                        <!-- Corps de la carte -->
                        <div class="event-body">
                            <h3 class="event-title"><?= htmlspecialchars($event['titre']) ?></h3>
                            
                            <div class="event-meta">
                                <span class="d-block mb-2">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <?= htmlspecialchars($event['lieu']) ?>
                                </span>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($event['type_evenement']) ?>
                                </span>
                            </div>
                            
                            <p class="event-description">
                                <?= htmlspecialchars($event['description']) ?>
                            </p>
                            
                            <div class="d-grid">
                                <a href="evenement-details.php?id=<?= $event['id'] ?>" 
                                   class="btn btn-details text-white">
                                   <i class="fas fa-info-circle me-2"></i>Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>