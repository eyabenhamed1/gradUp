<?php
// Dans votre fichier includes/upcoming-events.php

require_once(__DIR__ . "/../controller/EvenementFrontController.php");
$controller = new EvenementFrontController();
$upcomingEvents = $controller->getUpcomingEvenements(3);
?>

<div class="card mb-4">
    <div class="card-header">
        <h5>Prochains Événements</h5>
    </div>
    <div class="card-body">
        <?php if (empty($upcomingEvents)): ?>
            <p>Aucun événement à venir</p>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($upcomingEvents as $event): ?>
                    <a href="evenement-details.php?id=<?= $event['id'] ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= htmlspecialchars($event['titre']) ?></h6>
                            <small><?= date('d/m', strtotime($event['date_evenement'])) ?></small>
                        </div>
                        <small><?= htmlspecialchars($event['lieu']) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="mt-3 text-center">
                <a href="evenements.php" class="btn btn-sm btn-outline-primary">Voir tous les événements</a>
            </div>
        <?php endif; ?>
    </div>
</div>