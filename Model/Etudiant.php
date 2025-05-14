<?php
class Etudiant {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        if ($this->conn->connect_error) {
            die("Erreur de connexion: " . $this->conn->connect_error);
        }
    }

    public function connexion($email, $mot_de_passe) {
        $stmt = $this->conn->prepare("SELECT * FROM etudiant WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $etudiant = $result->fetch_assoc();
            
            if (password_verify($mot_de_passe, $etudiant['mot_de_passe'])) {
                return $etudiant;
            }
        }
        
        return false;
    }

    public function emailExiste($email) {
        $stmt = $this->conn->prepare("SELECT id_etudiant FROM etudiant WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }

    public function creerEtudiant($nom, $prenom, $email, $mot_de_passe_hash) {
        $stmt = $this->conn->prepare("INSERT INTO etudiant (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nom, $prenom, $email, $mot_de_passe_hash);
        
        return $stmt->execute();
    }
}