<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ParticipationController.php');

if (!isset($_SESSION['id'])) {
    header("Location: /ProjetWeb2A/View/Frontoffice/auth/login.php?error=session_expired");
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
            $data['id_utilisateur'] = $_SESSION['id'];
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