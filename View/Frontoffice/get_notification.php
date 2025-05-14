<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/Controller/ParticipationController.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit();
}

$controller = new ParticipationController();
$events = $controller->getUpcomingEventsReminders($_SESSION['id']);

$notifications = [];
foreach ($events as $event) {
    try {
        $eventDate = new DateTime($event['date_evenement']);
        $now = new DateTime();
        $interval = $now->diff($eventDate);
        
        $days = $interval->days;
        $hours = $interval->h;
        
        $message = "L'événement commence ";
        if ($days > 0) {
            $message .= "dans $days jour" . ($days > 1 ? 's' : '');
            if ($hours > 0) {
                $message .= " et $hours heure" . ($hours > 1 ? 's' : '');
            }
        } else {
            $message .= "dans $hours heure" . ($hours > 1 ? 's' : '');
        }
        
        $notifications[] = [
            'id' => $event['id_participation'],
            'title' => 'Rappel: ' . htmlspecialchars($event['titre']),
            'message' => $message . ' - Lieu: ' . htmlspecialchars($event['lieu']),
            'time' => $eventDate->format('d/m/Y H:i'),
            'read' => false
        ];
    } catch (Exception $e) {
        error_log("Erreur formatage événement: " . $e->getMessage());
    }
}

echo json_encode([
    'success' => true,
    'notifications' => $notifications
]);
?>