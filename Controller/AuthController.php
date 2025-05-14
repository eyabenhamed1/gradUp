<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Etudiant.php');

class AuthController {
    private $etudiantModel;

    public function __construct() {
        $this->etudiantModel = new Etudiant();
    }

    public function login($email, $password) {
        $etudiant = $this->etudiantModel->login($email, $password);
        
        if ($etudiant) {
            $_SESSION['etudiant'] = $etudiant;
            return true;
        }
        return false;
    }


    

    public function logout() {
        unset($_SESSION['etudiant']);
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['etudiant']);
    }

    public function getCurrentUser() {
        return $_SESSION['etudiant'] ?? null;
    }
}
?>