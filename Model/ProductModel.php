<?php
class Commande
{
    private ?int $id_commande;
    private string $nom;
    private string $prenom;
    private string $tlf;
    private string $adresse;
    /** @var array|string */ // Peut être un tableau PHP ou une chaîne JSON
    private $produits;
    private float $prix_total;
    private string $etat;
    private DateTime $date_creation;

    public function __construct(
        ?int $id_commande = null,
        string $nom,
        string $prenom,
        string $tlf,
        string $adresse,
        $produits, // Accepte tableau ou JSON
        float $prix_total,
        string $etat = 'en cours',
        ?DateTime $date_creation = null
    ) {
        $this->id_commande = $id_commande;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->tlf = $tlf;
        $this->adresse = $adresse;
        $this->produits = is_array($produits) ? json_encode($produits) : $produits;
        $this->prix_total = $prix_total;
        $this->etat = $etat;
        $this->date_creation = $date_creation ?? new DateTime();
    }

    // Getters
    public function getId(): ?int { return $this->id_commande; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getTlf(): string { return $this->tlf; }
    public function getAdresse(): string { return $this->adresse; }
    
    /**
     * @return array Décodage des produits depuis JSON
     */
    public function getProduits(): array { 
        return json_decode($this->produits, true) ?? []; 
    }
    
    public function getPrixTotal(): float { return $this->prix_total; }
    public function getEtat(): string { return $this->etat; }
    public function getDateCreation(): DateTime { return $this->date_creation; }

    // Setters
    public function setId(?int $id): void { $this->id_commande = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setTlf(string $tlf): void { $this->tlf = $tlf; }
    public function setAdresse(string $adresse): void { $this->adresse = $adresse; }
    
    /**
     * @param array|string $produits Accepte un tableau ou une chaîne JSON
     */
    public function setProduits($produits): void { 
        $this->produits = is_array($produits) ? json_encode($produits) : $produits; 
    }
    
    public function setPrixTotal(float $prix): void { $this->prix_total = $prix; }
    public function setEtat(string $etat): void { $this->etat = $etat; }
    public function setDateCreation(DateTime $date): void { $this->date_creation = $date; }

    /**
     * Formate la date pour l'affichage
     */
    public function getFormattedDate(): string {
        return $this->date_creation->format('d/m/Y H:i');
    }
}
?>