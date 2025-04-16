<?php
include '../../controller/UserController.php';

$error = "";
$user = null;

// Create an instance of the controller
$UserController = new UserController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST["username"], $_POST["password"], $_POST["category"], $_POST["adresse"], $_POST["role"]) &&
        !empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["category"]) && !empty($_POST["adresse"]) && !empty($_POST["role"])
    ) {
        // Create a new user object
        $user = new User(
            null,
            $_POST['username'],
            $_POST['password'],
            $_POST['category'],
            $_POST['adresse'],
            $_POST['role']
        );

        // Add the user to the database
        $UserController->addUser($user);

        // Redirect to the user list page
        header('Location: userList.php');
        exit;
    } else {
        $error = "Missing information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("addUserForm");

            form.addEventListener("submit", function (event) {
                let isValid = true;
                const username = document.getElementById("username").value.trim();
                const password = document.getElementById("password").value.trim();
                const category = document.getElementById("category").value.trim();
                const adresse = document.getElementById("adresse").value.trim();
                const role = document.getElementById("role").value.trim();

                console.log(username, password, category, adresse, role);

                // Clear previous error messages
                document.getElementById("username_error").textContent = "";
                document.getElementById("password_error").textContent = "";
                document.getElementById("category_error").textContent = "";
                document.getElementById("adresse_error").textContent = "";
                document.getElementById("role_error").textContent = "";

                // Validate 'username'
                if (username.length < 3 || username.length > 50) {
                    document.getElementById("username_error").textContent = "Username must be between 3 and 50 characters.";
                    isValid = false;
                }

                // Validate 'password'
                if (password.length < 6 || password.length > 50) {
                    document.getElementById("password_error").textContent = "Password must be between 6 and 50 characters.";
                    isValid = false;
                }

                // Validate 'category'
                if (category.length < 3 || category.length > 50) {
                    document.getElementById("category_error").textContent = "Category must be between 3 and 50 characters.";
                    isValid = false;
                }

                // Validate 'adresse'
                if (adresse.length < 3 || adresse.length > 100) {
                    document.getElementById("adresse_error").textContent = "Adresse must be between 3 and 100 characters.";
                    isValid = false;
                }

                // Validate 'role'
                if (role.length < 3 || role.length > 50) {
                    document.getElementById("role_error").textContent = "Role must be between 3 and 50 characters.";
                    isValid = false;
                }

                if (!isValid) event.preventDefault();
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Add a User</h1>
        <form id="addUserForm" action="" method="POST">
            <!-- Username -->
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username">
            <small id="username_error" class="text-danger"></small>

            <!-- Password -->
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password">
            <small id="password_error" class="text-danger"></small>

            <!-- Category -->
            <label for="category">Category:</label>
            <input type="text" class="form-control" id="category" name="category">
            <small id="category_error" class="text-danger"></small>

            <!-- Adresse -->
            <label for="adresse">Adresse:</label>
            <input type="text" class="form-control" id="adresse" name="adresse">
            <small id="adresse_error" class="text-danger"></small>

            <!-- Role -->
            <label for="role">Role:</label>
            <input type="text" class="form-control" id="role" name="role">
            <small id="role_error" class="text-danger"></small>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mt-3">Add User</button>
        </form>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>

</html>
