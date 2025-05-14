<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once(__DIR__ . "/../../controller/ParticipationController.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_participation'])) {
    $participationController = new ParticipationController();
    $id_participation = (int)$_POST['id_participation'];
    
    try {
        // Vérifier que la participation appartient bien à l'utilisateur
        $participation = $participationController->getParticipationById($id_participation);
        
        if ($participation && $participation['id_utilisateur'] == $_SESSION['user_id']) {
            // Annuler la participation
            $success = $participationController->annulerParticipation($id_participation);
            
            if ($success) {
                $_SESSION['annulation_success'] = true;
                header("Location: mes-inscriptions.php");
                exit();
            }
        }
        
        // Si problème, rediriger avec message d'erreur
        $_SESSION['annulation_error'] = "Impossible d'annuler cette inscription";
        header("Location: mes-inscriptions.php");
        exit();
        
    } catch (Exception $e) {
        error_log("Erreur lors de l'annulation: " . $e->getMessage());
        $_SESSION['annulation_error'] = "Une erreur est survenue lors de l'annulation";
        header("Location: mes-inscriptions.php");
        exit();
    }
} else {
    header("Location: mes-inscriptions.php");
    exit();
}