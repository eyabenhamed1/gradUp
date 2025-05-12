<?php
session_start();
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php'; // Connexion via classe config

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        $pdo = config::getConnexion();

        // Requête préparée avec PDO pour éviter les injections SQL
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Enregistrement des données essentielles de l'utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name']; // ou 'email' si vous préférez

            // Redirection vers la page profil
            header("Location: profile.php");
            exit();
        } else {
            // Redirection avec message d'erreur
            header("Location: sign-in.php?error=1");
            exit();
        }

    } catch (Exception $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}
?>
