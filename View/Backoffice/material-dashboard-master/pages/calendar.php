<?php
session_start();
require_once '../../../../Controller/evenementcontroller.php';
require_once '../../../../Controller/commandecont.php';
require_once '../../../../Controller/participationcontroller.php';

// Debug - Afficher les erreurs PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

$eventC = new EvenementController();
$livraisonC = new CommandeCont();
$participationC = new ParticipationController();

try {
    // Récupérer tous les événements
    $events = $eventC->listeEvenement();
    
    // Debug - Vérifier les événements
    echo "<!-- Debug - Nombre d'événements: " . count($events) . " -->\n";
    
    // Formater les données pour le calendrier
    $calendarEvents = [];
    
    // Ajouter les événements
    foreach ($events as $event) {
        try {
            $eventDate = new DateTime($event['date_evenement']);
            $calendarEvents[] = [
                'title' => $event['titre'],
                'start' => $eventDate->format('Y-m-d H:i:s'),
                'allDay' => false,
                'color' => '#4CAF50',
                'textColor' => '#ffffff',
                'description' => '<strong>Type:</strong> ' . $event['type_evenement'] . 
                               '<br><strong>Date:</strong> ' . $eventDate->format('d/m/Y H:i') .
                               '<br><strong>Lieu:</strong> ' . $event['lieu'] . 
                               '<br><strong>Description:</strong> ' . $event['description']
            ];
        } catch (Exception $e) {
            echo "<!-- Error processing event: " . $e->getMessage() . " -->\n";
        }
    }
    
    // Ajouter les livraisons
    $livraisons = $livraisonC->afficherCommande();
    foreach ($livraisons as $livraison) {
        try {
            $livraisonDate = new DateTime($livraison['date_livraison']);
            $calendarEvents[] = [
                'title' => 'Livraison #' . $livraison['id'],
                'start' => $livraisonDate->format('Y-m-d H:i:s'),
                'allDay' => false,
                'color' => '#2196F3',
                'textColor' => '#ffffff',
                'description' => '<strong>Adresse:</strong> ' . $livraison['adresse_livraison'] . 
                               '<br><strong>État:</strong> ' . $livraison['etat_livraison']
            ];
        } catch (Exception $e) {
            echo "<!-- Error processing livraison: " . $e->getMessage() . " -->\n";
        }
    }
    
} catch (Exception $e) {
    echo "<!-- Error: " . $e->getMessage() . " -->\n";
    $calendarEvents = [];
}

// Debug - Afficher les événements formatés
echo "<!-- Calendar Events: " . json_encode($calendarEvents) . " -->\n";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier - Administration</title>
    
    <!-- Material Dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/material-dashboard.css">
    
    <!-- FullCalendar CSS -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css' rel='stylesheet' />
    
    <style>
        .calendar-container {
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
            margin: 20px;
        }
        
        .fc-event {
            cursor: pointer;
            padding: 5px;
            margin: 2px 0;
            border-radius: 4px;
        }
        
        .event-details {
            padding: 20px;
            background: white;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            border-radius: 4px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        
        .formation-color { background: #4CAF50; }
        .conference-color { background: #9C27B0; }
        .workshop-color { background: #FF9800; }
        .seminaire-color { background: #E91E63; }
        .autre-color { background: #795548; }
        .livraison-color { background: #2196F3; }
        .inscription-color { background: #3F51B5; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="calendar-container">
            <h2>Calendrier des Événements et Livraisons</h2>
            
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color formation-color"></div>
                    <span>Formation</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color conference-color"></div>
                    <span>Conférence</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color workshop-color"></div>
                    <span>Workshop</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color seminaire-color"></div>
                    <span>Séminaire</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color autre-color"></div>
                    <span>Autre</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color livraison-color"></div>
                    <span>Livraison</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color inscription-color"></div>
                    <span>Inscription</span>
                </div>
            </div>
            
            <div id="calendar"></div>
            
            <div id="eventDetails" class="event-details" style="display: none;">
                <h4 id="detailTitle"></h4>
                <div id="detailDescription"></div>
                <button class="btn btn-primary mt-3" onclick="hideDetails()">Fermer</button>
            </div>
        </div>
    </div>
    
    <!-- FullCalendar JS -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/locales/fr.js'></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: <?php echo json_encode($calendarEvents); ?>,
            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            allDaySlot: false,
            displayEventTime: true,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            eventClick: function(info) {
                showDetails(info.event);
            },
            eventDidMount: function(info) {
                info.el.title = info.event.title;
            }
        });
        
        calendar.render();
    });
    
    function showDetails(event) {
        document.getElementById('detailTitle').textContent = event.title;
        document.getElementById('detailDescription').innerHTML = event.extendedProps.description;
        document.getElementById('eventDetails').style.display = 'block';
    }
    
    function hideDetails() {
        document.getElementById('eventDetails').style.display = 'none';
    }
    </script>
</body>
</html> 