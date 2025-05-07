<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/Controller/ParticipationController.php');

if (!isset($_SESSION['id_etudiant'])) {
    header("Location: connexion.php?error=session_expired");
    exit();
}

$participationController = new ParticipationController();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'statut' => $_POST['statut'],
            'id_evenement' => $_POST['id_evenement']
        ];

        if ($_POST['action'] === 'create') {
            $data['id_etudiant'] = $_SESSION['id_etudiant'];
            $result = $participationController->createParticipation($data);
            $_SESSION['message'] = "Inscription créée avec succès";
        } elseif ($_POST['action'] === 'update') {
            $result = $participationController->updateParticipation($_POST['id_participation'], $data);
            $_SESSION['message'] = "Inscription mise à jour avec succès";
        }

        $_SESSION['message_type'] = "success";
    }
} catch (Exception $e) {
    $_SESSION['message'] = $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: mes-inscriptions.php");
exit();
?>