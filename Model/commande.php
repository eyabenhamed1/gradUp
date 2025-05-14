<?php
class Commande {
    private $id_commande;
    private $nom;
    private $prenom;
    private $tlf;
    private $adresse;
    private $produits;
    private $prix_total;
    private $etat;
    private $date_livraison;
    private $id_user;

    // Getters
    public function getIdCommande() {
        return $this->id_commande;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getTlf() {
        return $this->tlf;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getProduits() {
        return $this->produits;
    }

    public function getPrixTotal() {
        return $this->prix_total;
    }

    public function getEtat() {
        return $this->etat;
    }

    public function getDatelivraison() {
        return $this->date_livraison;
    }

    public function getIdUser() {
        return $this->id_user;
    }

    // Setters
    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    public function setTlf($tlf) {
        $this->tlf = $tlf;
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

    public function setProduits($produits) {
        $this->produits = $produits;
    }

    public function setPrixTotal($prix_total) {
        $this->prix_total = $prix_total;
    }

    public function setEtat($etat) {
        $this->etat = $etat;
    }

    public function setIdUser($id_user) {
        $this->id_user = $id_user;
    }

    /**
     * Enregistre une nouvelle commande dans la base de données
     */
    public function save() {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        if ($conn->connect_error) {
            throw new Exception("Erreur de connexion: " . $conn->connect_error);
        }
        
        $stmt = $conn->prepare("INSERT INTO commande (nom, prenom, tlf, adresse, produits, prix_total, etat, id_user) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssdsi", 
            $this->nom, 
            $this->prenom, 
            $this->tlf, 
            $this->adresse, 
            $this->produits, 
            $this->prix_total, 
            $this->etat,
            $this->id_user);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur d'enregistrement: " . $stmt->error);
        }
        
        $this->id_commande = $conn->insert_id;
        $stmt->close();
        $conn->close();
        
        return $this->id_commande;
    }

    /**
     * Récupère une commande par son ID
     */
    public function getCommandeById($id) {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        $stmt = $conn->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }

    /**
     * Modifie une commande existante
     */
    public function modifierCommande($id, $data) {
        // Validation des données
        $errors = [];
        
        // Validation du nom
        if (empty($data['nom']) || !preg_match('/^[A-Za-zÀ-ÿ\s\-\']{2,50}$/', $data['nom'])) {
            $errors[] = "Le nom doit contenir entre 2 et 50 caractères alphabétiques";
        }
        
        // Validation du prénom
        if (empty($data['prenom']) || !preg_match('/^[A-Za-zÀ-ÿ\s\-\']{2,50}$/', $data['prenom'])) {
            $errors[] = "Le prénom doit contenir entre 2 et 50 caractères alphabétiques";
        }
        
        // Validation du téléphone
        if (empty($data['tlf']) || !preg_match('/^[0-9]{8,15}$/', $data['tlf'])) {
            $errors[] = "Le téléphone doit contenir entre 8 et 15 chiffres";
        }
        
        // Validation de l'adresse
        if (empty($data['adresse']) || strlen($data['adresse']) < 10 || strlen($data['adresse']) > 255) {
            $errors[] = "L'adresse doit contenir entre 10 et 255 caractères";
        }
        
        // Validation de l'état
        $allowedStates = ['en cours', 'validée'];
        if (empty($data['etat']) || !in_array($data['etat'], $allowedStates)) {
            $errors[] = "L'état de la commande est invalide";
        }
        
        if (!empty($errors)) {
            return implode(", ", $errors);
        }
        
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        // Vérifier d'abord si la commande est modifiable (état "en cours")
        $checkSql = "SELECT etat FROM commande WHERE id_commande = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            return "Commande introuvable";
        }
        
        $currentState = $checkResult->fetch_assoc()['etat'];
        if ($currentState !== 'en cours') {
            return "Seules les commandes 'en cours' peuvent être modifiées";
        }
        
        // Mise à jour de la commande
        $updateSql = "UPDATE commande SET 
                     nom = ?, 
                     prenom = ?, 
                     tlf = ?, 
                     adresse = ?, 
                     etat = ? 
                     WHERE id_commande = ?";
        
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssssi", 
            $data['nom'],
            $data['prenom'],
            $data['tlf'],
            $data['adresse'],
            $data['etat'],
            $id
        );
        
        if ($stmt->execute()) {
            return true;
        } else {
            return "Erreur lors de la mise à jour: " . $conn->error;
        }
    }

    /**
     * Supprime une commande
     */
    public function supprimerCommande($id) {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        // Vérifier d'abord si la commande est supprimable (état "en cours")
        $checkSql = "SELECT etat FROM commande WHERE id_commande = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            return "Commande introuvable";
        }
        
        $currentState = $checkResult->fetch_assoc()['etat'];
        if ($currentState !== 'en cours') {
            return "Seules les commandes 'en cours' peuvent être supprimées";
        }
        
        // Suppression de la commande
        $deleteSql = "DELETE FROM commande WHERE id_commande = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return "Erreur lors de la suppression: " . $conn->error;
        }
    }

    /**
     * Récupère toutes les commandes
     */
    public function getAllCommandes() {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        $result = $conn->query("SELECT * FROM commande ORDER BY date_livraison DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Récupère les commandes par état
     */
    public function getCommandesByEtat($etat) {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        $stmt = $conn->prepare("SELECT * FROM commande WHERE etat = ? ORDER BY date_livraison DESC");
        $stmt->bind_param("s", $etat);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get commands for a specific user
     */
    public function getCommandesByUserId($id_user) {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        if ($conn->connect_error) {
            throw new Exception("Erreur de connexion: " . $conn->connect_error);
        }
        
        $stmt = $conn->prepare("SELECT * FROM commande WHERE id_user = ? ORDER BY id_commande DESC");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $commandes = [];
        while ($row = $result->fetch_assoc()) {
            $commandes[] = $row;
        }
        
        $stmt->close();
        $conn->close();
        
        return $commandes;
    }

    // Dans votre classe Commande (model.php), ajoutez cette méthode
public function getFormattedProducts() {
    $produits = json_decode($this->produits, true);
    $html = '';
    
    if (is_array($produits)) {
        foreach ($produits as $produit) {
            $html .= sprintf(
                '<div class="produit-item">ID: %d - %s (%d x %s €)</div>',
                htmlspecialchars($produit['id']),
                htmlspecialchars($produit['name']),
                htmlspecialchars($produit['quantity']),
                number_format($produit['price'], 2)
            );
        }
    } else {
        $html = '<div>Aucun produit</div>';
    }
    
    return $html;
}

public function getCommandesPourAujourdhui() {
    $conn = new mysqli("localhost", "root", "", "projetweb2a");
    $today = date('Y-m-d');
    
    $stmt = $conn->prepare("SELECT * FROM commande WHERE date_livraison = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}
}