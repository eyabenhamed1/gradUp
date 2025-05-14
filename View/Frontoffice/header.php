<?php
// Add any necessary session handling or user authentication here if needed
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gradup Shop</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet">
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
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

body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: var(--light);
    color: var(--dark);
    line-height: 1.6;
}

header {
    background-color: var(--primary);
    color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    transition: transform 0.3s ease;
}

.logo:hover {
    transform: translateY(-2px);
}

.logo img {
    height: 40px;
    margin-right: 10px;
    border-radius: 50%;
}

nav {
    display: flex;
    align-items: center;
    gap: 1rem;
}

nav a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    font-size: 1rem;
    padding: 0.5rem 0;
    transition: all 0.3s ease;
    position: relative;
}

nav a:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--accent);
    transition: width 0.3s ease;
}

nav a:hover {
    color: var(--accent);
}

nav a:hover:after {
    width: 100%;
}

@media (max-width: 992px) {
    nav {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    nav a {
        margin: 0.5rem;
    }
}

@media (max-width: 768px) {
    header {
        flex-direction: column;
        padding: 1rem;
    }
    
    .logo {
        margin-bottom: 1rem;
    }
    
    nav {
        width: 100%;
        justify-content: space-around;
    }
}
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <img src="logo.jpeg" alt="logo">
      <h1>Gradup Shop</h1>
    </div>
    <nav>
      <a href="#">Accueil</a>
      <a href="#">Boutique</a>
      <a href="#">Cours</a>
      <a href="#">Forum</a>
      <a href="#">Événements</a>
      <a href="#">Dons</a>
      <a href="read_correction1.php">Corrections</a>
      <a href="chat_client.php">chatbot</a>
    </nav>
  </header>
</body>
</html> 