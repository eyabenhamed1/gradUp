<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Commande.php');

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$id = (int)$_GET['id'];
$commande = new Commande();
$commandeData = $commande->getCommandeById($id);

echo json_encode($commandeData);
?>