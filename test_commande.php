<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Controllers/CommandeController.php');

// 1. Test connexion
$pdo = config::getConnexion();
var_dump($pdo);

// 2. Test insertion directe
try {
    $sql = "INSERT INTO commande (nom, prenom, tlf, adresse, produits, prix_total) 
            VALUES ('Test', 'Test', '12345678', 'Test', '[]', 0)";
    $count = $pdo->exec($sql);
    echo "Insertion test: ".($count > 0 ? "OK" : "Échec");
} catch (PDOException $e) {
    echo "Erreur SQL: ".$e->getMessage();
}

// 3. Test du contrôleur
$_POST = [
    'commander' => 1,
    'nom' => 'Jean',
    'prenom' => 'Dupont',
    'tel' => '51234567',
    'adresse' => '123 Rue Test'
];
$_SESSION['cart'] = [
    1 => ['quantity' => 2] // ID produit 1 doit exister
];

$controller = new CommandeController();
$controller->processOrder();