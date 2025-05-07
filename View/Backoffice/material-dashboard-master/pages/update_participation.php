<?php
require_once 'C:/xampp/htdocs/projettt/projettt/ProjetWeb2A/Config.php';

$pdo = DB::getConnexion();

$stmt = $pdo->prepare("
    UPDATE participation 
    SET statut = ?
    WHERE id_participation = ?
");

$stmt->execute([
    $_POST['status'],
    $_POST['id_participation']
]);

// Retourner une réponse JSON pour AJAX
header('Content-Type: application/json');
echo json_encode(['success' => true]);