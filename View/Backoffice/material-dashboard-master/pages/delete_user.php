<?php
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php'; // Connexion via classe config

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $conn = config::getConnexion();
        // Suppression logique : on enregistre la date/heure de suppression
        $stmt = $conn->prepare("UPDATE user SET deleted_at = NOW() WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: tables.php?message=Utilisateur+supprimé+avec+succès");
        exit();
    } catch (Exception $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
} else {
    echo "ID utilisateur invalide.";
}
?>
