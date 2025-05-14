<?php
require_once __DIR__ . '/../../../config.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// Vérification du token et récupération de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$token || !$newPassword || !$confirmPassword) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE reset_token = :token");
            $stmt->execute(['token' => $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $resetTime = strtotime($user['reset_at']);
                $now = time();

                if ($now - $resetTime > 900) { // 15 minutes = 900 secondes
                    $error = "Le lien a expiré. Veuillez refaire une demande.";
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE user SET password = :password, reset_token = NULL, reset_at = NULL WHERE id = :id");
                    $stmt->execute([
                        'password' => $hashedPassword,
                        'id'       => $user['id']
                    ]);
                    $success = "Mot de passe réinitialisé avec succès. <a href='login.php'>Connectez-vous</a>";
                }
            } else {
                $error = "Lien invalide ou expiré.";
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
  <title>Réinitialisation du mot de passe</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #6a11cb, #2575fc);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .box {
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 450px;
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }

    input {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 15px;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #6a11cb;
      color: #fff;
      font-size: 16px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #5a0eb7;
    }

    .message {
      color: #333;
      background: #f0f0f0;
      padding: 10px;
      border-radius: 10px;
      margin-bottom: 15px;
      text-align: center;
    }

    .error {
      color: #d8000c;
      background: #ffbaba;
    }

    .success {
      color: #4F8A10;
      background: #DFF2BF;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Réinitialisation du mot de passe</h2>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
      <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
        <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
        <button type="submit">Réinitialiser</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
