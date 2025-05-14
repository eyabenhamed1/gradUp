<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)$_POST['product_id'];
    
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false]);
?>