<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../../Controller/ParticipationController.php');

$participationController = new ParticipationController();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'create') {
                $data = [
                    'id' => $_SESSION['user']['id'],
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
            $_SESSION['user']['id'], 
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
    $inscriptions = $participationController->getParticipationsByStudentId($_SESSION['user']['id']);
    $availableEvents = $participationController->getAvailableEvents($_SESSION['user']['id']);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
// Récupérer les rappels d'événements à venir
try {
    $upcomingEvents = $participationController->getUpcomingEventsReminders($_SESSION['user']['id']);
    $upcomingCount = count($upcomingEvents);
} catch (Exception $e) {
    $upcomingEvents = [];
    $upcomingCount = 0;
    // Vous pouvez logger l'erreur si nécessaire
    // error_log("Erreur lors de la récupération des rappels: " . $e->getMessage());
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

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-title {
        text-align: center;
        margin-bottom: 3rem;
        color: var(--dark);
        font-size: 2.5rem;
        font-weight: 600;
        position: relative;
    }

    .page-title:after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--primary);
        border-radius: 2px;
    }

    .inscriptions-table {
        width: 100%;
        background: var(--white);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-top: 2rem;
    }

    .inscriptions-table th {
        background-color: var(--primary);
        color: var(--white);
        padding: 1rem;
        text-align: left;
        font-weight: 500;
    }

    .inscriptions-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--light-gray);
        color: var(--dark);
    }

    .inscriptions-table tr:hover {
        background-color: rgba(236, 240, 241, 0.5);
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }

    .status-en_attente {
        background-color: #f1c40f;
        color: var(--white);
    }

    .status-confirme,
    .status-confirmé {
        background-color: #2ecc71;
        color: var(--white);
    }

    .status-annule {
        background-color: var(--accent);
        color: var(--white);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
        text-decoration: none;
    }

    .btn i {
        font-size: 1rem;
    }

    .btn-primary {
        background-color: var(--primary);
        color: var(--white);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-cancel {
        background-color: var(--accent);
        color: var(--white);
    }

    .btn-cancel:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .toast-container {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 1000;
    }

    .toast {
        background: var(--white);
        border-radius: 5px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        min-width: 300px;
    }

    .toast-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--light-gray);
    }

    .modal-content {
        background: var(--white);
        border-radius: 10px;
        padding: 2rem;
    }

    .modal-header {
        border-bottom: 1px solid var(--light-gray);
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--light-gray);
        border-radius: 5px;
        margin-bottom: 1rem;
        transition: var(--transition);
    }

    .form-control:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(44, 62, 80, 0.1);
    }

    @media (max-width: 768px) {
        .inscriptions-table {
            display: block;
            overflow-x: auto;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .page-title {
            font-size: 2rem;
        }
    }

    .logout-link {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        background-color: var(--accent);
        color: var(--white);
        text-decoration: none;
        border-radius: 4px;
        transition: var(--transition);
        font-weight: 500;
        margin-left: 20px;
    }

    .logout-link:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .logout-link i {
        margin-right: 8px;
        font-size: 1.1em;
    }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Gradup
            </a>
            
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="evenements.php" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            Événements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="mes-inscriptions.php" class="nav-link active">
                            <i class="fas fa-list-alt"></i>
                            Mes Inscriptions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-graduation-cap"></i>
                            E-learning
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="essaiee.php" class="nav-link">
                            <i class="fas fa-shopping-bag"></i>
                            Boutique
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-certificate"></i>
                            Certificat
                        </a>
                    </li>
                </ul>
                
                <div class="nav-icons">
                    <a href="profile.php" class="nav-link">
                        <i class="fas fa-user"></i>
                        Profil
                    </a>
                    <a href="auth/logout.php" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            </nav>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <h1 class="page-title">Mes Inscriptions aux Événements</h1>

            <!-- Toast pour les messages de succès -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-success text-white">
                            <strong class="me-auto">Succès</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            <?= htmlspecialchars($_SESSION['success_message']) ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-<?= $_SESSION['message_type'] ?? 'info' ?> text-white">
                            <strong class="me-auto"><?= ucfirst($_SESSION['message_type'] ?? 'Information') ?></strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            <?= htmlspecialchars($_SESSION['message']) ?>
                        </div>
                    </div>
                    <?php 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                <?php endif; ?>
            </div>
            
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
                <h5 class="modal-title" id="modalTitle">Modifier mes informations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form id="participationForm" method="POST">
                    <input type="hidden" name="id_participation" id="id_participation">
                    <input type="hidden" name="action" id="formAction" value="update">
                    
                    <!-- Événement (en lecture seule) -->
                    <div class="mb-3">
                        <label class="form-label">Événement</label>
                        <input type="text" class="form-control" id="event_title" readonly>
                    </div>

                    <!-- Nom -->
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="form-text">Pour recevoir les mises à jour concernant l'événement</div>
                    </div>

                    <!-- Téléphone -->
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone">
                        <div class="form-text">Pour vous contacter en cas de besoin</div>
                    </div>

                    <!-- Commentaire -->
                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" id="commentaire" name="commentaire" rows="3" 
                                placeholder="Vos attentes, questions ou besoins particuliers..."></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
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
            $('#event_title').val(data.titre || '');
            $('#nom').val(data.nom || '');
            $('#email').val(data.email || '');
            $('#telephone').val(data.telephone || '');
            $('#commentaire').val(data.commentaire || '');
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
            // Remplir le formulaire avec les données existantes
            $('#id_participation').val(inscriptionData.id_participation);
            $('#event_title').val(inscriptionData.titre);
            $('#nom').val(inscriptionData.nom_participant || '');
            $('#email').val(inscriptionData.email || '');
            $('#telephone').val(inscriptionData.telephone || '');
            $('#commentaire').val(inscriptionData.commentaire || '');
            
            // Afficher le modal
            new bootstrap.Modal(document.getElementById('participationModal')).show();
        } catch (error) {
            console.error("Erreur:", error);
            showToast("Erreur lors du chargement des données", "danger");
        }
    });

    // Fonction pour afficher les toasts
    function showToast(message, type = 'success') {
        const toastContainer = $('.toast-container');
        const toast = $(`
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">${type === 'success' ? 'Succès' : 'Erreur'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);
        
        toastContainer.append(toast);
        
        // Supprimer le toast après 5 secondes
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // Gestionnaire de soumission du formulaire
    $('#participationForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'update_participation.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Fermer le modal
                    bootstrap.Modal.getInstance(document.getElementById('participationModal')).hide();
                    
                    // Afficher le message de succès
                    showToast(response.message || "Mise à jour réussie");
                    
                    // Recharger la page après un court délai
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    // Afficher l'erreur dans un toast
                    showToast(response.message || "Une erreur est survenue", "danger");
                }
            },
            error: function(xhr) {
                let errorMessage = "Erreur lors de la communication avec le serveur";
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error("Erreur lors du parsing de la réponse:", e);
                }
                showToast(errorMessage, "danger");
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

///////
// Fonction pour charger et afficher les notifications
function loadAndDisplayNotifications() {
    fetch('./get_notification.php')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.getElementById('notificationsList');
            notificationsList.innerHTML = '';

            if (data.success && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    const notificationElement = document.createElement('div');
                    notificationElement.className = 'list-group-item list-group-item-action';
                    notificationElement.innerHTML = `
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">${notification.title}</h5>
                            <small>${notification.time}</small>
                        </div>
                        <p class="mb-1">${notification.message}</p>
                    `;
                    notificationsList.appendChild(notificationElement);
                });
            } else {
                notificationsList.innerHTML = `
                    <div class="list-group-item">
                        <p class="text-muted">Aucun rappel pour le moment</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('notificationsList').innerHTML = `
                <div class="list-group-item list-group-item-danger">
                    Erreur lors du chargement des rappels
                </div>
            `;
        });
}

// Bouton pour afficher/masquer les notifications
document.getElementById('toggleNotificationsBtn').addEventListener('click', function() {
    const section = document.getElementById('notificationsSection');
    if (section.style.display === 'none') {
        section.style.display = 'block';
        loadAndDisplayNotifications();
    } else {
        section.style.display = 'none';
    }
});

// Charger les notifications au démarrage si nécessaire
// loadAndDisplayNotifications();
</script>
<script>
    // Initialisation des toasts Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide des toasts après 5 secondes
        var toasts = document.querySelectorAll('.toast');
        toasts.forEach(function(toast) {
            // Créer une instance Toast de Bootstrap
            var bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: 5000
            });
            
            // S'assurer que le toast est affiché
            bsToast.show();
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestionnaire pour le formulaire de modification
        const forms = document.querySelectorAll('form[data-action="update"]');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'update');
                
                fetch('mes-inscriptions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.text();
                })
                .then(result => {
                    // Afficher un message de succès
                    const toast = document.createElement('div');
                    toast.className = 'toast show position-fixed bottom-0 end-0 m-3';
                    toast.innerHTML = `
                        <div class="toast-header bg-success text-white">
                            <strong class="me-auto">Succès</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            Modification enregistrée avec succès
                        </div>
                    `;
                    document.body.appendChild(toast);
                    
                    // Recharger la page après 1 seconde
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                })
                .catch(error => {
                    // Afficher un message d'erreur
                    const toast = document.createElement('div');
                    toast.className = 'toast show position-fixed bottom-0 end-0 m-3';
                    toast.innerHTML = `
                        <div class="toast-header bg-danger text-white">
                            <strong class="me-auto">Erreur</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            Une erreur est survenue lors de la modification
                        </div>
                    `;
                    document.body.appendChild(toast);
                });
            });
        });
        
        // Initialisation des toasts Bootstrap
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
        });
    });
</script>
</body>
</html>