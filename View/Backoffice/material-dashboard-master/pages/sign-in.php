<?php
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php';
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error_message = "Email ou mot de passe incorrect.";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connexion - GradUp</title>

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">

    <!-- Material Dashboard CSS -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

    <style>
        .bg-orange-blue {
            background: linear-gradient(90deg, #f57c00, #1976d2);
        }

        .btn-orange-blue {
            background-color: #f57c00;
            color: white;
        }

        .btn-orange-blue:hover {
            background-color:rgb(253, 101, 20);
        }

        .title-logo {
            font-size: 3.5rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            color:rgb(255, 112, 2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: -80px; /* Ajuste cet espace entre le logo et le texte */
        }

        .title-logo img {
          height:120px;
          width: auto;
          margin-right: 10px; 
          margin-left: 10px; 
}

        

        .text-orange-blue {
            color: #f57c00;
        }

        .login-card {
            max-width: 600px; /* plus large */
            width: 100%;
        }
    </style>
</head>

<body class="bg-gray-200">
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100" style="background-image: url('../assets/img/hero-bg.jpg'); background-size: cover; background-position: center;">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-6 col-md-8 col-12 mx-auto">
                        <div class="card login-card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header bg-orange-blue text-center py-4">
                                <div class="title-logo">
                                <img src="../assets/img/logo.png" alt="Logo GradUp">
                                GradUp
                                </div>
                                <h5 class="text-white font-weight-bolder mt-2">Connexion</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($error_message)): ?>
                                    <div class="alert alert-danger text-center" role="alert">
                                        <?= $error_message; ?>
                                    </div>
                                <?php endif; ?>

                                <form action="login.php" method="POST" class="text-start">
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Mot de passe</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-orange-blue w-100 my-4 mb-2">Se connecter</button>
                                    </div>
                                    <p class="mt-4 text-sm text-center">
                                        Vous n'avez pas de compte ?
                                        <a href="sign-up.php" class="text-orange-blue font-weight-bold">Créer un compte</a>
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

    <!-- JS scripts -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>

</html>
