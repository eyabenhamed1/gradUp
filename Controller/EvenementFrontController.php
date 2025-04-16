<?php
// frontoffice/controller/EvenementFrontController.php

require_once(__DIR__ . "/../../controller/evenementcontroller.php");

class EvenementFrontController {
    private $evenementController;
    
    public function __construct() {
        $this->evenementController = new evenementController();
    }
    
    public function getAllEvenements() {
        return $this->evenementController->getAllEvenements();
    }
    
    public function getEvenementById($id) {
        return $this->evenementController->getevenementById($id);
    }
    
    public function getUpcomingEvenements($limit = 3) {
        return $this->evenementController->getUpcomingEvenements($limit);
    }
}
?>