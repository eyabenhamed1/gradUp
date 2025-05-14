<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Étudiant</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f2f5;
    }
    header {
      background: linear-gradient(90deg, #4a00e0, #7f00ff);
      padding: 20px 50px;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    header h1 { font-size: 28px; }
    .profile {
      display: flex;
      align-items: center;
    }
    .profile img {
      width: 50px; height: 50px;
      border-radius: 50%;
      margin-right: 10px;
      border: 2px solid #fff;
    }
    .profile span {
      font-weight: 600;
    }
    .dashboard {
      padding: 30px 50px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }
    .card {
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      text-align: center;
      transition: 0.3s;
    }
    .card:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    .card img {
      width: 80px;
      margin-bottom: 20px;
    }
    .card h3 {
      margin-bottom: 15px;
      font-weight: 600;
      color: #4a00e0;
    }
    .card p {
      color: #555;
    }
    .actions {
      margin-top: 40px;
      text-align: center;
    }
    .btn {
      padding: 12px 25px;
      background: #7f00ff;
      color: white;
      border: none;
      border-radius: 30px;
      font-size: 16px;
      margin: 0 10px;
      cursor: pointer;
      transition: background 0.3s;
      text-decoration: none;
    }
    .btn:hover {
      background: #4a00e0;
    }
  </style>
</head>
<body>

<header>
  <h1>Tableau de Bord</h1>
  <div class="profile">
    <img src="assets/img/user.png" alt="Photo de Profil">
    <span>Bienvenue, <b>Nom Utilisateur</b></span>
  </div>
</header>

<section class="dashboard">
  <div class="card">
    <img src="assets/img/users.png" alt="Utilisateurs">
    <h3>Utilisateurs</h3>
    <p>Gérez votre profil et consultez les autres étudiants.</p>
  </div>
  <div class="card">
    <img src="assets/img/stats.png" alt="Statistiques">
    <h3>Statistiques</h3>
    <p>Suivez vos performances et votre progression.</p>
  </div>
  <div class="card">
    <img src="assets/img/rewards.png" alt="Récompenses">
    <h3>Récompenses</h3>
    <p>Découvrez les prix que vous pouvez gagner !</p>
  </div>
</section>

<div class="actions">
  <a href="profile.php" class="btn">Mon Compte</a>
  <a href="auth/logout.php" class="btn">Déconnexion</a>
</div>

</body>
</html>
