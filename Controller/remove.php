<?php
require_once(__DIR__.'/../CartController.php');

header('Content-Type: application/json');

try {
    // Vérifier si l'ID produit est bien reçu
    if (!isset($_POST['product_id'])) {
        throw new Exception('ID produit manquant');
    }

    $productId = (int)$_POST['product_id'];
    
    // Valider que l'ID est un nombre valide
    if ($productId <= 0) {
        throw new Exception('ID produit invalide');
    }

    $controller = new CartController();
    $result = $controller->removeFromCart($productId);

    if (!$result['success']) {
        throw new Exception('Échec de la suppression');
    }

    // Retourner le nouveau total et count mis à jour
    echo json_encode([
        'success' => true,
        'message' => 'Produit supprimé du panier',
        'count' => $result['count'],
        'total' => $result['total']
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'count' => 0,
        'total' => 0
    ]);
}
?>