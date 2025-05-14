<?php
session_start();
require_once __DIR__ . '/../../../configg.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $role     = trim($_POST['role']     ?? '');
    $category = trim($_POST['category'] ?? '');

    if (!$name || !$email || !$password || !$role) {
        $error = "Tous les champs sont obligatoires.";
    }

    $uploadsDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext     = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($ext, $allowed)) {
            $error = "Seules les images JPG, JPEG, PNG et GIF sont autorisées.";
        } else {
            $filename = uniqid('usr_') . '.' . $ext;
            $target   = $uploadsDir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $photoPath = 'uploads/' . $filename;
            } else {
                $error = "Erreur lors de l'upload de la photo.";
            }
        }
    } else {
        $photoPath = 'uploads/default-avatar.png';
    }

    if (!$error) {
        try {
            $pdo = config::getConnexion();

            $stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $error = "Cet email est déjà utilisé.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO user (name, email, password, role, category, photo)
                    VALUES (:name, :email, :password, :role, :category, :photo)
                ");
                $stmt->execute([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => $hash,
                    'role'     => $role,
                    'category' => $category,
                    'photo'    => $photoPath
                ]);

                $_SESSION['user'] = [
                    'id'       => $pdo->lastInsertId(),
                    'name'     => $name,
                    'email'    => $email,
                    'role'     => $role,
                    'category' => $category,
                    'photo'    => $photoPath
                ];
                header("Location: profile.php");
                exit();
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
  <title>Inscription</title>
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

    input, select {
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

    .error {
      color: #d8000c;
      background: #ffbaba;
      padding: 10px;
      border-radius: 10px;
      margin-bottom: 15px;
      text-align: center;
    }

    input[type="file"] {
      border: none;
      background: #f1f1f1;
      padding: 10px;
    }

    .login-link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .login-link a {
      color: #2575fc;
      text-decoration: none;
      font-weight: bold;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Créer un compte</h2>
    <?php if($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="text" name="name" placeholder="Nom complet" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Mot de passe" required>
      <select name="role" required>
        <option value="">Choisir un rôle</option>
        <option value="étudiant">Étudiant</option>
        <option value="professeur">Professeur</option>
      </select>
      <input type="text" name="category" placeholder="Catégorie" required>
      <input type="file" name="photo" accept="image/*">
      <button type="submit">S'inscrire</button>
    </form>
    <div class="login-link">
      Déjà un compte ? <a href="login.php">Se connecter</a>
    </div>
  </div>
</body>
</html>
