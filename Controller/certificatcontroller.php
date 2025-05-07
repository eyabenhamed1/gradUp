<?php
require_once(__DIR__ . "/../Config.php");
require_once(__DIR__ . "/../Model/certificat.php");

class certificatController {

// Dans votre contrôleur (par exemple CertificatController.php)
public function index() {
    $model = new CertificatModel();
    $data['certificat'] = $model->getAllCertificats(); // Doit retourner un tableau
    $this->view('Frontoffice/index', $data);
}
    
    
    // Créer un certificat
    public function createCertificat($nom, $type, $objet,$date_demande,$status,$niveau, $image) {
        $db = config::getConnexion();
        try {
            // Ajout du placeholder :image dans la clause VALUES pour insérer l'image dans la BDD
            $query = $db->prepare("INSERT INTO certificat ( nom, type, objet,date_demande,status,niveau, image) 
                                   VALUES ( :nom, :type, :objet, :date_demande, :status, :niveau, :image)");
            
            $query->bindParam(':nom', $nom);
            $query->bindParam(':type', $type);
            $query->bindParam(':objet', $objet);
            $query->bindParam(':date_demande', $date_demande);
            $query->bindParam(':status', $status);
            $query->bindParam(':niveau', $niveau);
            $query->bindParam(':image', $image);
            $query->execute();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    public function afficherCertificats() {
        // Initialisation
        $certificatModel = new Certificat();
        
        // Récupération des données
        $certificats = $certificatModel->listeCertificat();
        
        // Debug (à commenter en production)
        echo '<pre>Nombre de certificats : ' . count($certificats) . '</pre>';
        
        // Passage à la vue
        $this->view('Frontoffice/index', [
            'certificats' => $certificats,
            'titre_page' => 'Liste des Certificats'
        ]);
    }

    public function listeCertificats() {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT * FROM certificat ORDER BY nom DESC");
            $query->execute();
            $certificats = $query->fetchAll(PDO::FETCH_ASSOC);
            return $certificats;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    // Lire la liste des certificat
    public function listeCertificat() {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT * FROM certificat ORDER BY nom DESC");
            $query->execute();
            $certificats = $query->fetchAll(PDO::FETCH_ASSOC);
            return $certificats;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    // Mettre à jour un certificat
    public function updateCertificat($id, $nom, $type, $objet, $date_demande,$status,$niveau, $image) {
        $db = config::getConnexion();
        try {
            // Dans la mise à jour, on met aussi à jour la colonne image
            $query = $db->prepare("UPDATE certificat 
                                   SET nom = :nom,  type = :type, objet = :objet, date_demande = :date_demande, status = :status,niveau =:niveau, image = :image
                                   WHERE id = :id");
            $query->bindParam(':id', $id);
            $query->bindParam(':nom', $nom);
            $query->bindParam(':type', $type);
            $query->bindParam(':objet', $objet);
            $query->bindParam(':date_demande', $date_demande);
            $query->bindParam(':status', $status);
            $query->bindParam(':niveau', $niveau);
            $query->bindParam(':image', $image);
            
            $query->execute();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // Supprimer un certificat
    public function deleteCertificat($id) {
        $db = config::getConnexion();
        try {
            // Supprimer d'abord le cadeau associé
            $query1 = $db->prepare("DELETE FROM cadeau WHERE id = :id");
            $query1->bindParam(':id', $id);
            $query1->execute();
    
            // Ensuite supprimer le certificat
            $query2 = $db->prepare("DELETE FROM certificat WHERE id = :id");
            $query2->bindParam(':id', $id);
            $query2->execute();
    
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
    

    // Lire un certificat par ID
    public function getCertificatById($id) {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT * FROM certificat WHERE id = :id");
            $query->bindParam(':id', $id);
            $query->execute();
            $certificat = $query->fetch(PDO::FETCH_ASSOC);
            return $certificat;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return null;
        }
    }
}
?>
