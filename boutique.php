<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Controllers/CommandeController.php');

$commandeController = new CommandeController();
$commandeController->processOrder();

// Afficher la vue
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Views/commander.php');
?>