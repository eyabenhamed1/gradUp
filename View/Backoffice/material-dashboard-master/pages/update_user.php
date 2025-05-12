<?php
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php';
$conn = config::getConnexion();

if (!isset($_GET['id'])) {
    die("User ID is missing.");
}

$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    try {
        $stmt = $conn->prepare("UPDATE user SET name = :name, email = :email, password = :password, role = :role WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role,
            ':id' => $id
        ]);
        header("Location: tables.php?message=User+updated+successfully");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();
    if (!$user) {
        die("User not found.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Update User</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>
<body class="g-sidenav-show bg-gray-100">

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-lg-6 col-md-8 col-12 mx-auto">
          <div class="card">
            <div class="card-header text-center">
              <h5 class="mb-0">Update User</h5>
            </div>
            <div class="card-body">
              <form method="POST" action="">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($user['password']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Role</label>
                  <input type="text" name="role" class="form-control" value="<?= htmlspecialchars($user['role']) ?>" required>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Update</button>
                  <a href="tables.php" class="btn btn-secondary">Cancel</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
