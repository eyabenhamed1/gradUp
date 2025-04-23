<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/ProjetWeb2A/Model/Commande.php');

session_start();
$commande = new Commande();

$data = [
    'id_commande' => $_POST['id_commande'],
    'nom' => $_POST['nom'],
    'prenom' => $_POST['prenom'],
    'tlf' => $_POST['tlf'],
    'adresse' => $_POST['adresse'],
    'etat' => 'en cours' // On garde le même état
];

$result = $commande->modifierCommande($_POST['id_commande'], $data);

if ($result === true) {
    $_SESSION['message'] = "Commande #".$_POST['id_commande']." mise à jour avec succès";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = $result;
    $_SESSION['message_type'] = "danger";
}

header("Location: macommande.php");
exit();
?>