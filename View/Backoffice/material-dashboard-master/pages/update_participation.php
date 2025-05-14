<?php
require_once 'C:/xampp/htdocs/ProjetWeb2A/configg.php';

header('Content-Type: application/json');

try {
    $pdo = config::getConnexion();

    // Récupérer les données
    $id_participation = filter_input(INPUT_POST, 'id_participation', FILTER_VALIDATE_INT);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $commentaire_admin = filter_input(INPUT_POST, 'commentaire_admin', FILTER_SANITIZE_STRING);

    if (!$id_participation) {
        throw new Exception("ID de participation invalide");
    }

    // Mettre à jour la participation
    $stmt = $pdo->prepare("
        UPDATE participation 
        SET telephone = ?,
            commentaire_admin = ?
        WHERE id_participation = ?
    ");
    
    $success = $stmt->execute([
        $telephone,
        $commentaire_admin,
        $id_participation
    ]);

    if (!$success) {
        throw new Exception("Erreur lors de la mise à jour");
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}