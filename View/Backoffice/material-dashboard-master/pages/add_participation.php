<?php
require_once 'C:/xampp/htdocs/ProjetWeb2A/configg.php';

header('Content-Type: application/json');

try {
    $pdo = config::getConnexion();

    // Debug: Afficher toutes les données reçues
    error_log("Données POST reçues : " . print_r($_POST, true));

    // Récupérer et valider les données
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $mot_de_passe = filter_input(INPUT_POST, 'mot_de_passe', FILTER_SANITIZE_STRING);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $commentaire = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING) ?? '';

    // Date d'inscription actuelle
    $date_inscription = date('Y-m-d H:i:s');

    // Vérification des données requises
    $errors = [];
    if (!$name) $errors[] = "Le nom est requis";
    if (!$email) $errors[] = "L'email est invalide ou manquant";
    if (!$mot_de_passe) $errors[] = "Le mot de passe est requis";
    if (!$event_id) $errors[] = "L'ID de l'événement est invalide ou manquant";
    if (!$status) $errors[] = "Le statut est requis";

    if (!empty($errors)) {
        throw new Exception("Erreurs de validation : " . implode(", ", $errors));
    }

    // Commencer une transaction
    $pdo->beginTransaction();

    try {
        // 1. Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Un utilisateur avec cet email existe déjà");
        }

        // 2. Créer l'utilisateur
        $stmt = $pdo->prepare("INSERT INTO user (name, email, password, role, category) VALUES (?, ?, ?, 'user', 'participant')");
        if (!$stmt->execute([$name, $email, password_hash($mot_de_passe, PASSWORD_DEFAULT)])) {
            throw new Exception("Erreur lors de la création de l'utilisateur: " . implode(", ", $stmt->errorInfo()));
        }
        
        $userId = $pdo->lastInsertId();

        // 3. Créer la participation
        $stmt = $pdo->prepare("
            INSERT INTO participation (
                id_evenement, 
                id_utilisateur, 
                statut, 
                date_inscription, 
                commentaire, 
                email, 
                telephone,
                commentaire_admin
            ) VALUES (?, ?, ?, ?, ?, ?, ?, '')
        ");
        
        if (!$stmt->execute([
            $event_id,
            $userId,
            $status,
            $date_inscription,
            $commentaire,
            $email,
            $telephone
        ])) {
            throw new Exception("Erreur lors de la création de la participation: " . implode(", ", $stmt->errorInfo()));
        }

        $pdo->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Erreur dans add_participation.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}