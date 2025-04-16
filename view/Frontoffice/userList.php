<?php
include '../../controller/UserController.php';

$userController = new UserController();
$list = $userController->getUserList();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User List - Dashboard</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-text mx-3">GradUp</div> <!-- Changed from "chakchouka" to "GradUp" -->
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="addUser.php">
                    <i class="fas fa-user-plus"></i>
                    <span>Add User</span>
                </a>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                </nav>

                <!-- Main Content -->
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">User List</h1>
                    </div>

                    <!-- User Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Registered Users</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Category</th>
                                            <th>Adresse</th>
                                            <th>Role</th>
                                            <th>Password</th>
                                            <th colspan="2">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($list as $user) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['id']); ?></td>
                                                <td><?= htmlspecialchars($user['username']); ?></td>
                                                <td><?= htmlspecialchars($user['category']); ?></td>
                                                <td><?= htmlspecialchars($user['adresse']); ?></td>
                                                <td><?= htmlspecialchars($user['role']); ?></td>
                                                <td><?= htmlspecialchars($user['password']); ?></td>
                                                <td>
                                                    <form method="POST" action="updateUser.php">
                                                        <input type="hidden" name="id" value="<?= $user['id']; ?>">
                                                        <input type="submit" name="update" value="Update" class="btn btn-primary btn-sm">
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="deleteUser.php?id=<?= $user['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End User Table -->
                </div>
                <!-- End Main Content -->
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Travel Booking 2024</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>
