<?php
session_start();
require_once(__DIR__ . '/../../configg.php');

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

    // Set default photo path
    $photoPath = 'uploads/default.png';

    // Handle photo upload if provided
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if ($_FILES['photo']['size'] > $maxFileSize) {
            $error = "La taille de l'image ne doit pas dépasser 5MB.";
        } else {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
        if (!in_array($ext, $allowed)) {
            $error = "Seules les images JPG, JPEG, PNG et GIF sont autorisées.";
        } else {
                // Create uploads directory if it doesn't exist
                $uploadsDir = __DIR__ . '/../../uploads';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }

                // Generate unique filename
                $filename = 'pdp_' . time() . '_' . uniqid() . '.' . $ext;
                $targetPath = $uploadsDir . '/' . $filename;
                
                // Attempt to move uploaded file
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $photoPath = 'uploads/' . $filename;
            } else {
                    $error = "Erreur lors de l'upload de la photo. Vérifiez les permissions du dossier.";
                }
            }
        }
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

    .file-input-container {
      margin: 10px 0;
    }

    .file-input-container label {
      display: block;
      margin-bottom: 5px;
      color: #666;
      font-size: 14px;
    }

    input[type="file"] {
      border: 2px dashed #ccc;
      background: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
      cursor: pointer;
      transition: border-color 0.3s ease;
    }

    input[type="file"]:hover {
      border-color: #6a11cb;
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

    .photo-preview {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      margin: 10px auto;
      display: none;
      object-fit: cover;
      border: 3px solid #6a11cb;
    }

    /* Add logo styles */
    .logo-container {
      text-align: center;
      margin-bottom: 20px;
    }

    .logo-container img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 10px;
      border: 3px solid #6a11cb;
    }

    .logo-container h1 {
      margin: 10px 0;
      font-size: 24px;
      color: #333;
      font-weight: 700;
    }
  </style>
</head>
<body>
  <div class="box">
    <div class="logo-container">
      <img src="../../assets/logo.jpg" alt="Logo GradUp">
      <h1>GradUp</h1>
    </div>
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
      <input type="text" name="category" placeholder="Classe" required>
      <div class="file-input-container">
        <label for="photo">Photo de profil (optionnel, max 5MB)</label>
        <input type="file" id="photo" name="photo" accept="image/*" onchange="previewImage(this)">
        <img id="preview" class="photo-preview">
      </div>
      <button type="submit">S'inscrire</button>
    </form>
    <div class="login-link">
      Déjà un compte ? <a href="login.php">Se connecter</a>
    </div>
  </div>

  <script>
    function previewImage(input) {
      const preview = document.getElementById('preview');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
      } else {
        preview.style.display = 'none';
      }
    }
  </script>
</body>
</html>
