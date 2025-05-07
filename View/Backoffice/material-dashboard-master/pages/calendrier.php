<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/../../../../controller/evenementcontroller.php");
$controller = new EvenementController();
$events = $controller->getEventsForCalendar();

if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['message']).'</div>';
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Calendrier des Événements | Backoffice</title>
  
  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  
  <!-- Polices et icônes -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <!-- FullCalendar CSS -->
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    #calendar {
      margin: 20px auto;
      max-width: 100%;
      padding: 15px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .fc-event {
      cursor: pointer;
      transition: all 0.3s;
      padding: 3px 6px;
      border-left: 4px solid #28a745;
    }
    .fc-event:hover {
      opacity: 0.9;
      transform: translateY(-2px);
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .fc-toolbar-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: #2c3e50;
    }
    .fc-past-event {
      border-left-color: #6c757d !important;
      opacity: 0.8;
    }
    .fc-daygrid-event-dot {
      display: none;
    }
    .fc-event-title {
      font-weight: 500;
    }
    
    /* Style pour la sidebar active */
    .nav-link.active {
      border-left: 4px solid #fff;
      font-weight: 600;
    }
    .nav-item {
      margin-bottom: 5px;
    }
    .nav-link {
      transition: all 0.3s ease;
      border-radius: 4px;
    }
    .nav-link:hover:not(.active) {
      background-color: rgba(0,0,0,0.05);
    }
    
    /* Style pour les boutons du calendrier */
    .fc-button {
      background-color: #2c3e50 !important;
      border-color: #2c3e50 !important;
    }
    .fc-button:hover {
      background-color: #1a252f !important;
    }
    .fc-button-active {
      background-color: #1a252f !important;
    }
    
    /* Style pour le header de la card */
    .card-header-calendar {
      background-color: #2c3e50 !important;
      color: white;
    }
    
    /* Style pour les événements dans la liste */
    .fc-list-event td {
      padding: 8px 10px;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="Logo Backoffice">
        <span class="ms-1 text-sm text-dark font-weight-bold">Backoffice</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/dashboard.html">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Tableau de bord</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/tables.html">
            <i class="material-symbols-rounded opacity-5">Group</i>
            <span class="nav-link-text ms-1">Utilisateurs</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/evenement.php">
            <i class="material-symbols-rounded opacity-5">Event</i>
            <span class="nav-link-text ms-1">Événements</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/calendrier.php">
            <i class="material-symbols-rounded text-white">calendar_month</i>
            <span class="nav-link-text ms-1">Calendrier</span>
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
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Admin</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Événements</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Calendrier des événements</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
              <label class="form-label">Rechercher...</label>
              <input type="text" class="form-control" id="searchInput">
            </div>
          </div>
          <ul class="navbar-nav justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <span class="text-sm font-weight-bold"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
            </li>
          </ul>
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
                <h6 class="text-white text-capitalize ps-3">Calendrier des événements</h6>
                <div class="container mt-2">
                  <a href="evenement.php" class="btn btn-success">
                    <i class="material-symbols-rounded">list</i> Voir la liste
                  </a>
                </div>
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

    <!-- Modal Détails Événement -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-gradient-dark text-white">
            <h5 class="modal-title"></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong><i class="fas fa-calendar-day me-2"></i>Date :</strong> <span class="event-date"></span></p>
                <p><strong><i class="fas fa-map-marker-alt me-2"></i>Lieu :</strong> <span class="event-location"></span></p>
                <p><strong><i class="fas fa-tag me-2"></i>Type :</strong> <span class="event-type"></span></p>
              </div>
              <div class="col-md-6">
                <div class="text-center">
                  <img id="eventImage" src="" class="img-fluid rounded" style="max-height: 200px; display: none;" alt="Image de l'événement">
                </div>
              </div>
            </div>
            <hr>
            <p><strong><i class="fas fa-align-left me-2"></i>Description :</strong></p>
            <p class="event-description"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            <a href="#" class="btn btn-primary" id="editEventBtn">
              <i class="material-symbols-rounded">edit</i> Modifier
            </a>
            <button type="button" class="btn btn-danger" id="deleteEventBtn">
              <i class="material-symbols-rounded">delete</i> Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Confirmation Suppression -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmer la suppression</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Supprimer</button>
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
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/fr.min.js'></script>
  
  <script>
    // Initialisation du scrollbar
    if (document.querySelector('#sidenav-scrollbar')) {
      new PerfectScrollbar('#sidenav-scrollbar', {
        wheelSpeed: 0.5,
        suppressScrollX: true
      });
    }

    // Initialisation du calendrier
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap5',
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        buttonText: {
          today: 'Aujourd\'hui',
          month: 'Mois',
          week: 'Semaine',
          day: 'Jour',
          list: 'Liste'
        },
        events: <?= json_encode($events) ?>,
        eventClick: function(info) {
          // Remplir les détails de l'événement dans le modal
          $('#eventDetailsModal .modal-title').text(info.event.title);
          
          // Formater la date
          const eventDate = new Date(info.event.start);
          const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          };
          $('#eventDetailsModal .event-date').text(eventDate.toLocaleString('fr-FR', options));
          
          // Afficher les autres détails
          $('#eventDetailsModal .event-location').text(info.event.extendedProps.lieu || 'Non spécifié');
          $('#eventDetailsModal .event-type').text(info.event.extendedProps.type_evenement || 'Non spécifié');
          $('#eventDetailsModal .event-description').text(info.event.extendedProps.description || 'Aucune description disponible');
          
          // Afficher l'image si elle existe
          const imageEl = document.getElementById('eventImage');
          if (info.event.extendedProps.image) {
            imageEl.src = '../uploads/' + info.event.extendedProps.image;
            imageEl.style.display = 'block';
          } else {
            imageEl.style.display = 'none';
          }
          
          // Configurer le lien de modification
          $('#editEventBtn').attr('href', 'evenement.php?edit=' + info.event.id);
          
          // Stocker l'ID pour la suppression
          $('#eventDetailsModal').data('event-id', info.event.id);
          
          // Afficher le modal
          $('#eventDetailsModal').modal('show');
          
          // Empêcher le comportement par défaut
          info.jsEvent.preventDefault();
        },
        eventDidMount: function(info) {
          // Style différent pour les événements passés
          if (info.event.start < new Date()) {
            info.el.classList.add('fc-past-event');
          }
          
          // Ajouter un effet au survol
          info.el.style.transition = 'all 0.3s ease';
          info.el.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
          });
          info.el.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
          });
        }
      });
      
      calendar.render();

      // Gestion de la suppression
      $('#deleteEventBtn').click(function() {
        $('#eventDetailsModal').modal('hide');
        $('#deleteConfirmModal').modal('show');
      });
      
      $('#confirmDeleteBtn').click(function() {
        const eventId = $('#eventDetailsModal').data('event-id');
        window.location.href = 'delete_evenement.php?id=' + eventId;
      });
    });

    // Recherche dans le calendrier
    document.getElementById('searchInput').addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      const events = document.querySelectorAll('.fc-event');
      
      events.forEach(event => {
        const title = event.querySelector('.fc-event-title')?.textContent.toLowerCase() || '';
        if (title.includes(searchTerm)) {
          event.style.display = 'block';
        } else {
          event.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>