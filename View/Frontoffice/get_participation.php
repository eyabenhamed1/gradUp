<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/Controller/ParticipationController.php');

if (!isset($_SESSION['id_etudiant'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Non autorisé']));
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'ID manquant']));
}

$participationController = new ParticipationController();
$participation = $participationController->getParticipationById($_GET['id'], $_SESSION['id_etudiant']);

if (!$participation) {
    http_response_code(404);
    die(json_encode(['error' => 'Inscription non trouvée']));
}

header('Content-Type: application/json');
echo json_encode($participation);
?>