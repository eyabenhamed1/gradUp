<?php
session_start();

// Connexion à la base de données
try {
    $db = new PDO(
        "mysql:host=localhost;dbname=projetweb2a;charset=utf8mb4",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des données
    $id_etudiant = trim($_POST['id_etudiant'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($id_etudiant)) {
        $errors['id_etudiant'] = "L'identifiant étudiant est requis";
    } elseif (!is_numeric($id_etudiant)) {
        $errors['id_etudiant'] = "L'identifiant doit être numérique";
    } else {
        // Vérifier si l'ID existe déjà
        $stmt = $db->prepare("SELECT id_etudiant FROM etudiant WHERE id_etudiant = ?");
        $stmt->execute([$id_etudiant]);
        if ($stmt->fetch()) {
            $errors['id_etudiant'] = "Cet identifiant est déjà utilisé";
        }
    }

    if (empty($nom)) {
        $errors['nom'] = "Le nom est requis";
    } elseif (strlen($nom) > 50) {
        $errors['nom'] = "Le nom ne doit pas dépasser 50 caractères";
    }

    if (empty($prenom)) {
        $errors['prenom'] = "Le prénom est requis";
    } elseif (strlen($prenom) > 50) {
        $errors['prenom'] = "Le prénom ne doit pas dépasser 50 caractères";
    }

    if (empty($email)) {
        $errors['email'] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email invalide";
    } elseif (strlen($email) > 100) {
        $errors['email'] = "L'email ne doit pas dépasser 100 caractères";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $db->prepare("SELECT email FROM etudiant WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = "Cet email est déjà utilisé";
        }
    }

    if (empty($password)) {
        $errors['password'] = "Le mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas";
    }

    // Si pas d'erreurs, inscription
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $db->prepare("INSERT INTO etudiant (id_etudiant, nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_etudiant, $nom, $prenom, $email, $hashed_password]);
            
            $success = true;
            $_SESSION['success_message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header("Location: connexion.php");
            exit();
        } catch (PDOException $e) {
            $errors['database'] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Plateforme Éducative</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --primary-light: #34495e;
            --primary-dark: #1a252f;
            --secondary: #7f8c8d;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --light-gray: #bdc3c7;
            --medium-gray: #95a5a6;
            --dark: #2c3e50;
            --dark-gray: #34495e;
            --white: #ffffff;
            --black: #000000;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .register-container {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h1 {
            color: var(--primary);
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .register-header p {
            color: var(--medium-gray);
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.2);
        }
        
        .btn-register {
            width: 100%;
            padding: 12px;
            background-color: var(--accent);
            color: var(--white);
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }
        
        .btn-register:hover {
            background-color: #c0392b;
        }
        
        .error-message {
            color: var(--accent);
            font-size: 14px;
            margin-top: 5px;
        }
        
        .register-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--medium-gray);
        }
        
        .register-footer a {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .register-footer a:hover {
            color: var(--accent);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: var(--medium-gray);
        }
        
        .input-icon input {
            padding-left: 45px;
        }
        
        .success-message {
            color: #2ecc71;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1><i class="fas fa-user-plus"></i> Créer un compte</h1>
            <p>Rejoignez notre plateforme éducative</p>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($errors['database'])): ?>
            <div class="error-message" style="text-align: center; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['database']) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="inscription.php">
            <div class="form-group">
                <label for="id_etudiant">Identifiant étudiant *</label>
                <input type="text" class="form-control" id="id_etudiant" name="id_etudiant" 
                       value="<?= htmlspecialchars($_POST['id_etudiant'] ?? '') ?>" required>
                <?php if (isset($errors['id_etudiant'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['id_etudiant']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" class="form-control" id="nom" name="nom" 
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required maxlength="50">
                <?php if (isset($errors['nom'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['nom']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" class="form-control" id="prenom" name="prenom" 
                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required maxlength="50">
                <?php if (isset($errors['prenom'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['prenom']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required maxlength="100">
                </div>
                <?php if (isset($errors['email'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe *</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
                <?php endif; ?>
                <small style="color: var(--medium-gray);">Minimum 8 caractères</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe *</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> S'inscrire
            </button>
        </form>
        
        <div class="register-footer">
            <p>Déjà inscrit ? <a href="connexion.php">Connectez-vous</a></p>
        </div>
    </div>
</body>
</html>