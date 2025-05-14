<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/Model/ParticipationModel.php');

if (!isset($_SESSION['id_utilisateur']) || !isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

$model = new ParticipationModel();
$model->markNotificationAsRead($_GET['id'], $_SESSION['id_utilisateur']);

echo json_encode(['success' => true]);
?>