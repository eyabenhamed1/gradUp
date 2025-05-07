<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/controller/participationcontroller.php');

session_start();

$eventId = (int)$_GET['event_id'];
$studentId = (int)$_GET['student_id'];

$controller = new ParticipationController();
$isRegistered = $controller->checkParticipation($studentId, $eventId);

header('Content-Type: application/json');
echo json_encode(['registered' => $isRegistered]);