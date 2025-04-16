<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the UserController and User model
require_once '../../controller/UserController.php';
require_once '../../model/User.php';

$userController = new UserController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $user->setUsername($_POST['username']);
    $user->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT)); // Hash the password
    $user->setCategory($_POST['category']);
    $user->setAdresse($_POST['adresse']);
    $user->setRole('user'); // Default role
    
    if ($userController->addUser($user)) {
        header('Location: signin.php?account_created=1');
        exit();
    } else {
        $error = "Failed to create account. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .signup-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Create Account</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['account_created'])): ?>
            <div class="success">Account created successfully! Please login.</div>
        <?php endif; ?>
        
        <form method="POST">
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
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="professional">Professional</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="adresse">Address:</label>
                <input type="text" id="adresse" name="adresse" required>
            </div>
            
            <button type="submit">Create Account</button>
        </form>
    </div>
</body>
</html>