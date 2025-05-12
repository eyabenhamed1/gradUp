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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Livraisons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        #calendar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .fc-event {
            cursor: pointer;
        }
        .fc-toolbar-title {
            color: #344767;
            font-weight: 600;
        }
        .fc-button {
            background-color: #e9ecef !important;
            border: none !important;
            color: #344767 !important;
        }
        .fc-button-active {
            background-color: #344767 !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Calendrier des Livraisons</h3>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const events = [
                <?php foreach($datesLivraison as $date): ?>
                {
                    title: '<?= $date['nb_commandes'] ?> livraison(s)',
                    start: '<?= $date['date_livraison'] ?>',
                    color: '#2ecc71',
                    textColor: 'white',
                    url: 'commande.php?date=<?= $date['date_livraison'] ?>'
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
                events: events
            });
            
            calendar.render();
        });
    </script>
</body>
</html>