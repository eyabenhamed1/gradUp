<?php
session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');
$produitFront = new ProduitFront();

$response = [
    'items' => [],
    'cartCount' => 0,
    'total' => 0
];

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $item) {
        $product = $produitFront->getProduit($productId);
        if ($product) {
            $itemTotal = $product['prix'] * $item['quantity'];
            $response['total'] += $itemTotal;
            
            $response['items'][] = [
                'id' => $productId,
                'name' => $product['name'],
                'price' => $product['prix'],
                'quantity' => $item['quantity'],
                'total' => $itemTotal,
                'image' => $product['image_path'] ?? 'https://via.placeholder.com/50?text=Image+Indisponible'
            ];
        }
    }
    
    $response['cartCount'] = array_reduce($_SESSION['cart'], function($carry, $item) {
        return $carry + $item['quantity'];
    }, 0);
}

header('Content-Type: application/json');
echo json_encode($response);