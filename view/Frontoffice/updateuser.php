<?php
include '../../controller/UserController.php';

$error = "";
$user = null;

// Create an instance of the controller
$userController = new UserController();

if (
    isset($_POST["username"], $_POST["password"], $_POST["category"], $_POST["adresse"], $_POST["role"], $_POST["id"])
) {
    if (
        !empty($_POST["username"]) &&
        !empty($_POST["password"]) &&
        !empty($_POST["category"]) &&
        !empty($_POST["adresse"]) &&
        !empty($_POST["role"]) &&
        !empty($_POST["id"])
    ) {
        // Create a User object with all fields, including id
        $user = new User(
            $_POST['id'],  // Pass the id from the form
            $_POST['username'],
            $_POST['password'],
            $_POST['category'],
            $_POST['adresse'],
            $_POST['role']
        );

        // Call the updateUser method
        if ($userController->updateUser($user)) {
            // Redirect to user list if update is successful
            header('Location:userList.php');
            exit();
        } else {
            $error = "Failed to update the user.";
        }
    } else {
        $error = "Missing information.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Update User - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar and content omitted for brevity -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                </nav>

                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Update the User with ID = <?php echo $_POST['id'] ?? ''; ?> </h1>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <?php
                                        if (isset($_POST['id'])) {
                                            // Use getUserById to fetch the user details based on the ID
                                            $user = $userController->getUserById($_POST['id']);
                                        ?>
                                        <form id="updateUserForm" action="" method="POST">
                                            <input type="hidden" id="id" name="id" value="<?php echo $user['id']; ?>">

                                            <label for="username">Username:</label><br>
                                            <input class="form-control form-control-user" type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>

                                            <label for="password">Password:</label><br>
                                            <input class="form-control form-control-user" type="password" id="password" name="password" value="<?php echo $user['password']; ?>" required>

                                            <label for="category">Category:</label><br>
                                            <input class="form-control form-control-user" type="text" id="category" name="category" value="<?php echo $user['category']; ?>" required>

                                            <label for="adresse">Address:</label><br>
                                            <input class="form-control form-control-user" type="text" id="adresse" name="adresse" value="<?php echo $user['adresse']; ?>" required>

                                            <label for="role">Role:</label><br>
                                            <input class="form-control form-control-user" type="text" id="role" name="role" value="<?php echo $user['role']; ?>" required>

                                            <span id="zone_error" style="color: red;"><?php echo $error; ?></span><br>

                                            <button type="submit" class="btn btn-primary btn-user btn-block">Update User</button>
                                        </form>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2024</span>
                    </div>
                </div>
            </footer>

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="js/addOffer.js"></script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>
</body>

</html>
