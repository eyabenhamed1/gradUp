<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
$success = '';
$error = '';
$conn = new mysqli('localhost','root','','projetweb2a');
if ($conn->connect_error) die('Erreur: '.$conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $role     = trim($_POST['role']);
    $category = trim($_POST['category']);
    $photoPath = $user['photo'];

    // Create uploads directory if it doesn't exist
    $uploadsDir = 'uploads';
    if (!file_exists($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    if (isset($_POST['delete_photo'])) {
        if ($photoPath !== 'uploads/default.png' && file_exists($photoPath)) {
            unlink($photoPath);
        }
        $photoPath = 'uploads/default.png';
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['photo']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        
        if (in_array($ext, $allowed)) {
            $newName = $uploadsDir . '/pdp_' . time() . '_' . uniqid() . '.' . $ext;
            
            if (move_uploaded_file($tmp_name, $newName)) {
                // Delete old photo if it exists and is not the default
                if ($photoPath !== 'uploads/default.png' && file_exists($photoPath)) {
                    unlink($photoPath);
                }
                $photoPath = $newName;
            } else {
                $error = 'Échec du téléversement de la photo.';
            }
        } else {
            $error = 'Format d\'image non autorisé. Formats acceptés: JPG, JPEG, PNG, GIF';
        }
    }

    if (!$error) {
        $stmt=$conn->prepare("UPDATE user SET name=?,email=?,role=?,category=?,photo=? WHERE id=?");
        $stmt->bind_param('sssssi',$name,$email,$role,$category,$photoPath,$user['id']);
        if ($stmt->execute()){
            $_SESSION['user']['name']=$name;
            $_SESSION['user']['email']=$email;
            $_SESSION['user']['role']=$role;
            $_SESSION['user']['category']=$category;
            $_SESSION['user']['photo']=$photoPath;
            $user=$_SESSION['user'];
            $success='Profil mis à jour';
        } else $error='Erreur BDD';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil de <?=htmlspecialchars($user['name'])?> - Gradup</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/header.css">
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
      margin: 0;
      padding: 0;
      background-color: var(--light);
      color: var(--dark);
      line-height: 1.6;
    }
    
    /* Main Content */
    .main {
      padding: 2rem 0;
      min-height: calc(100vh - 120px);
    }
    
    /* Profile container */
    .profile-container {
      background: var(--white);
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      padding: 40px;
      margin-bottom: 40px;
    }
    
    /* Profile header */
    .profile-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 30px;
      text-align: center;
    }
    
    .profile-avatar {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid var(--light);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }
    
    .profile-name {
      font-size: 1.8rem;
      color: var(--primary);
      margin-bottom: 5px;
    }
    
    .profile-role {
      color: var(--secondary);
      font-weight: 500;
      margin-bottom: 15px;
    }
    
    .edit-profile-btn {
      background-color: var(--primary);
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 30px;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    
    .edit-profile-btn:hover {
      background-color: var(--accent);
      transform: translateY(-2px);
    }
    
    /* Profile details */
    .profile-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      margin-top: 40px;
    }
    
    .detail-card {
      background: var(--light);
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .detail-title {
      font-size: 0.9rem;
      color: var(--medium-gray);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }
    
    .detail-value {
      font-size: 1.2rem;
      color: var(--primary);
      font-weight: 600;
    }
    
    .detail-icon {
      color: var(--accent);
      font-size: 1.5rem;
      margin-bottom: 10px;
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    
    .modal-content {
      background-color: var(--white);
      margin: 5% auto;
      padding: 30px;
      border-radius: 10px;
      width: 90%;
      max-width: 600px;
      max-height: 80vh;
      overflow-y: auto;
      box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    }
    
    .close-modal {
      color: var(--medium-gray);
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      transition: var(--transition);
    }
    
    .close-modal:hover {
      color: var(--accent);
    }
    
    .modal-title {
      font-size: 1.8rem;
      color: var(--primary);
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--light-gray);
    }
    
    /* Form styles */
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--primary-light);
    }
    
    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid var(--light-gray);
      border-radius: 6px;
      font-family: 'Poppins', sans-serif;
      transition: var(--transition);
      font-size: 1rem;
    }
    
    .form-control:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
    }
    
    /* Photo upload */
    .photo-upload-container {
      text-align: center;
      margin-bottom: 25px;
    }
    
    .preview-img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid var(--light);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }
    
    .photo-upload-label {
      display: inline-block;
      padding: 10px 20px;
      background-color: var(--primary);
      color: white;
      border-radius: 6px;
      cursor: pointer;
      transition: var(--transition);
      font-weight: 500;
    }
    
    .photo-upload-label:hover {
      background-color: var(--primary-light);
    }
    
    .photo-upload-label i {
      margin-right: 8px;
    }
    
    #photo-upload {
      display: none;
    }
    
    /* Form buttons */
    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 30px;
    }
    
    .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 6px;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    
    .btn-primary {
      background-color: var(--primary);
      color: white;
    }
    
    .btn-primary:hover {
      background-color: var(--primary-light);
      transform: translateY(-2px);
    }
    
    .btn-danger {
      background-color: var(--accent);
      color: white;
    }
    
    .btn-danger:hover {
      background-color: #c0392b;
      transform: translateY(-2px);
    }
    
    /* Messages */
    .alert {
      padding: 15px;
      border-radius: 6px;
      margin-bottom: 25px;
      font-weight: 500;
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    /* Footer */
    .footer {
      background-color: var(--dark);
      color: var(--white);
      padding: 3rem 0 1.5rem;
      margin-top: 60px;
    }
    
    .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 2rem;
    }
    
    .footer-col h3 {
      font-size: 1.2rem;
      margin-bottom: 1.5rem;
      position: relative;
      padding-bottom: 0.5rem;
      color: var(--white);
    }
    
    .footer-col h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 40px;
      height: 2px;
      background-color: var(--accent);
    }
    
    .footer-links {
      list-style: none;
    }
    
    .footer-link {
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .footer-link a {
      color: var(--light-gray);
      text-decoration: none;
      transition: var(--transition);
      font-size: 0.9rem;
    }
    
    .footer-link a:hover {
      color: var(--accent);
      padding-left: 5px;
    }
    
    .social-links {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }
    
    .social-link {
      color: var(--white);
      background-color: rgba(255, 255, 255, 0.1);
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
    }
    
    .social-link:hover {
      background-color: var(--accent);
      transform: translateY(-3px);
    }
    
    .footer-bottom {
      text-align: center;
      padding-top: 2rem;
      margin-top: 2rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      font-size: 0.85rem;
      color: var(--medium-gray);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .profile-header {
        text-align: center;
      }
      
      .profile-details {
        grid-template-columns: 1fr;
      }
      
      .modal-content {
        padding: 20px;
      }
      
      .form-actions {
        flex-direction: column;
      }
      
      .btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="main">
        <h1 class="page-title">Mon Profil</h1>
        
        <div class="profile-container">
          <?php if($success): ?>
            <div class="alert alert-success"><?=$success?></div>
          <?php endif; ?>
          
          <?php if($error): ?>
            <div class="alert alert-error"><?=$error?></div>
          <?php endif; ?>
          
          <div class="profile-header">
            <img src="<?=htmlspecialchars($user['photo'])?>" alt="Photo de profil" class="profile-avatar" onerror="this.src='uploads/default.png'">
            <h2 class="profile-name"><?=htmlspecialchars($user['name'])?></h2>
            <p class="profile-role"><?=htmlspecialchars($user['role'])?></p>
            <button class="edit-profile-btn" onclick="openModal()">
              <i class="fas fa-edit"></i>
              Modifier le profil
            </button>
          </div>
          
          <div class="profile-details">
            <div class="detail-card">
              <i class="fas fa-envelope detail-icon"></i>
              <h3 class="detail-title">Email</h3>
              <p class="detail-value"><?=htmlspecialchars($user['email'])?></p>
            </div>
            
            <div class="detail-card">
              <i class="fas fa-user-tag detail-icon"></i>
              <h3 class="detail-title">Rôle</h3>
              <p class="detail-value"><?=htmlspecialchars($user['role'])?></p>
            </div>
            
            <div class="detail-card">
              <i class="fas fa-th-large detail-icon"></i>
              <h3 class="detail-title">Classe</h3>
              <p class="detail-value"><?=htmlspecialchars($user['category'])?></p>
            </div>
          </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
      <div class="footer-container">
        <div class="footer-col">
          <h3>Gradup</h3>
          <p>La plateforme tout-en-un pour les étudiants tunisiens.</p>
          <div class="social-links">
            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        
        <div class="footer-col">
          <h3>Liens rapides</h3>
          <ul class="footer-links">
            <li class="footer-link"><a href="index.php">Accueil</a></li>
            <li class="footer-link"><a href="boutique.php">Boutique</a></li>
            <li class="footer-link"><a href="elearning.php">E-Learning</a></li>
            <li class="footer-link"><a href="clubs.php">Clubs</a></li>
          </ul>
        </div>
        
        <div class="footer-col">
          <h3>Informations</h3>
          <ul class="footer-links">
            <li class="footer-link"><a href="a-propos.php">À propos</a></li>
            <li class="footer-link"><a href="contact.php">Contact</a></li>
            <li class="footer-link"><a href="conditions.php">Conditions</a></li>
            <li class="footer-link"><a href="confidentialite.php">Confidentialité</a></li>
          </ul>
        </div>
        
        <div class="footer-col">
          <h3>Contactez-nous</h3>
          <ul class="footer-links">
            <li class="footer-link"><i class="fas fa-map-marker-alt"></i> Tunis, Tunisie</li>
            <li class="footer-link"><i class="fas fa-phone"></i> +216 12 345 678</li>
            <li class="footer-link"><i class="fas fa-envelope"></i> contact@gradup.tn</li>
          </ul>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Gradup. Tous droits réservés.</p>
      </div>
    </footer>
    
    <!-- Edit Profile Modal -->
    <div id="modal" class="modal">
      <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2 class="modal-title">Modifier le profil</h2>
        
        <form method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <div class="photo-upload-container">
              <img id="preview" src="<?=htmlspecialchars($user['photo'])?>" class="preview-img" alt="Photo de profil" onerror="this.src='uploads/default.png'">
              <label for="photo-upload" class="photo-upload-label">
                <i class="fas fa-camera"></i> Changer la photo
              </label>
              <input type="file" id="photo-upload" name="photo" accept="image/*" onchange="previewImage(event)">
            </div>
          </div>
          
          <div class="form-group">
            <label for="name">Nom complet</label>
            <input type="text" id="name" name="name" class="form-control" value="<?=htmlspecialchars($user['name'])?>" required>
          </div>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?=htmlspecialchars($user['email'])?>" required>
          </div>
          
          <div class="form-group">
            <label for="role">Rôle</label>
            <input type="text" id="role" name="role" class="form-control" value="<?=htmlspecialchars($user['role'])?>" required>
          </div>
          
          <div class="form-group">
            <label for="category">Classe</label>
            <input type="text" id="category" name="category" class="form-control" value="<?=htmlspecialchars($user['category'])?>">
          </div>
          
          <div class="form-actions">
            <button type="submit" name="delete_photo" value="1" class="btn btn-danger">
              <i class="fas fa-trash"></i> Supprimer photo
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <script>
      function openModal() {
        document.getElementById('modal').style.display = 'block';
      }
      
      function closeModal() {
        document.getElementById('modal').style.display = 'none';
      }
      
      function previewImage(e) {
        const preview = document.getElementById('preview');
        const file = e.target.files[0];
        
        if (file) {
          // Check file size (max 5MB)
          if (file.size > 5 * 1024 * 1024) {
            alert('La taille de l\'image ne doit pas dépasser 5MB');
            e.target.value = '';
            return;
          }
          
          // Check file type
          const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
          if (!validTypes.includes(file.type)) {
            alert('Format d\'image non autorisé. Utilisez JPG, JPEG, PNG ou GIF');
            e.target.value = '';
            return;
          }
          
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.src = e.target.result;
          }
          reader.readAsDataURL(file);
        } else {
          preview.src = 'uploads/default.png';
        }
      }
      
      // Close modal when clicking outside
      window.onclick = function(event) {
        const modal = document.getElementById('modal');
        if (event.target === modal) {
          closeModal();
        }
      };

      // Handle image load errors
      document.querySelectorAll('img').forEach(img => {
        img.onerror = function() {
          this.src = 'uploads/default.png';
        }
      });
    </script>
</body>
</html>