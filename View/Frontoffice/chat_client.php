<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gradup Shop - Assistant Virtuel</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet">
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f8;
    }
    header {
      background-color: #3498db;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      display: flex;
      align-items: center;
    }
    .logo img {
      height: 40px;
      margin-right: 10px;
    }
    nav a {
      color: white;
      margin: 0 1rem;
      text-decoration: none;
      font-weight: 500;
    }
    .main-content {
      padding: 30px;
      background-color: #f8f9fa;
      min-height: calc(100vh - 200px);
    }
    .card-body {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      padding: 30px;
      margin-top: 20px;
    }
    .breadcrumb {
      background-color: transparent;
      padding: 0;
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 20px;
    }
    .breadcrumb-item a {
      color: #3498db;
      text-decoration: none;
    }
    .breadcrumb-item a:hover {
      text-decoration: underline;
    }
    .breadcrumb-item.active {
      color: #6c757d;
    }
    .chat-container {
      display: flex;
      flex-direction: column;
      height: 500px;
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
    }
    .chat-header {
      background-color: #3498db;
      color: white;
      padding: 15px;
      text-align: center;
      font-weight: bold;
    }
    .chat-messages {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      background-color: #f8f9fa;
    }
    .message {
      margin-bottom: 15px;
      padding: 12px 16px;
      border-radius: 8px;
      max-width: 70%;
      line-height: 1.5;
    }
    .user-message {
      background-color: #e3f2fd;
      margin-left: auto;
      border-bottom-right-radius: 0;
    }
    .bot-message {
      background-color: #f1f1f1;
      margin-right: auto;
      border-bottom-left-radius: 0;
    }
    .chat-input {
      display: flex;
      padding: 15px;
      border-top: 1px solid #ddd;
      background-color: white;
    }
    #userInput {
      flex: 1;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 20px;
      outline: none;
      font-family: 'Poppins', sans-serif;
    }
    #sendBtn {
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 20px;
      padding: 12px 25px;
      margin-left: 10px;
      cursor: pointer;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      transition: background-color 0.3s;
    }
    #sendBtn:hover {
      background-color: #2980b9;
    }
    .status-message {
      color: #666;
      font-size: 0.9rem;
      text-align: center;
      padding: 5px;
      font-style: italic;
    }
    footer {
      background-color: #3498db;
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
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
      <a href="read_corection1.php?"#>Corrections</a>
      <a href="readtype.php?"#>exams</a>
    </nav>
  </header>

  <div class="main-content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active" aria-current="page">Assistant Virtuel</li>
      </ol>
    </nav>

    <h2 class="section-title">Assistant Virtuel Gradup</h2>

    <div class="card-body">
      <div class="chat-container">
        <div class="chat-header">
          <i class="fas fa-robot"></i> Assistant Éducatif
        </div>
        <div class="chat-messages" id="chatbox">
          <div class="status-message" id="connectionStatus">Connexion au serveur en cours...</div>
          <div class="message bot-message">
            Bonjour! Je suis l'assistant virtuel de Gradup. Comment puis-je vous aider aujourd'hui?
          </div>
        </div>
        <div class="chat-input">
          <input type="text" id="userInput" placeholder="Tapez votre message ici..." />
          <button id="sendBtn">Envoyer</button>
        </div>
      </div>
    </div>
  </div>

  <footer>
    &copy; 2025 Gradup Shop. Tous droits réservés. | Contact : gradup@edu.tn | +216 99 999 999
  </footer>

  <!-- JS -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  
  <script>
    const socket = io("http://127.0.0.1:5000");
    const chatbox = document.getElementById("chatbox");
    const userInput = document.getElementById("userInput");
    const sendBtn = document.getElementById("sendBtn");
    const connectionStatus = document.getElementById("connectionStatus");

    // Connection status handling
    socket.on("connect", () => {
      connectionStatus.textContent = "Connecté au serveur";
      connectionStatus.style.color = "green";
      setTimeout(() => {
        connectionStatus.style.display = "none";
      }, 2000);
    });

    socket.on("disconnect", () => {
      connectionStatus.textContent = "Déconnecté du serveur - Tentative de reconnexion...";
      connectionStatus.style.color = "red";
      connectionStatus.style.display = "block";
    });

    socket.on("connect_error", () => {
      connectionStatus.textContent = "Erreur de connexion au serveur";
      connectionStatus.style.color = "red";
    });

    // Message handling
    socket.on("message", (data) => {
      const messageDiv = document.createElement("div");
      messageDiv.className = "message bot-message";
      messageDiv.innerHTML = data;
      chatbox.appendChild(messageDiv);
      chatbox.scrollTop = chatbox.scrollHeight;
    });

    // Send message function
    function sendMessage() {
      const message = userInput.value.trim();
      if (message !== "") {
        // Add user message to chat
        const userMessageDiv = document.createElement("div");
        userMessageDiv.className = "message user-message";
        userMessageDiv.textContent = message;
        chatbox.appendChild(userMessageDiv);
        
        // Send to server
        socket.send(message);
        userInput.value = "";
        chatbox.scrollTop = chatbox.scrollHeight;
      }
    }

    // Event listeners
    sendBtn.addEventListener("click", sendMessage);
    userInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        sendMessage();
      }
    });

    // Focus input on load
    window.addEventListener("load", () => {
      userInput.focus();
    });
  </script>
</body>
</html>