<?php
require_once(__DIR__.'/../Model/EvenementModel.php');
require_once(__DIR__.'/../Model/ParticipationModel.php'); // Ajout pour la gestion des participations

class EvenementController {
    private $evenementModel;
    private $participationModel; // Nouvelle propriété

    public function __construct() {
        $this->evenementModel = new EvenementModel();
        $this->participationModel = new ParticipationModel(); // Initialisation
    }

    public function getPlacesDisponibles($eventId) {
        $event = $this->evenementModel->getEvenementById($eventId);
        
        if (!$event || !$event['places_limitees']) {
            return 999;
        }

        $participations = $this->participationModel->getNombreParticipations($eventId);
        return max(0, $event['places_max'] - $participations);
    }

    public function listeEvenement() {
        return $this->evenementModel->getAllEvenements();
    }

    public function getEvenementById($id) {
        return $this->evenementModel->getEvenementById($id);
    }

    // Nouvelle méthode pour vérifier si un événement est complet
    public function estComplet($eventId) {
        return $this->getPlacesDisponibles($eventId) <= 0;
    }
}