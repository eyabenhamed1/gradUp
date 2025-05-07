<?php
require_once 'C:/xampp/htdocs/projettt/projettt/ProjetWeb2A/Config.php';

$pdo = DB::getConnexion();

// Récupération des données du formulaire
$eventId = $_POST['event_id'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
$statut = $_POST['status'];

header('Content-Type: application/json');

try {
    $pdo->beginTransaction();

    // 1. Créer l'étudiant
    $stmt = $pdo->prepare("INSERT INTO etudiant (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $mot_de_passe]);
    $studentId = $pdo->lastInsertId();

    // 2. Créer la participation
    $stmt = $pdo->prepare("INSERT INTO participation (id_evenement, id_etudiant, statut, date_inscription) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$eventId, $studentId, $statut]);

    $pdo->commit();
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}