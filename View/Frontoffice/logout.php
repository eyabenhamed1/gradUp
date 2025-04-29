<?php
session_start();

// Détruire la session
session_destroy();

// Rediriger vers la page de login
header("Location: index.php");
exit();
?>