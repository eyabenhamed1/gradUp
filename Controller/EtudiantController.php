<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Etudiant.php');

class EtudiantController {
    private $etudiantModel;

    public function __construct() {
        $this->etudiantModel = new Etudiant();
    }

    public function connexion($email, $mot_de_passe) {
        return $this->etudiantModel->connexion($email, $mot_de_passe);
    }

    public function inscription($nom, $prenom, $email, $mot_de_passe) {
        // Vérifier si l'email existe déjà
        if ($this->etudiantModel->emailExiste($email)) {
            return "Cet email est déjà utilisé";
        }

        // Hacher le mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Créer l'étudiant
        $resultat = $this->etudiantModel->creerEtudiant($nom, $prenom, $email, $mot_de_passe_hash);

        if ($resultat) {
            return true;
        } else {
            return "Erreur lors de l'inscription";
        }
    }
}