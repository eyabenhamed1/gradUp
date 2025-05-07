<?php
require_once 'C:/xampp/htdocs/projettt/projettt/ProjetWeb2A/Config.php';

if (isset($_GET['id'])) {
    $pdo = DB::getConnexion();
    
    // Récupérer l'ID de l'événement avant suppression
    $eventId = $pdo->query("
        SELECT id_evenement FROM participation 
        WHERE id_participation = " . (int)$_GET['id']
    )->fetchColumn();
    
    $pdo->exec("
        DELETE FROM participation 
        WHERE id_participation = " . (int)$_GET['id']
    );
    
    header("Location: participants_details.php?event_id=$eventId");
    exit();
}