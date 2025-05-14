<?php
session_start();

require_once(__DIR__ . "/../../Controller/UserController.php");

$error = '';
$success = '';

// Check for logout message
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success = "Vous avez été déconnecté avec succès.";
}

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $userController = new UserController();
        $result = $userController->login($email, $password);
        
        if (is_array($result)) {
            // Successful login
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['email'] = $result['email'];
            $_SESSION['role'] = $result['role'];
            
            // Redirect based on role
            if ($result['role'] === 'admin') {
                header("Location: ../Backoffice/material-dashboard-master/pages/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            // Login failed
            $error = "Email ou mot de passe incorrect.";
        }
    }
}

// Traitement du formulaire "Mot de passe oublié"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $forgot_email = trim($_POST['forgot_email'] ?? '');

    if (!$forgot_email) {
        $error = "Veuillez entrer votre adresse email.";
    } else {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute(['email' => $forgot_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $resetLink = "http://localhost/ProjetWeb2A/View/Frontoffice/auth/reset-password.php?token=$token";

                $stmt = $pdo->prepare("UPDATE user SET reset_token = :token, reset_at = NOW() WHERE email = :email");
                $stmt->execute(['token' => $token, 'email' => $forgot_email]);

                mail($forgot_email, "Réinitialisation du mot de passe", "Cliquez ici pour réinitialiser votre mot de passe : $resetLink");

                $error = "Un lien de réinitialisation a été envoyé à votre email.";
            } else {
                $error = "Aucun utilisateur trouvé avec cet email.";
            }
        } catch (Exception $e) {
            $error = "Erreur serveur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Gradup</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --primary-light: #34495e;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --error: #e74c3c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: var(--primary);
            font-size: 2em;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: var(--primary);
            outline: none;
        }

        .form-group .password-toggle {
            position: relative;
        }

        .form-group .password-toggle i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: var(--primary);
            color: white;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: var(--primary-light);
        }

        .error-message {
            background-color: var(--error);
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9em;
            text-align: center;
        }

        .success-message {
            background-color: var(--success);
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9em;
            text-align: center;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9em;
            transition: color 0.3s ease;
        }

        .links a:hover {
            color: var(--accent);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Connexion</h1>
            <p>Connectez-vous à votre compte Gradup</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Votre adresse email">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="password-toggle">
                    <input type="password" id="password" name="password" required placeholder="Votre mot de passe">
                    <i class="fas fa-eye" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" class="btn">Se connecter</button>

            <div class="links">
                <a href="forgot-password.php">Mot de passe oublié?</a>
                <br>
                <a href="register.php">Créer un compte</a>
            </div>
        </form>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // Toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle the icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
