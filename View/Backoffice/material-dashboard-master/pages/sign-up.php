<?php
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (!empty($name) && !empty($email) && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword, $role]);

            $message = "✅ Compte créé avec succès. <a href='sign-in.php'>Connectez-vous ici</a>.";
        } catch (Exception $e) {
            $message = "❌ Erreur lors de l'inscription : " . $e->getMessage();
        }
    } else {
        $message = "❌ Tous les champs sont requis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inscription - GradUp</title>
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <style>
        .bg-orange-blue {
            background: linear-gradient(90deg, #ff6f00, #0288d1);
        }
        .btn-orange-blue {
            background-color: #ff6f00;
            color: white;
        }
        .btn-orange-blue:hover {
            background-color: #d75d00;
        }
        .title-logo {
            font-size: 3.5rem;
            font-weight: 800;
            color: #ff6f00;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .card-header.bg-orange-blue {
            background-color: #ff6f00 !important;
        }
    </style>
</head>

<body class="bg-gray-200">
    <main class="main-content mt-0">
    <div class="page-header align-items-start min-vh-100" style="background-image: url('../assets/img/hero-bg.jpg'); background-size: cover; background-position: center;">
    <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header bg-orange-blue text-center py-4">
                                <div class="title-logo">
                                    <img src="../assets/img/logo.png" alt="Logo GradUp" height="80px">
                                    GradUp
                                </div>
                                <h5 class="text-white font-weight-bolder mt-2">Créer un compte</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($message): ?>
                                    <div class="alert alert-info text-center"><?= $message ?></div>
                                <?php endif; ?>
                                <form method="POST" action="sign-up.php" class="text-start">
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label">Nom</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label">Mot de passe</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label">Rôle</label>
                                        <input type="text" name="role" class="form-control" value="user">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-orange-blue w-100 my-4 mb-2">S'inscrire</button>
                                    </div>
                                    <p class="text-sm text-center">
                                        Vous avez déjà un compte ?
                                        <a href="sign-in.php" class="text-orange-blue font-weight-bold">Se connecter</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                        <p class="text-center mt-4 text-muted">© <?= date("Y") ?> GradUp. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
