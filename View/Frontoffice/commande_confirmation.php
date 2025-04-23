<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controller/ProduitFront.php');

$produitFront = new ProduitFront();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de commande</title>
    <!-- Inclure vos CSS ici -->
</head>
<body>
    <div class="container py-5">
        <?php
        if (isset($_SESSION['flash_success'])) {
            echo '<div class="alert alert-success">'.$_SESSION['flash_success'].'</div>';
            unset($_SESSION['flash_success']);
        } else {
            echo '<div class="alert alert-info">Merci pour votre commande!</div>';
        }
        ?>
        <a href="boutique.php" class="btn btn-primary">Retour à la boutique</a>
    </div>
</body>
</html>