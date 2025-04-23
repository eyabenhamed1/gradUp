<?php
require_once(__DIR__.'/../CartController.php');

header('Content-Type: application/json');

$controller = new CartController();
echo json_encode($controller->clearCart());
?>