<?php
require_once(__DIR__ . "/../../../../controller/produitcontroller.php");

if (isset($_GET['id'])) {
    $id_produit = intval($_GET['id']); // Sécuriser l'entrée

    $controller = new ProduiController();
    $controller->deleteProduit($id_produit);

    // Rediriger vers la liste après suppression
    header("Location: produit.php?delete=success");
    exit();
} else {
    echo "ID d'événement non spécifié.";
}
?>
