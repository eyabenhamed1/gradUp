//////deconnexion.php

<?php
// deconnexion.php

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Détruire toutes les données de session
$_SESSION = array(); // Vide le tableau de session

// Si vous voulez détruire complètement la session, supprimez également le cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Finalement, détruire la session
session_destroy();

// Rediriger vers la page de connexion
header("Location: connexion.php"); //
exit();
?>