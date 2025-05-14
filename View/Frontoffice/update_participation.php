<?php
session_start();
header('Content-Type: application/json');

require_once(__DIR__ . '/../../Controller/ParticipationController.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit();
}

// Vérifier si la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données
$id_participation = filter_input(INPUT_POST, 'id_participation', FILTER_VALIDATE_INT);
$nom = trim(htmlspecialchars($_POST['nom'] ?? ''));
$email = trim($_POST['email'] ?? '');
$telephone = trim(htmlspecialchars($_POST['telephone'] ?? ''));
$commentaire = trim(htmlspecialchars($_POST['commentaire'] ?? ''));

// Validation des données
if (!$id_participation) {
    echo json_encode(['success' => false, 'message' => 'ID de participation invalide']);
    exit();
}

if (empty($nom)) {
    echo json_encode(['success' => false, 'message' => 'Le nom est requis']);
    exit();
}

try {
    $participationController = new ParticipationController();
    
    // Vérifier que la participation appartient bien à l'utilisateur connecté
    $participation = $participationController->getParticipationById($id_participation, $_SESSION['user']['id']);
    if (!$participation) {
        throw new Exception('Participation non trouvée ou non autorisée');
    }
    
    // Mettre à jour les données
    $data = [
        'nom_participant' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'commentaire' => $commentaire
    ];
    
    $result = $participationController->updateParticipation($id_participation, $data);
    
    if ($result) {
        $_SESSION['success_message'] = "Vos informations ont été mises à jour avec succès";
        echo json_encode(['success' => true, 'message' => 'Mise à jour réussie']);
    } else {
        throw new Exception('Erreur lors de la mise à jour');
    }
    
} catch (Exception $e) {
    error_log("Erreur lors de la mise à jour de la participation : " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 