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
    private $date_commande;

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

    public function getDateLivraison() {
        return $this->date_livraison;
    }

    public function getDateCommande() {
        return $this->date_commande;
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

    public function setDateLivraison($date_livraison) {
        $this->date_livraison = $date_livraison;
    }

    /**
     * Enregistre une nouvelle commande dans la base de données
     */
    public function save() {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        if ($conn->connect_error) {
            throw new Exception("Erreur de connexion: " . $conn->connect_error);
        }
        
        $stmt = $conn->prepare("INSERT INTO commande (nom, prenom, tlf, adresse, produits, prix_total, etat, date_livraison) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssdss", 
            $this->nom, 
            $this->prenom, 
            $this->tlf, 
            $this->adresse, 
            $this->produits, 
            $this->prix_total, 
            $this->etat,
            $this->date_livraison);
        
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
    public static function getCommandeById($id) {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        $stmt = $conn->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        $data = $result->fetch_assoc();
        $commande = new Commande();
        $commande->id_commande = $data['id_commande'];
        $commande->nom = $data['nom'];
        $commande->prenom = $data['prenom'];
        $commande->tlf = $data['tlf'];
        $commande->adresse = $data['adresse'];
        $commande->produits = $data['produits'];
        $commande->prix_total = $data['prix_total'];
        $commande->etat = $data['etat'];
        $commande->date_livraison = $data['date_livraison'];
        $commande->date_commande = $data['date_commande'];
        
        $stmt->close();
        $conn->close();
        
        return $commande;
    }

    /**
     * Modifie une commande existante
     */
    public function update() {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        $stmt = $conn->prepare("UPDATE commande SET 
                               nom = ?, 
                               prenom = ?, 
                               tlf = ?, 
                               adresse = ?, 
                               produits = ?,
                               prix_total = ?,
                               etat = ?,
                               date_livraison = ?
                               WHERE id_commande = ?");
        
        $stmt->bind_param("sssssdssi", 
            $this->nom,
            $this->prenom,
            $this->tlf,
            $this->adresse,
            $this->produits,
            $this->prix_total,
            $this->etat,
            $this->date_livraison,
            $this->id_commande
        );
        
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        
        return $result;
    }

    /**
     * Met à jour uniquement le statut et la date de livraison
     */
    public static function updateStatus($id, $etat, $date_livraison = null) {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        $stmt = $conn->prepare("UPDATE commande SET 
                               etat = ?,
                               date_livraison = ?
                               WHERE id_commande = ?");
        
        $stmt->bind_param("ssi", 
            $etat,
            $date_livraison,
            $id
        );
        
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        
        return $result;
    }

    /**
     * Supprime une commande
     */
    public static function delete($id) {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        $stmt = $conn->prepare("DELETE FROM commande WHERE id_commande = ?");
        $stmt->bind_param("i", $id);
        
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        
        return $result;
    }

    /**
     * Récupère toutes les commandes
     */
    public static function getAll() {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        $result = $conn->query("SELECT * FROM commande ORDER BY date_commande DESC");
        
        $commandes = [];
        while ($data = $result->fetch_assoc()) {
            $commande = new Commande();
            $commande->id_commande = $data['id_commande'];
            $commande->nom = $data['nom'];
            $commande->prenom = $data['prenom'];
            $commande->tlf = $data['tlf'];
            $commande->adresse = $data['adresse'];
            $commande->produits = $data['produits'];
            $commande->prix_total = $data['prix_total'];
            $commande->etat = $data['etat'];
            $commande->date_livraison = $data['date_livraison'];
            $commande->date_commande = $data['date_commande'];
            
            $commandes[] = $commande;
        }
        
        $conn->close();
        return $commandes;
    }

    /**
     * Récupère les commandes par état
     */
    public static function getByEtat($etat) {
        $conn = new mysqli("localhost", "root", "", "projetweb2a");
        
        $stmt = $conn->prepare("SELECT * FROM commande WHERE etat = ? ORDER BY date_commande DESC");
        $stmt->bind_param("s", $etat);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $commandes = [];
        while ($data = $result->fetch_assoc()) {
            $commande = new Commande();
            $commande->id_commande = $data['id_commande'];
            $commande->nom = $data['nom'];
            $commande->prenom = $data['prenom'];
            $commande->tlf = $data['tlf'];
            $commande->adresse = $data['adresse'];
            $commande->produits = $data['produits'];
            $commande->prix_total = $data['prix_total'];
            $commande->etat = $data['etat'];
            $commande->date_livraison = $data['date_livraison'];
            $commande->date_commande = $data['date_commande'];
            
            $commandes[] = $commande;
        }
        
        $stmt->close();
        $conn->close();
        
        return $commandes;
    }

    /**
     * Formate les produits pour l'affichage
     */
    public function getFormattedProducts() {
        $produits = json_decode($this->produits, true);
        $html = '';
        
        if (is_array($produits)) {
            foreach ($produits as $produit) {
                $html .= sprintf(
                    '<div class="produit-item">ID: %d - %s (%d x %s €)</div>',
                    htmlspecialchars($produit['id_produit']),
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

    /**
     * Formate la date de livraison
     */
    public function getFormattedDeliveryDate() {
        if ($this->date_livraison) {
            return date('d/m/Y', strtotime($this->date_livraison));
        }
        return "Non définie";
    }

    /**
     * Formate la date de commande
     */
    public function getFormattedOrderDate() {
        return date('d/m/Y H:i', strtotime($this->date_commande));
    }
}