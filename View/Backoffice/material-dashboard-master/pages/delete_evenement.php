<?php
require_once(__DIR__ . "/../../../../controller/EvenementController.php");

if (isset($_GET['id'])) {
    $id_evenement = intval($_GET['id']); // Sécuriser l'entrée

    $controller = new EvenementController();
    $controller->deleteEvenement($id_evenement);

    // Rediriger vers la liste après suppression
    header("Location: evenement.php?delete=success");
    exit();
} else {
    echo "ID d'événement non spécifié.";
}
?>
