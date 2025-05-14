<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'projetweb2a';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

// Récupérer les dates de livraison
$query = "SELECT date_livraison, COUNT(*) as nb_commandes FROM commande 
          WHERE date_livraison IS NOT NULL GROUP BY date_livraison";
$datesLivraison = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les événements
$queryEvenements = "SELECT id, titre, date_evenement, lieu, type_evenement FROM evenement";
$evenements = $db->query($queryEvenements)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Calendrier des Livraisons</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <!-- FullCalendar CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <style>
    /* Styles existants de commande.php */
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
    }
    
    .status-en-cours {
      background-color: #f39c12;
      color: white;
    }
    
    .status-validee {
      background-color: #2ecc71;
      color: white;
    }
    
    .status-null {
      background-color: #95a5a6;
      color: white;
    }
    
    /* Styles spécifiques au calendrier */
    #calendar {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-top: 20px;
    }
    
    .fc-event {
      cursor: pointer;
      border: none !important;
    }
    
    .fc-toolbar-title {
      color: #344767;
      font-weight: 600;
    }
    
    .fc-button {
      background-color: #e9ecef !important;
      border: none !important;
      color: #344767 !important;
      box-shadow: none !important;
    }
    
    .fc-button-active {
      background-color: #344767 !important;
      color: white !important;
    }
    
    .fc-daygrid-event {
      border-radius: 4px;
    }
    
    /* Style pour les événements */
    .event-evenement {
      background-color: #4CAF50 !important;
      border: none !important;
      color: white !important;
    }
    
    .event-livraison {
      background-color: #2196F3 !important;
      border: none !important;
      color: white !important;
    }

    /* Légende */
    .calendar-legend {
      display: flex;
      gap: 15px;
      margin: 20px;
      padding: 10px;
      background: #f8f9fa;
      border-radius: 8px;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .legend-color {
      width: 20px;
      height: 20px;
      border-radius: 4px;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="ms-1 text-sm text-dark">Gestion Commandes</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/dashboard.html">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="commande.php">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Commandes</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="calendrier.php">
            <i class="material-symbols-rounded opacity-5">calendar_today</i>
            <span class="nav-link-text ms-1">Calendrier</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/produit.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">Produits</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Calendrier des Livraisons</li>
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
              <label class="form-label">Rechercher une date...</label>
              <input type="text" class="form-control" id="dateSearch">
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Calendrier des Livraisons et Événements</h6>
              </div>
            </div>
            
            <!-- Légende -->
            <div class="calendar-legend">
              <div class="legend-item">
                <div class="legend-color" style="background-color: #2196F3;"></div>
                <span>Livraisons</span>
              </div>
              <div class="legend-item">
                <div class="legend-color" style="background-color: #4CAF50;"></div>
                <span>Événements</span>
              </div>
            </div>

            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <div id="calendar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Scripts -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const events = [
        // Ajouter les livraisons
        <?php foreach($datesLivraison as $date): ?>
        {
          title: '<?= $date['nb_commandes'] ?> livraison(s)',
          start: '<?= $date['date_livraison'] ?>',
          className: 'event-livraison',
          url: 'commande.php?date=<?= $date['date_livraison'] ?>'
        },
        <?php endforeach; ?>

        // Ajouter les événements
        <?php foreach($evenements as $event): ?>
        {
          title: '<?= htmlspecialchars($event['titre']) ?>',
          start: '<?= $event['date_evenement'] ?>',
          className: 'event-evenement',
          url: 'evenement.php?id=<?= $event['id'] ?>',
          extendedProps: {
            type: '<?= htmlspecialchars($event['type_evenement']) ?>',
            lieu: '<?= htmlspecialchars($event['lieu']) ?>'
          }
        },
        <?php endforeach; ?>
      ];
      
      const calendarEl = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: events,
        eventDisplay: 'block',
        eventTimeFormat: { 
          hour: '2-digit',
          minute: '2-digit',
          meridiem: false
        },
        eventMouseEnter: function(info) {
          // Ajouter un tooltip
          const event = info.event;
          let tooltipText = event.title;
          
          if (event.extendedProps.type) {
            tooltipText += '\nType: ' + event.extendedProps.type;
            tooltipText += '\nLieu: ' + event.extendedProps.lieu;
          }
          
          info.el.title = tooltipText;
        }
      });
      
      calendar.render();

      // Initialisation du scrollbar
      var win = navigator.platform.indexOf('Win') > -1;
      if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = { damping: '0.5' }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
      }
    });
  </script>
</body>
</html>