<?php
require_once(__DIR__ . '/../Model/EtudiantModel.php');

class EtudiantController {
    private $etudiantModel;
    
    public function __construct() {
        $this->etudiantModel = new EtudiantModel();
    }
    
    public function getEtudiantById($id_etudiant) {
        if (!is_numeric($id_etudiant)) {
            throw new InvalidArgumentException('ID étudiant doit être numérique');
        }
        
        return $this->etudiantModel->getEtudiant($id_etudiant);
    }
}