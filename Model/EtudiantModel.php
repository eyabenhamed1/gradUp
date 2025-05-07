<?php
class EtudiantModel {
    private $db;
    
    public function __construct() {
        $this->db = DB::getConnexion();
    }
    
    public function getEtudiant($id_etudiant) {
        $sql = "SELECT id_etudiant, nom, prenom, email FROM etudiant WHERE id_etudiant = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_etudiant]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}