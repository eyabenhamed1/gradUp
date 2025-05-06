<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false];
    
    if (isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        
        // Pour la suppression
        if (isset($_POST['remove']) && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $response['success'] = true;
        } 
        // Pour la modification de quantité
        elseif (isset($_POST['quantity_change']) && isset($_SESSION['cart'][$productId])) {
            $change = (int)$_POST['quantity_change'];
            $_SESSION['cart'][$productId]['quantity'] += $change;
            
            // Supprimer si quantité <= 0
            if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                unset($_SESSION['cart'][$productId]);
            }
            
            $response['success'] = true;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}