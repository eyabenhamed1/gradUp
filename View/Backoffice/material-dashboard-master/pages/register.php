<?php
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php'; // Chemin absolu vers config.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'user'; // Par défaut, rôle utilisateur

    // Validation des champs
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer un email valide.";
    } else {
        // Vérification de l'existence de l'email
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Cet email est déjà enregistré.";
        } else {
            // Insertion des données
            $stmt = $conn->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, password_hash($password, PASSWORD_DEFAULT), $role);
            if ($stmt->execute()) {
                header('Location: sign-in.php');
                exit();
            } else {
                $error = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
