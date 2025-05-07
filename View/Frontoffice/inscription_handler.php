<?php
require_once(__DIR__ . "/../../controller/participationcontroller.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$data = [
    'eventId' => filter_input(INPUT_POST, 'eventId', FILTER_VALIDATE_INT),
    'id_etudiant' => filter_input(INPUT_POST, 'id_etudiant', FILTER_VALIDATE_INT),
    'statut' => filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING),
    'date_inscription' => date('Y-m-d')
];

if (!$data['eventId'] || !$data['id_etudiant'] || !$data['statut']) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis']);
    exit;
}

try {
    $controller = new ParticipationController();
    $result = $controller->inscrireParticipant(
        $data['eventId'],
        $data['id_etudiant'],
        $data['statut'],
        $data['date_inscription']
    );
    
    echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}