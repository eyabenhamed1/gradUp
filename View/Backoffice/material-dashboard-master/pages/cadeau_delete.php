<?php
require_once(__DIR__ . "/../../../../controller/cadeaucontroller.php");

$controller = new CadeauController();

// Récupérer l'ID depuis l'URL
$id_cadeau = $_GET['id'] ?? null;

if ($id_cadeau) {
    // Appel correct du contrôleur
    $result = $controller->deleteCadeau($id_cadeau);
    
    if ($result) {
        header("Location: cadeau.php?success=1");
    } else {
        header("Location: cadeau.php?error=1");
    }
    exit();
} else {
    header("Location: cadeau.php?error=2");
    exit();
}
?>