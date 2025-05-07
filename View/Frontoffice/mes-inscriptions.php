<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/projettt/projettt/ProjetWeb2A/Controller/ParticipationController.php');

if (!isset($_SESSION['id_etudiant'])) {
    header("Location: connexion.php?error=session_expired");
    exit();
}

$participationController = new ParticipationController();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'create') {
                $data = [
                    'id_etudiant' => $_SESSION['id_etudiant'],
                    'id_evenement' => $_POST['id_evenement'],
                    'statut' => $_POST['statut']
                ];
                $result = $participationController->createParticipation($data);
                $_SESSION['message'] = "Inscription créée avec succès";
            } elseif ($_POST['action'] === 'update') {
                $data = [
                    'statut' => $_POST['statut']
                ];
                $result = $participationController->updateParticipation($_POST['id_participation'], $data);
                $_SESSION['message'] = "Inscription mise à jour avec succès";
            }
            
            $_SESSION['message_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }
        
        header("Location: mes-inscriptions.php");
        exit();
    }
} elseif (isset($_GET['action']) && $_GET['action'] == 'annuler' && isset($_GET['id'])) {
    $id_participation = (int)$_GET['id'];
    
    try {
        $result = $participationController->annulerParticipationByUserAndEvent(
            $_SESSION['id_etudiant'], 
            $id_participation
        );
        
        if ($result) {
            $_SESSION['message'] = "Inscription annulée avec succès";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de l'annulation";
            $_SESSION['message_type'] = "danger";
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: mes-inscriptions.php");
    exit();
}

// Récupérer les inscriptions
try {
    $inscriptions = $participationController->getParticipationsByStudentId($_SESSION['id_etudiant']);
    $availableEvents = $participationController->getAvailableEvents($_SESSION['id_etudiant']);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Inscriptions - Plateforme Éducative</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        /* Header */
        .header {
            background-color: var(--white);
            color: var(--dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        /* Navigation */
        .nav {
            display: flex;
            align-items: center;
        }
        
        .nav-list {
            display: flex;
            list-style: none;
        }
        
        .nav-item {
            margin-left: 1.5rem;
            position: relative;
        }
        
        .nav-link {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
        }
        
        .nav-link:hover {
            color: var(--accent);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent);
            transition: var(--transition);
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .nav-icons {
            display: flex;
            align-items: center;
            margin-left: 2rem;
        }
        
        .nav-icon {
            color: var(--dark);
            font-size: 1.2rem;
            margin-left: 1.2rem;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            text-decoration: none;
        }
        
        .nav-icon:hover {
            color: var(--accent);
            transform: translateY(-2px);
        }
        
        /* Main Content */
        .main {
            padding: 2rem 0;
            min-height: calc(100vh - 120px);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .page-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 0.5rem;
            text-align: center;
            font-weight: 600;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--accent);
            border-radius: 2px;
        }
        
        /* Table Styles */
        .inscriptions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        
        .inscriptions-table th, 
        .inscriptions-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .inscriptions-table th {
            background-color: var(--primary);
            color: var(--white);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        .inscriptions-table tr:nth-child(even) {
            background-color: rgba(236, 240, 241, 0.5);
        }
        
        .inscriptions-table tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .btn-view {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-view:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-cancel {
            background-color: var(--accent);
            color: white;
        }
        
        .btn-cancel:hover {
            background-color: #c0392b;
        }
        
        .btn-disabled {
            background-color: var(--medium-gray);
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: var(--medium-gray);
            font-style: italic;
        }
        
        .event-date {
            font-weight: 600;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-en_attente {
            background-color: #f39c12;
            color: white;
        }
        
        .status-confirme {
            background-color: #2ecc71;
            color: white;
        }
        .status-confirmé {  /* avec accent */
    background-color: #2ecc71;
    color: white;
}

/* Styles pour les notifications toast */
.toast {
    min-width: 350px;
    max-width: 100%;
    font-size: 0.875rem;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    opacity: 1;
    transition: opacity 0.3s ease;
}

.toast.hide {
    opacity: 0;
}

.toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1100;
}

/* Animation d'entrée */
@keyframes slideIn {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(0);
    }
}

/* Animation de sortie */
@keyframes slideOut {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(100%);
    }
}

.toast.show {
    animation: slideIn 0.3s forwards;
}

.toast.hide {
    animation: slideOut 0.3s forwards;
}
        .status-annule {
            background-color: #e74c3c;
            color: white;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark);
            color: var(--white);
            padding: 3rem 0 1.5rem;
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
            .header-container {
                flex-direction: column;
                padding: 1rem;
            }
            
            .nav {
                width: 100%;
                margin-top: 1rem;
                justify-content: space-between;
            }
            
            .nav-list {
                display: none;
            }
            
            .nav-icons {
                margin-left: auto;
            }
            
            .inscriptions-table {
                display: block;
                overflow-x: auto;
            }
            
            .footer-container {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn {
                width: 100%;
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
        }
        /* Correction pour le backdrop */
.modal-backdrop.fade.show {
    z-index: 1040; /* Doit être inférieur à la z-index de la modale */
}

.modal {
    z-index: 1050;
}

/* Empêche le body de devenir non-scrollable */
body.modal-open {
    overflow: auto;
    padding-right: 0 !important;
}
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .add-participation-btn {
            margin-bottom: 20px;
        }
/* Style des notifications */
.notification-dropdown {
    width: 350px;
    padding: 0;
}

.notification-container {
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    transition: all 0.3s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-time {
    font-size: 12px;
    color: #6c757d;
}

.notification-counter {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 10px;
}
/* Adaptation pour le header */
.nav-item.dropdown {
    position: relative;
    list-style: none;
}

.nav-link.dropdown-toggle {
    display: flex;
    align-items: center;
    position: relative;
    padding: 0.5rem 1rem;
}

 /* CSS pour les toasts (popups) */
 .notification-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 10000;
        }

        .notification-toast.show {
            opacity: 1;
        }

        .toast-header {
            padding: 10px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 5px 5px 0 0;
        }

        .toast-body {
            padding: 15px;
        }
    </style>
</head>
<body>
<header class="header">
    <div class="header-container">
        <a href="index.php" class="logo">
            <i class="fas fa-graduation-cap"></i>
            Plateforme Éducative
        </a>
        
        <nav class="nav">
            <ul class="nav-list">
                <li class="nav-item"><a href="index.php" class="nav-link">Accueil</a></li>
                <li class="nav-item"><a href="evenements.php" class="nav-link">Événements</a></li>
                <li class="nav-item"><a href="mes-inscriptions.php" class="nav-link active">Mes Inscriptions</a></li>
                <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
                
                <!-- Ajoutez ici le dropdown des notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <?php 
                        $upcomingCount = count($participationController->getUpcomingEventsReminders($_SESSION['id_etudiant']));
                        if ($upcomingCount > 0): ?>
                            <span class="badge bg-danger notification-counter" style="position: absolute; top: -5px; right: -5px; font-size: 10px;">
                                <?= $upcomingCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="navbarDropdown">
                        <li><h6 class="dropdown-header">Vos rappels</h6></li>
                        <div class="notification-container">
                            <!-- Les notifications seront chargées ici via JavaScript -->
                            <div class="text-center py-2">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="reminders.php">Voir tous les rappels</a></li>
                    </ul>
                </li>
            </ul>
            
            <div class="nav-icons">
                <a href="deconnexion.php" class="nav-icon" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </nav>
    </div>
</header>
    <main class="main">
        <div class="container">
            <h1 class="page-title">Mes Inscriptions aux Événements</h1>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                    <?= $_SESSION['message'] ?>
                </div>
                <?php 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            <?php endif; ?>
            
            <table class="inscriptions-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Événement</th>
                        <th>Date</th>
                        <th>Lieu</th>
                        <th>Date Inscription</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscriptions as $inscription): ?>
                        <tr>
                            <td><?= $inscription['id_participation'] ?></td>
                            <td><?= htmlspecialchars($inscription['titre'] ?? 'Événement inconnu') ?></td>
                            <td class="event-date"><?= date('d/m/Y', strtotime($inscription['date_evenement'])) ?></td>
                            <td><?= htmlspecialchars($inscription['lieu'] ?? 'Non spécifié') ?></td>
                            <td><?= date('d/m/Y', strtotime($inscription['date_inscription'])) ?></td>
                            <td>
                            <span class="status-badge status-<?= strtolower(str_replace(['é', 'ê', 'è', 'ë'], 'e', $inscription['statut'])) ?>">                                    <?= ucfirst(str_replace('_', ' ', $inscription['statut'])) ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <button class="btn btn-primary edit-btn" 
                                    data-id="<?= $inscription['id_participation'] ?>"
                                    data-evento='<?= htmlspecialchars(json_encode($inscription), ENT_QUOTES, 'UTF-8') ?>'>
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <a href="mes-inscriptions.php?action=annuler&id=<?= $inscription['id_participation'] ?>" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir annuler cette inscription ?')"
                                   class="btn btn-cancel">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

   <!-- Modal -->
   <div class="modal fade" id="participationModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Titre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form id="participationForm" method="POST">
                    <input type="hidden" name="id_participation" id="id_participation">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select" id="statut" name="statut" required>
                            <option value="confirme">Confirmé</option>
                        </select>
                    </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>

                </form>            
            </div>
            
        </div>
    </div>
</div>
<div id="personalCalendar"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    
// Votre code JavaScript ici
$(document).ready(function() {
    // Initialisation de la modale
    var participationModal = new bootstrap.Modal(document.getElementById('participationModal'));

    // Fonction pour ouvrir la modale
    function openModal(title, action, data = null) {
        $('#modalTitle').text(title);
        $('#formAction').val(action);
        
        if (data) {
            $('#id_participation').val(data.id_participation || '');
            $('#id_evenement').val(data.id_evenement || '');
            $('#statut').val(data.statut || 'en_attente');
        }
        
        participationModal.show();
    }

    // Gestionnaire pour le bouton d'ajout
    $('#addParticipationBtn').click(function() {
        $('#participationForm')[0].reset();
        openModal("Nouvelle Inscription", "create");
    });

    // Gestionnaire pour les boutons d'édition
    $(document).on('click', '.edit-btn', function() {
        try {
            const inscriptionData = JSON.parse($(this).attr('data-evento'));
            openModal("Modifier Inscription", "update", inscriptionData);
        } catch (error) {
            console.error("Erreur:", error);
            alert("Erreur lors du chargement des données");
        }
    });

    // Gestionnaire de soumission du formulaire
    $('#participationForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'mes-inscriptions.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                participationModal.hide();
                window.location.reload();
            },
            error: function(xhr, status, error) {
                alert("Erreur: " + error);
                participationModal.hide();
            }
        });
    });

    // Nettoyage au cas où
    $(document).on('hidden.bs.modal', '#participationModal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });
});
</script>
<script>
// Calendrier des événements auxquels l'utilisateur est inscrit
var personalCalendar = new FullCalendar.Calendar(document.getElementById('personalCalendar'), {
    initialView: 'dayGridMonth',
    events: [
        <?php foreach($userEvents as $event): ?>
        {
            id: '<?= $event['id'] ?>',
            title: '<?= addslashes($event['titre']) ?>',
            start: '<?= $event['date_evenement'] ?>',
            url: 'evenement-details.php?id=<?= $event['id'] ?>'
        },
        <?php endforeach; ?>
    ],
    eventClick: function(info) {
        info.jsEvent.preventDefault();
        window.location.href = info.event.url;
    }
});
personalCalendar.render();

//////
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour charger les notifications
    function loadNotifications() {
            fetch('./get_notification.php')  // "./" pour le même dossier
            .then(response => response.json())
            .then(data => {
                const container = document.querySelector('.notification-container');
                const counter = document.querySelector('.notification-counter');
                
                container.innerHTML = '';
                counter.textContent = data.length;
                
                if(data.length === 0) {
                    container.innerHTML = '<li class="notification-item text-center py-3">Aucun rappel pour le moment</li>';
                    return;
                }
                
                data.forEach(notification => {
                    const item = document.createElement('li');
                    item.className = `notification-item ${notification.read ? '' : 'unread'}`;
                    item.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <strong>${notification.title}</strong>
                            <small class="notification-time">${notification.time}</small>
                        </div>
                        <div>${notification.message}</div>
                    `;
                    item.addEventListener('click', () => markAsRead(notification.id));
                    container.appendChild(item);
                });
            });
    }
    
    // Fonction pour marquer comme lu
    function markAsRead(id) {
        fetch('mark_as_read.php?id=' + id)
            .then(() => loadNotifications());
    }
    
    // Charger les notifications au démarrage
    loadNotifications();
    
    // Recharger toutes les 5 minutes
    setInterval(loadNotifications, 300000);
});
////////// Fonction pour afficher les popups
function showNewNotification(notification) {
    const toast = document.createElement('div');
    toast.className = 'notification-toast show';
    toast.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto">${notification.title}</strong>
            <small>${notification.time}</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">${notification.message}</div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Modifiez votre fonction loadNotifications existante
function loadNotifications() {
    console.log("Chargement des notifications...");
    
    fetch('./get_notification.php')
        .then(response => {
            console.log("Statut HTTP:", response.status);
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            console.log("Données reçues:", data);
            
            const container = document.querySelector('.notification-container');
            const counter = document.querySelector('.notification-counter');
            
            if (!data.success) {
                console.error("Erreur API:", data.error);
                container.innerHTML = '<li class="text-danger">Erreur de chargement</li>';
                return;
            }

            container.innerHTML = '';
            counter.textContent = data.notifications.length;
            
            if(data.notifications.length === 0) {
                container.innerHTML = '<li class="text-center py-3">Aucun rappel</li>';
                return;
            }
            
            // Afficher les popups
            data.notifications.filter(n => !n.read).forEach(showNewNotification);
            
            // Remplir le dropdown
            data.notifications.forEach(notif => {
                const item = document.createElement('li');
                item.className = 'notification-item';
                item.innerHTML = `
                    <div class="d-flex justify-content-between">
                        <strong>${notif.title}</strong>
                        <small>${notif.time}</small>
                    </div>
                    <div>${notif.message}</div>
                `;
                container.appendChild(item);
            });
        })
        .catch(error => {
            console.error("Erreur fetch:", error);
            document.querySelector('.notification-container').innerHTML = 
                '<li class="text-danger">Erreur de connexion</li>';
        });
}

// Appelez loadNotifications au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    
    // Optionnel: recharger périodiquement
    setInterval(loadNotifications, 300000); // Toutes les 5 minutes
});
////////////////
// Fonction pour afficher les notifications toast
function showNotificationToast(notification) {
    const toastContainer = document.createElement('div');
    toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
    toastContainer.style.zIndex = '1100';
    
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.role = 'alert';
    toast.ariaLive = 'assertive';
    toast.ariaAtomic = 'true';
    
    toast.innerHTML = `
        <div class="toast-header bg-primary text-white">
            <strong class="me-auto">${notification.title}</strong>
            <small class="text-white">${notification.time}</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-light">
            ${notification.message}
            <div class="mt-2 pt-2 border-top">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-dismiss="toast">
                    Fermer
                </button>
            </div>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    document.body.appendChild(toastContainer);
    
    // Fermeture automatique après 5 secondes
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toastContainer.remove(), 300);
    }, 5000);
}

// Fonction pour charger et afficher les notifications
function loadAndShowNotifications() {
    fetch('./get_notification.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.notifications.length > 0) {
                // Afficher chaque notification non lue
                data.notifications.forEach(notification => {
                    showNotificationToast(notification);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Charger et afficher les notifications immédiatement
    loadAndShowNotifications();
    
    // Vérifier les nouvelles notifications toutes les minutes
    setInterval(loadAndShowNotifications, 60000);
    
    // Initialiser les tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
</body>
</html>