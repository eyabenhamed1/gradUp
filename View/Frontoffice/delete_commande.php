<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Model/Commande.php');

session_start();
$commande = new Commande();

$result = $commande->supprimerCommande($_GET['id']);

if ($result === true) {
    $_SESSION['message'] = "Commande #".$_GET['id']." supprimée avec succès";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = $result;
    $_SESSION['message_type'] = "danger";
}

header("Location: macommande.php");
exit();
?>