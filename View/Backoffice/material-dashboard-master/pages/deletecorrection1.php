<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Config.php';

// Check if id_cor parameter exists
if(isset($_GET['id_cor'])) {
    $id_cor = $_GET['id_cor'];
    
    try {
        $conn = config::getConnexion();
        $sql = "DELETE FROM correction1 WHERE id_cor = :id_cor";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_cor', $id_cor, PDO::PARAM_INT);
        $stmt->execute();
        
        // Redirect back to the corrections list with success message
        header("Location: correction1.php?delete_success=1");
        exit();
    } catch(PDOException $e) {
        // Redirect back with error message
        header("Location: correction1.php?delete_error=1");
        exit();
    }
} else {
    // No ID provided, redirect back
    header("Location: correction1.php");
    exit();
}
?>