<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/Controller/ParticipationController.php');

if (!isset($_SESSION['id_etudiant'])) {
    header("Location: connexion.php");
    exit();
}

$participationController = new ParticipationController();
$upcomingEvents = $participationController->getUpcomingEventsReminders($_SESSION['id_etudiant']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappels d'événements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .reminder-card {
            border-left: 4px solid #4e73df;
            margin-bottom: 1rem;
        }
        .event-date {
            font-weight: bold;
            color: #4e73df;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4">Mes rappels d'événements</h2>
        
        <?php if (empty($upcomingEvents)): ?>
            <div class="alert alert-info">
                Vous n'avez aucun événement à venir dans les 7 prochains jours.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($upcomingEvents as $event): 
                    $eventDate = new DateTime($event['date_evenement']);
                    $now = new DateTime();
                    $interval = $now->diff($eventDate);
                ?>
                <div class="col-md-6">
                    <div class="card reminder-card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['titre']) ?></h5>
                            <p class="card-text">
                                <span class="event-date">
                                    <?= $eventDate->format('d/m/Y à H:i') ?>
                                </span>
                                - <?= htmlspecialchars($event['lieu']) ?>
                            </p>
                            <p class="text-muted">
                                Dans <?= $interval->format('%a jours et %h heures') ?>
                            </p>
                            <a href="evenement-details.php?id=<?= $event['id_evenement'] ?>" class="btn btn-primary">
                                Voir détails
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>