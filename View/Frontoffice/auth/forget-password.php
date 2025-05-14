<?php
require_once __DIR__ . '/../../../config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $error = "Veuillez saisir votre adresse e-mail.";
    } else {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(16));
                $resetLink = "http://localhost/try/ProjetWeb2A/View/Frontoffice/auth/reset-password.php?token=$token";

                // Stocker le token et l'heure actuelle
                $stmt = $pdo->prepare("UPDATE user SET reset_token = :token, reset_at = NOW() WHERE id = :id");
                $stmt->execute([
                    'token' => $token,
                    'id'    => $user['id']
                ]);

                // Envoi du mail (version simple)
                $to = $user['email'];
                $subject = "Réinitialisation de votre mot de passe - Gradup";
                $message = "Bonjour,\n\nPour réinitialiser votre mot de passe, veuillez cliquer sur ce lien :\n\n$resetLink\n\nCe lien expire dans 15 minutes.\n\nCordialement,\nL'équipe Gradup";
                $headers = "From: no-reply@gradup.com";

                if (mail($to, $subject, $message, $headers)) {
                    $success = "Un lien de réinitialisation vous a été envoyé par e-mail.";
                } else {
                    $error = "Erreur lors de l'envoi du mail.";
                }
            } else {
                $error = "Aucun compte associé à cet e-mail.";
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
  <title>Mot de passe oublié - Gradup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .container {
      max-width: 500px;
      background: #fff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
    }
    .logo {
      width: 120px;
      display: block;
      margin: 0 auto 20px;
    }
    .message {
      padding: 10px;
      border-radius: 10px;
      text-align: center;
    }
    .error {
      background-color: #ffbaba;
      color: #d8000c;
    }
    .success {
      background-color: #d4edda;
      color: #155724;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="../../../img/assets/logo.png" alt="Gradup" class="logo">
    <h3 class="text-center mb-4">Mot de passe oublié</h3>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Adresse e-mail</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
    </form>

    <div class="text-center mt-3">
      <a href="login.php">← Retour à la connexion</a>
    </div>
  </div>
</body>

</html>
