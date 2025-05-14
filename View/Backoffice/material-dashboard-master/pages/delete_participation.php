<?php
require_once 'C:/xampp/htdocs/ProjetWeb2A/configg.php';
try {
    $pdo = config::getConnexion();

    // Récupérer l'ID de la participation
    $id_participation = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id_participation) {
        throw new Exception("ID de participation invalide");
    }

    // Récupérer l'ID de l'événement avant de supprimer la participation
    $stmt = $pdo->prepare("SELECT id_evenement FROM participation WHERE id_participation = ?");
    $stmt->execute([$id_participation]);
    $event_id = $stmt->fetchColumn();

    if (!$event_id) {
        throw new Exception("Participation non trouvée");
    }

    // Supprimer la participation
    $stmt = $pdo->prepare("DELETE FROM participation WHERE id_participation = ?");
    $success = $stmt->execute([$id_participation]);

    if (!$success) {
        throw new Exception("Erreur lors de la suppression");
    }

    // Rediriger vers la page des participants avec l'ID de l'événement
    header("Location: participants_details.php?event_id=" . $event_id . "&delete=success");
    exit();

} catch (Exception $e) {
    // Rediriger avec un message d'erreur
    if (isset($event_id)) {
        header("Location: participants_details.php?event_id=" . $event_id . "&delete=error&message=" . urlencode($e->getMessage()));
    } else {
        header("Location: evenement.php?delete=error&message=" . urlencode($e->getMessage()));
    }
    exit();
} 