<?php
require_once(__DIR__.'/../CartController.php');

header('Content-Type: application/json');

try {
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;

    if (!$productId || $quantity === null) {
        throw new Exception('Paramètres manquants');
    }

    $controller = new CartController();
    $response = $controller->updateQuantity($productId, $quantity);
    
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>