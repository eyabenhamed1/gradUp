<?php
// Activation des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . "/../../controller/evenementcontroller.php");

// Vérification de l'ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: evenements.php");
    exit();
}

$controller = new EvenementController();
$event = $controller->getEvenementById($_GET['id']);

// Vérification si l'événement existe
if (!$event) {
    header("Location: evenements.php");
    exit();
}

// Chemin de base pour les images
$imagePath = "../uploads/";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['titre']) ?> - Détails</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .event-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        <?= !empty($event['image']) ? "url('$imagePath{$event['image']}')" : "var(--bs-dark)" ?>;
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .event-image {
            max-height: 500px;
            object-fit: cover;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .event-meta {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .default-image {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- En-tête avec image de fond -->
        <div class="event-header text-center">
            <h1 class="display-4"><?= htmlspecialchars($event['titre']) ?></h1>
            <p class="lead">
                <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($event['date_evenement'])) ?>
                | <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['lieu']) ?>
            </p>
            <span class="badge bg-primary"><?= htmlspecialchars($event['type_evenement']) ?></span>
        </div>

        <div class="row mt-4">
            <!-- Colonne principale -->
            <div class="col-lg-8 mb-4">
                <!-- Affichage de l'image principale -->
                <?php if (!empty($event['image']) && file_exists($imagePath . $event['image'])): ?>
                    <img src="<?= $imagePath . htmlspecialchars($event['image']) ?>" 
                         class="img-fluid event-image mb-4" 
                         alt="<?= htmlspecialchars($event['titre']) ?>">
                <?php else: ?>
                    <div class="default-image mb-4">
                        <div class="text-center">
                            <i class="fas fa-image fa-5x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucune image disponible</h4>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Description -->
                <h2 class="mb-3"><i class="fas fa-align-left text-primary me-2"></i>Description</h2>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <p class="card-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Sidebar avec informations -->
            <div class="col-lg-4">
                <div class="event-meta sticky-top" style="top: 20px;">
                    <h3 class="mb-4"><i class="fas fa-info-circle text-primary me-2"></i>Informations</h3>
                    
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar-day me-2"></i> Date</span>
                            <span><?= date('d/m/Y', strtotime($event['date_evenement'])) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-clock me-2"></i> Heure</span>
                            <span><?= date('H:i', strtotime($event['date_evenement'])) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-map-marker-alt me-2"></i> Lieu</span>
                            <span><?= htmlspecialchars($event['lieu']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-tag me-2"></i> Type</span>
                            <span><?= htmlspecialchars($event['type_evenement']) ?></span>
                        </li>
                    </ul>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg">
                            <i class="fas fa-ticket-alt me-2"></i>S'inscrire à l'événement
                        </button>
                        <a href="evenements.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>