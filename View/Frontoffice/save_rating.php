<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['rating'])) {
    $productId = (int)$_POST['product_id'];
    $rating = (int)$_POST['rating'];
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Note invalide']);
        exit;
    }
    
    $produitFront = new ProduitFront();
    $success = $produitFront->saveRating($productId, $rating);
    
    if ($success) {
        $average = $produitFront->getAverageRating($productId);
        echo json_encode(['success' => true, 'average' => $average]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide']);
}