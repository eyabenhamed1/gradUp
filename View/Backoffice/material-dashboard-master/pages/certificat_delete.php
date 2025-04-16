<?php
require_once(__DIR__ . "/../../../../controller/certificatcontroller.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sécuriser l'entrée

    $controller = new CertificatController();
    $controller->deleteCertificat($id);

    // Rediriger vers la liste après suppression
    header("Location: certificat.php?delete=success");
    exit();
} else {
    echo "ID d'événement non spécifié.";
}
?>
