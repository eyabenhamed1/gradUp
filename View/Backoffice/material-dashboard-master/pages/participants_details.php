<?php
// Configuration et sécurité
$configPath = 'C:/xampp/htdocs/ProjetWeb2A/configg.php';
require_once $configPath;

if (!class_exists('config')) {
    die("Erreur de configuration : La classe config n'existe pas");
}

try {
    $pdo = config::getConnexion();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupération de l'ID événement
$eventId = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
if (!$eventId) {
    header("Location: evenement.php");
    exit();
}

// Récupération des données de l'événement
$stmt = $pdo->prepare("SELECT * FROM evenement WHERE id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    die("Cet événement n'existe pas");
}

// Récupération des participants
$participantsQuery = $pdo->prepare("
    SELECT 
        p.id_participation,
        p.statut,
        p.date_inscription,
        u.id,
        u.name,
        u.email,
        u.password
    FROM participation p
    INNER JOIN user u ON p.id_utilisateur = u.id
    WHERE p.id_evenement = ?
    ORDER BY p.date_inscription DESC
");
$participantsQuery->execute([$eventId]);
$participations = $participantsQuery->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Dans participants_details.php -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Détails des Participants</title>
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="../assets/css/material-dashboard.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-gradient-dark {
            background: linear-gradient(87deg, #172b4d 0, #1a174d 100%) !important;
        }
        .table-responsive {
            padding: 0 15px;
        }
        .back-button {
            margin-bottom: 20px;
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
            <span class="nav-link-text ms-1">Tableau de bord</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/tables.html">
            <span class="nav-link-text ms-1">Utilisateurs</span>
          </a>
        </li>
        <li class="nav-item">
        <a class="nav-link active bg-gradient-dark text-white" href="../pages/evenement.php">
            <span class="nav-link-text ms-1">Événements</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/participants_details.php">
            <span class="nav-link-text ms-1">Participations</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="evenement.php">Événements</a></li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Participants</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">Détails des Participants</h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Rechercher...</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item d-flex align-items-center">
                            <a href="../logout.php" class="nav-link text-body font-weight-bold px-0">
                                <i class="fa fa-user me-sm-1"></i>
                                <span class="d-sm-inline d-none">Déconnexion</span>
                            </a>
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-white text-capitalize ps-3">
                                        <i class="fas fa-users me-2"></i>
                                        Participants pour: <?= htmlspecialchars($event['titre']) ?> 
                                        (<?= count($participations) ?> participants)
                                    </h6>
                                    <a href="evenement.php" class="btn btn-light btn-sm me-3">
                                        <i class="fas fa-arrow-left me-1"></i> Retour
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-3">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Participant</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date Inscription</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($participations)): ?>
                                            <?php foreach ($participations as $part): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <?php if (!empty($part['photo'])): ?>
                                                                <img src="../uploads/<?= htmlspecialchars($part['photo']) ?>" 
                                                                     class="avatar avatar-sm me-3 border-radius-lg" alt="Photo profil">
                                                            <?php else: ?>
                                                                <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-dark">
                                                                    <i class="fas fa-user"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($part['name']) ?></h6>
                                                            <p class="text-xs text-secondary mb-0">ID: <?= $part['id'] ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($part['email']) ?></p>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm bg-gradient-<?= 
                                                        $part['statut'] === 'confirmé' ? 'success' : 
                                                        ($part['statut'] === 'annulé' ? 'danger' : 'warning') 
                                                    ?>">
                                                        <?= htmlspecialchars($part['statut']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <?= date('d/m/Y H:i', strtotime($part['date_inscription'])) ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
    <button class="btn btn-sm btn-warning edit-btn" 
            data-id="<?= $part['id_participation'] ?>"
            data-bs-toggle="modal" 
            data-bs-target="#editParticipationModal">
        <i class="fas fa-edit"></i>
    </button>
    
    <a href="delete_participation.php?id=<?= $part['id_participation'] ?>" 
       class="btn btn-sm btn-danger"
       onclick="return confirm('Confirmer la suppression?')">
       <i class="fas fa-trash"></i>
    </a>
</td>
                                                
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="alert alert-light text-dark" role="alert">
                                                        <i class="fas fa-info-circle me-2"></i> Aucun participant trouvé pour cet événement
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton pour ouvrir le modal -->
                    <button class="btn bg-gradient-dark mb-4" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                        <i class="fas fa-user-plus me-2"></i> Ajouter un participant
                    </button>

                    <!-- Modal d'ajout -->
                    <div class="modal fade" id="addParticipantModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-gradient-dark text-white">
                                    <h5 class="modal-title">Ajouter un participant</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addParticipantForm" method="post">
                                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nom complet</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Téléphone</label>
                                                <input type="tel" name="telephone" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Mot de passe</label>
                                                <input type="password" name="mot_de_passe" class="form-control" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Statut</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="confirmé">Confirmé</option>
                                                    <option value="en attente">En attente</option>
                                                    <option value="annulé">Annulé</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Commentaire</label>
                                            <textarea name="commentaire" class="form-control" rows="3"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" form="addParticipantForm" class="btn bg-gradient-dark">
                                        <i class="fas fa-save me-2"></i> Enregistrer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal d'édition -->
    <div class="modal fade" id="editParticipationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-dark text-white">
                    <h5 class="modal-title">Modifier Participation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="editParticipationModalBody">
                    <!-- Le contenu sera chargé ici via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/chartjs.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Configuration de la sidebar
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        // Gérer la soumission du formulaire d'ajout
        $('#addParticipantForm').on('submit', function(e) {
            e.preventDefault();
            
            // Désactiver le bouton pendant la soumission
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            
            $.ajax({
                url: 'add_participation.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#addParticipantModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Erreur: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('XHR:', xhr);
                    console.log('Status:', status);
                    console.log('Error:', error);
                    
                    let errorMessage = 'Une erreur est survenue';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert('Erreur lors de l\'ajout: ' + errorMessage);
                },
                complete: function() {
                    // Réactiver le bouton
                    submitBtn.prop('disabled', false);
                }
            });
        });
        
        // Réinitialiser le formulaire quand le modal se ferme
        $('#addParticipantModal').on('hidden.bs.modal', function () {
            $('#addParticipantForm')[0].reset();
        });

        // Gérer le clic sur le bouton d'édition
        $('.edit-btn').click(function() {
            var participationId = $(this).data('id');
            
            // Charger le formulaire dans le modal
            $('#editParticipationModalBody').load(
                'edit_participation_modal.php?id=' + participationId,
                function(response, status, xhr) {
                    if (status == "error") {
                        alert("Erreur lors du chargement du formulaire d'édition: " + xhr.status + " " + xhr.statusText);
                    }
                }
            );
        });
    });
    </script>
</body>
</html>