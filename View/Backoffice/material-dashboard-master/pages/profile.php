<?php 
session_start();
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$conn = config::getConnexion();
$sql = "SELECT name, email, role FROM user WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();
if (!$user) {
    die("Utilisateur introuvable.");
}
$name  = htmlspecialchars($user['name']);
$email = htmlspecialchars($user['email']);
$role  = htmlspecialchars($user['role']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Profil Admin – GradUp</title>
  <!-- Font & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --bg: #e0e5ec;
      --light-shadow: #ffffff;
      --dark-shadow: #a3b1c6;
      --accent: #ff8c42;
      --text-dark: #333;
      --sidebar-bg: #2f3e4e;
      --sidebar-text: #fff;
    }
    * { box-sizing: border-box; margin:0; padding:0; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      display: flex;
      min-height: 100vh;
    }
    /* Sidebar */
    .sidebar {
      width: 220px;
      background: var(--sidebar-bg);
      color: var(--sidebar-text);
      padding: 2rem 1rem;
      display: flex;
      flex-direction: column;
    }
    .sidebar .brand {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 2rem;
      text-align: center;
      letter-spacing: .5px;
    }
    .sidebar nav a {
      display: flex;
      align-items: center;
      padding: .75rem 1rem;
      border-radius: 12px;
      color: var(--sidebar-text);
      text-decoration: none;
      margin-bottom: .5rem;
      transition: background .2s;
    }
    .sidebar nav a i {
      margin-right: 10px;
    }
    .sidebar nav a.active,
    .sidebar nav a:hover {
      background: var(--accent);
      color: #fff;
    }
    .sidebar .logout {
      margin-top: auto;
    }
    .sidebar .logout a {
      display: block;
      padding: .75rem 1rem;
      text-align: center;
      background: var(--accent);
      color: #fff;
      border-radius: 12px;
      text-decoration: none;
      margin-top: 2rem;
    }
    /* Main */
    .main {
      flex: 1;
      padding: 2rem;
    }
    .page-header {
      height: 180px;
      background: url('../assets/img/cover.jpg') center/cover no-repeat;
      border-radius: 12px;
      position: relative;
      margin-bottom: 2rem;
    }
    .page-header::after {
      content:"";
      position:absolute; inset:0;
      background: rgba(0,0,0,0.2);
      border-radius:12px;
    }
    /* Neumorphic card */
    .card {
      background: var(--bg);
      border-radius: 16px;
      box-shadow: 
        8px 8px 16px var(--dark-shadow),
        -8px -8px 16px var(--light-shadow);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    .profile-header {
      display: flex;
      align-items: center;
      margin-top: -70px;
    }
    .profile-header img {
      width: 100px; height:100px;
      border-radius:50%;
      object-fit: cover;
      box-shadow:
        6px 6px 12px var(--dark-shadow),
        -6px -6px 12px var(--light-shadow);
    }
    .profile-header .info {
      margin-left: 1.5rem;
    }
    .profile-header .info h2 {
      color: var(--text-dark);
      font-size: 1.8rem;
    }
    .profile-header .info p {
      color: var(--accent);
      margin-top: .25rem;
    }
    form .form-group {
      margin-bottom: 1.25rem;
    }
    form label {
      display: block;
      color: var(--text-dark);
      margin-bottom: .5rem;
      font-weight: 500;
    }
    form input {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 12px;
      background: var(--bg);
      box-shadow: 
        inset 4px 4px 8px var(--dark-shadow),
        inset -4px -4px 8px var(--light-shadow);
      font-size: 1rem;
      color: var(--text-dark);
      outline: none;
      transition: box-shadow .2s;
    }
    form input:focus {
      box-shadow:
        inset 2px 2px 4px var(--dark-shadow),
        inset -2px -2px 4px var(--light-shadow);
    }
    form button {
      padding: 1rem 2rem;
      background: var(--accent);
      color: #fff;
      border: none;
      border-radius: 12px;
      font-size: 1rem;
      cursor: pointer;
      transition: background .2s;
    }
    form button:hover {
      background: darken(var(--accent),10%);
    }
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="brand">GradUp</div>
  <nav>
    <a href="profile.php" class="active"><i class="fas fa-user-circle"></i> Profil</a>
    <a href="shopping.php"><i class="fas fa-shopping-cart"></i> Shopping</a>
    <a href="events.php"><i class="fas fa-calendar-alt"></i> Événements</a>
    <a href="elearning.php"><i class="fas fa-laptop"></i> E-learning</a>
    <a href="certifications.php"><i class="fas fa-certificate"></i> Certifications</a>
    <a href="tables.php"><i class="fas fa-table"></i> Tables</a>
  </nav>
  <div class="logout">
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
  </div>
</aside>

  <!-- Main Content -->
  <main class="main">
    <div class="page-header"></div>
    <div class="card">
      <div class="profile-header">
        <img src="../assets/img/avatar.jpg" alt="Avatar">
        <div class="info">
          <h2><?php echo $name; ?></h2>
          <p><?php echo $role; ?></p>
        </div>
      </div>
      <div class="form-group">
        <label>Nom</label>
        <p><?php echo $name; ?></p>
      </div>
      <div class="form-group">
        <label>Email</label>
        <p><?php echo $email; ?></p>
      </div>
      <div class="form-group">
        <label>Rôle</label>
        <p><?php echo $role; ?></p>
      </div>
      <!-- Button to trigger the modal -->
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateProfileModal">Mettre à jour</button>
    </div>
  </main>

  <!-- Modal for profile update -->
  <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateProfileModalLabel">Mettre à jour le profil</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="update-profile.php" method="POST">
            <div class="form-group">
              <label for="name">Nom complet</label>
              <input id="name" name="name" type="text" value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
              <label for="email">Adresse email</label>
              <input id="email" name="email" type="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
              <label for="role">Rôle</label>
              <input id="role" name="role" type="text" value="<?php echo $role; ?>" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
