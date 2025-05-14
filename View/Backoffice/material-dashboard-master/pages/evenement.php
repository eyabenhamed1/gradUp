<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/../../../../controller/evenementcontroller.php");
require_once(__DIR__ . "/../../../../controller/participationcontroller.php");

$controller = new evenementController();
$participationController = new ParticipationController();
$evenement = $controller->listeEvenement();

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
  <title>Gestion des Événements | Backoffice</title>
  
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
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/fr.min.js'></script>
  <style>
    .event-image {
      max-width: 80px;
      max-height: 80px;
      border-radius: 4px;
      object-fit: cover;
    }
    .action-buttons .btn {
      margin: 2px;
      padding: 0.3rem 0.6rem;
    }
    .modal-form label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    .modal-form input, .modal-form textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .modal-form .file-info {
      font-size: 12px;
      color: #666;
      margin-top: 5px;
    }
    .image-preview {
      max-width: 100px;
      max-height: 100px;
      display: none;
      margin-top: 10px;
    }
    .input-error {
      border-color: #f44336 !important;
    }
    .no-image {
      width: 80px;
      height: 80px;
      background: #f8f9fa;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6c757d;
    }

    .fc-event {
    cursor: pointer;
    border-radius: 4px;
    padding: 2px 4px;
}
.fc-event:hover {
    text-decoration: underline;
    
}
.nav-link i.fas {
    margin-right: 8px;
    width: 20px;
    text-align: center;
}

/* Pour la version Material Dashboard */
.nav-link .material-symbols-rounded {
    font-size: 1.2rem;
    vertical-align: middle;
    margin-right: 10px;}
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
        <a class="nav-link active bg-gradient-dark text-white" href="../pages/evenement.php">
        <i class="material-symbols-rounded text-white">Event</i>
            <span class="nav-link-text ms-1">Événements</span>
          </a>
        </li>
        <ul class="navbar-nav">
    <!-- Autres éléments -->
    <li class="nav-item">
        <a class="nav-link text-dark" href="calendrier.php">
            <i class="material-symbols-rounded opacity-5">calendar_month</i>
            <span class="nav-link-text ms-1">Calendrier</span>
        </a>
    </li>
    <li class="nav-item">
    <a class="nav-link text-dark" href="../pages/statistiques.php">
        <i class="material-symbols-rounded opacity-5">bar_chart</i>
        <span class="nav-link-text ms-1">Statistiques</span>
    </a>
</li>
</ul>
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
          <h6 class="font-weight-bolder mb-0">Gestion des événements</h6>
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
      <!-- Message d'alerte -->
      <?php if (!empty($alertMessage)): ?>
        <div class="alert alert-<?= $alertClass ?> alert-dismissible fade show">
          <?= htmlspecialchars($alertMessage) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-12">
          <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Liste des événements</h6>
                <div class="container mt-2">
                  <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="material-symbols-rounded">add</i> Ajouter un événement
                  </button>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">

    </div>

              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="eventsTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Titre</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Image</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Lieu</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Type</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Participants</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($evenement as $event): 
                      $participantsCount = $participationController->countParticipants($event['id']);
                    ?>
                    <tr>
                      <td>
                        <div class="d-flex px-3 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm font-weight-bold"><?= htmlspecialchars($event['titre']) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <?php if (!empty($event['image']) && file_exists('../uploads/' . $event['image'])): ?>
                          <img src="<?= '../uploads/' . htmlspecialchars($event['image']) ?>" 
                               class="event-image" 
                               alt="Image de <?= htmlspecialchars($event['titre']) ?>"
                               loading="lazy">
                        <?php else: ?>
                          <div class="no-image">
                            <i class="fas fa-image fa-lg"></i>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td class="align-middle">
                        <p class="text-xs font-weight-bold mb-0 text-truncate" style="max-width: 200px;">
                          <?= htmlspecialchars($event['description']) ?>
                        </p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-xs font-weight-bold">
                          <?= date('d/m/Y', strtotime($event['date_evenement'])) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="badge bg-gradient-info">
                          <?= htmlspecialchars($event['lieu']) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="badge bg-gradient-warning">
                          <?= htmlspecialchars($event['type_evenement']) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center">
                  <a href="participants_details.php?event_id=<?= $event['id'] ?>" class="text-decoration-none">
                <span class="badge bg-gradient-success">
                      <?= $participantsCount ?>
                      <i class="fas fa-users ms-1"></i>
                     </span>
                        </a>
                         </td>
                      <td class="align-middle text-center action-buttons">
                        <button class="btn btn-sm btn-warning edit-event" 
                                data-id="<?= $event['id'] ?>"
                                data-titre="<?= htmlspecialchars($event['titre']) ?>"
                                data-description="<?= htmlspecialchars($event['description']) ?>"
                                data-date="<?= htmlspecialchars($event['date_evenement']) ?>"
                                data-lieu="<?= htmlspecialchars($event['lieu']) ?>"
                                data-type="<?= htmlspecialchars($event['type_evenement']) ?>"
                                data-image="<?= !empty($event['image']) ? '../uploads/' . htmlspecialchars($event['image']) : '' ?>"
                                title="Modifier"
                                data-bs-toggle="modal" 
                                data-bs-target="#editEventModal">
                          <i class="material-symbols-rounded">edit</i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-event" 
                                data-id="<?= $event['id'] ?>"
                                title="Supprimer">
                          <i class="material-symbols-rounded">delete</i>
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </main>

  <!-- Modal Ajout Événement -->
  <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addEventModalLabel">Ajouter un nouvel événement</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addEventForm" method="POST" enctype="multipart/form-data" action="addEvenement.php">
          <div class="modal-body">
            <div class="mb-3">
              <label for="add_titre" class="form-label">Titre *</label>
              <input type="text" class="form-control" id="add_titre" name="titre" required>
              <div id="add_titre_error" class="error-message"></div>
            </div>
            
            <div class="mb-3">
              <label for="add_description" class="form-label">Description *</label>
              <textarea class="form-control" id="add_description" name="description" rows="3" required></textarea>
              <div id="add_description_error" class="error-message"></div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="add_date" class="form-label">Date *</label>
                <input type="datetime-local" class="form-control" id="add_date" name="date_evenement" required>
                <div id="add_date_error" class="error-message"></div>
              </div>
              
              <div class="col-md-6 mb-3">
                <label for="add_lieu" class="form-label">Lieu *</label>
                <input type="text" class="form-control" id="add_lieu" name="lieu" required>
                <div id="add_lieu_error" class="error-message"></div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="add_type" class="form-label">Type d'événement *</label>
              <select class="form-select" id="add_type" name="type_evenement" required>
                <option value="">Sélectionnez un type</option>
                <option value="Conférence">Conférence</option>
                <option value="Atelier">Atelier</option>
                <option value="Formation">Formation</option>
                <option value="Social">Événement social</option>
                <option value="Sportif">Événement sportif</option>
              </select>
              <div id="add_type_error" class="error-message"></div>
            </div>
            
            <div class="mb-3">
              <label for="add_image" class="form-label">Image *</label>
              <input type="file" class="form-control" id="add_image" name="image" accept="image/*" required>
              <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF (max 2MB)</div>
              <div id="add_image_error" class="error-message"></div>
              <img id="add_image_preview" class="image-preview mt-2" src="#" alt="Aperçu de l'image" style="display:none;">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Modification Événement -->
  <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editEventModalLabel">Modifier l'événement</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editEventForm" method="POST" enctype="multipart/form-data" action="update_evenement.php">
        <input type="hidden" id="edit_id" name="id" value="<?= isset($evenement['id']) ? htmlspecialchars($evenement['id']) : '' ?>">        <div class="modal-body">
            <div class="mb-3">
              <label for="edit_titre" class="form-label">Titre *</label>
              <input type="text" class="form-control" id="edit_titre" name="titre" required>
              <div id="edit_titre_error" class="error-message"></div>
            </div>
            
            <div class="mb-3">
              <label for="edit_description" class="form-label">Description *</label>
              <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
              <div id="edit_description_error" class="error-message"></div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="edit_date" class="form-label">Date *</label>
                <input type="datetime-local" class="form-control" id="edit_date" name="date_evenement" required>
                <div id="edit_date_error" class="error-message"></div>
              </div>
              
              <div class="col-md-6 mb-3">
                <label for="edit_lieu" class="form-label">Lieu *</label>
                <input type="text" class="form-control" id="edit_lieu" name="lieu" required>
                <div id="edit_lieu_error" class="error-message"></div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="edit_type" class="form-label">Type d'événement *</label>
              <select class="form-select" id="edit_type" name="type_evenement" required>
                <option value="">Sélectionnez un type</option>
                <option value="Conférence">Conférence</option>
                <option value="Atelier">Atelier</option>
                <option value="Formation">Formation</option>
                <option value="Social">Événement social</option>
                <option value="Sportif">Événement sportif</option>
              </select>
              <div id="edit_type_error" class="error-message"></div>
            </div>
            
            <div class="mb-3">
              <label for="edit_image" class="form-label">Image</label>
              <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
              <div class="file-info">Laisser vide pour conserver l'image actuelle</div>
              <div id="edit_image_error" class="error-message"></div>
              <img id="edit_image_preview" class="image-preview mt-2" src="#" alt="Aperçu de l'image">
              <input type="hidden" id="edit_current_image" name="current_image">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
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
  <!-- Scripts -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    // Initialisation du scrollbar
    if (document.querySelector('#sidenav-scrollbar')) {
      new PerfectScrollbar('#sidenav-scrollbar', {
        wheelSpeed: 0.5,
        suppressScrollX: true
      });
    }

    // Recherche en temps réel
    document.getElementById('searchInput').addEventListener('input', function() {
      const input = this.value.toLowerCase();
      const rows = document.querySelectorAll('#eventsTable tbody tr');
      
      rows.forEach(row => {
        const title = row.querySelector('td:first-child h6').textContent.toLowerCase();
        const description = row.querySelector('td:nth-child(3) p').textContent.toLowerCase();
        const location = row.querySelector('td:nth-child(5) span').textContent.toLowerCase();
        const type = row.querySelector('td:nth-child(6) span').textContent.toLowerCase();
        
        if (title.includes(input) || description.includes(input) || location.includes(input) || type.includes(input)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // Gestion des aperçus d'image
    function handleImagePreview(inputId, previewId) {
      const input = document.getElementById(inputId);
      const preview = document.getElementById(previewId);
      
      input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else if (inputId === 'edit_image') {
          preview.style.display = 'block';
        } else {
          preview.style.display = 'none';
        }
      });
    }

    handleImagePreview('add_image', 'add_image_preview');
    handleImagePreview('edit_image', 'edit_image_preview');

    // Remplir le formulaire de modification
    document.querySelectorAll('.edit-event').forEach(button => {
      button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        if (!id) {
            alert('ID de l\'événement non trouvé');
            return;
        }
        const titre = this.getAttribute('data-titre');
        const description = this.getAttribute('data-description');
        const date = this.getAttribute('data-date');
        const lieu = this.getAttribute('data-lieu');
        const type = this.getAttribute('data-type');
        const image = this.getAttribute('data-image');
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_titre').value = titre;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_date').value = formatDateTimeForInput(date);
        document.getElementById('edit_lieu').value = lieu;
        document.getElementById('edit_type').value = type;
        
        const preview = document.getElementById('edit_image_preview');
        if (image) {
          preview.src = image;
          preview.style.display = 'block';
          document.getElementById('edit_current_image').value = image.split('/').pop();
        } else {
          preview.style.display = 'none';
          document.getElementById('edit_current_image').value = '';
        }
      });
    });

    // Formatage de la date pour l'input datetime-local
    function formatDateTimeForInput(dateString) {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return dateString;
      
      const pad = num => num.toString().padStart(2, '0');
      
      return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

// Gestion de la suppression
let eventToDelete = null;
    
    document.querySelectorAll('.delete-event').forEach(button => {
      button.addEventListener('click', function() {
        eventToDelete = this.getAttribute('data-id');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
      });
    });
    
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
      if (eventToDelete) {
        window.location.href = `delete_evenement.php?id=${eventToDelete}`;
      }
    });

    // Validation des formulaires
    function validateForm(formId) {
      const form = document.getElementById(formId);
      let isValid = true;
      
      // Réinitialiser les erreurs
      form.querySelectorAll('.error-message').forEach(el => {
        el.textContent = '';
      });
      form.querySelectorAll('.form-control').forEach(el => {
        el.classList.remove('input-error');
      });
      
      // Validation des champs requis
      form.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) {
          const errorId = `${input.id}_error`;
          const errorElement = document.getElementById(errorId);
          if (errorElement) {
            errorElement.textContent = 'Ce champ est requis';
          }
          input.classList.add('input-error');
          isValid = false;
        }
      });
      
      // Validation spécifique pour les images
      const imageInput = form.querySelector('input[type="file"]');
      if (imageInput && imageInput.hasAttribute('required') && (!imageInput.files || imageInput.files.length === 0)) {
        const errorId = `${imageInput.id}_error`;
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
          errorElement.textContent = 'Une image est requise';
        }
        imageInput.classList.add('input-error');
        isValid = false;
      }
      
      // Validation de la taille et type de fichier
      if (imageInput && imageInput.files && imageInput.files.length > 0) {
        const file = imageInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!allowedTypes.includes(file.type)) {
          const errorId = `${imageInput.id}_error`;
          const errorElement = document.getElementById(errorId);
          if (errorElement) {
            errorElement.textContent = 'Format d\'image non valide (JPG, JPEG, PNG, GIF seulement)';
          }
          imageInput.classList.add('input-error');
          isValid = false;
        }
        
        if (file.size > maxSize) {
          const errorId = `${imageInput.id}_error`;
          const errorElement = document.getElementById(errorId);
          if (errorElement) {
            errorElement.textContent = 'L\'image ne doit pas dépasser 2MB';
          }
          imageInput.classList.add('input-error');
          isValid = false;
        }
      }
      
      return isValid;
    }

    document.getElementById('addEventForm').addEventListener('submit', function(e) {
      if (!validateForm('addEventForm')) {
        e.preventDefault();
      }
    });

    document.getElementById('editEventForm').addEventListener('submit', function(e) {
      if (!validateForm('editEventForm')) {
        e.preventDefault();
      }
    });
    
    
    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap5',
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?= json_encode($controller->getEventsForCalendar()) ?>,
        eventClick: function(info) {
            // Empêche le comportement par défaut (ouvrir dans la même fenêtre)
            info.jsEvent.preventDefault();
            
            // Ouvre le lien dans un nouvel onglet
            window.open(info.event.url, '_blank');
        },
        eventDidMount: function(info) {
            // Style des événements
            if (info.event.start < new Date()) {
                info.el.style.opacity = '0.7';
                info.el.style.borderLeft = '4px solid #6c757d';
            } else {
                info.el.style.borderLeft = '4px solid #28a745';
            }
            
            // Ajoute un effet au survol
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
});
 
  
</script>
</body>
</html>