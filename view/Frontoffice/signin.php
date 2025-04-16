<?php
// signin.php
session_start();
require_once __DIR__ . '/../../controller/UserController.php';

// Check if user is already logged in
if (isset($_SESSION['username'])) {
    header("Location: front.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $category = trim($_POST['category']);
    $role = trim($_POST['role']);
    $adresse = trim($_POST['adresse']);

    if (!empty($username) && !empty($password) && !empty($category) && !empty($role) && !empty($adresse)) {
        $userController = new UserController();
        $user = $userController->login($username, $password); // login() utilisé ici

        if ($user) {
            // Vérification des champs supplémentaires (facultatif selon ton besoin)
            if ($user['category'] === $category && $user['role'] === $role && $user['adresse'] === $adresse) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_info'] = $user;

                header("Location: front.php");
                exit();
            } else {
                $error = "Category, role, or address does not match.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sign In</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="signin.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <input type="text" id="role" name="role" required>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse:</label>
                <input type="text" id="adresse" name="adresse" required>
            </div>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
