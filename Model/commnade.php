<?php

class Commande {
    private ?int $id_commande;
    private float $prix_total;
    private string $nom;
    private string $prenom;
    private string $tlf;
    private string $adresse;
    private array $produits;
    private string $etat;

    public const ETAT_EN_COURS = 'en cours';
    public const ETAT_VALIDEE = 'validée';

    public function __construct(
        string $nom,
        string $prenom,
        string $tlf,
        string $adresse,
        array $produits,
        float $prix_total,
        string $etat = self::ETAT_EN_COURS,
        ?int $id_commande = null
    ) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->setTlf($tlf);
        $this->adresse = $adresse;
        $this->setProduits($produits);
        $this->prix_total = $prix_total;
        $this->setEtat($etat);
        $this->id_commande = $id_commande;
    }

    // Getters
    public function getIdCommande(): ?int { return $this->id_commande; }
    public function getPrixTotal(): float { return $this->prix_total; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getTlf(): string { return $this->tlf; }
    public function getAdresse(): string { return $this->adresse; }
    public function getProduits(): array { return $this->produits; }
    public function getEtat(): string { return $this->etat; }

    // Setters avec validation
    public function setTlf(string $tlf): void {
        if (!preg_match('/^\+?\d{8,15}$/', $tlf)) {
            throw new InvalidArgumentException("Format de téléphone invalide");
        }
        $this->tlf = $tlf;
    }

    public function setProduits(array $produits): void {
        foreach ($produits as $produit) {
            if (!isset($produit['id_produit'], $produit['quantite'], $produit['prix'])) {
                throw new InvalidArgumentException("Structure produit invalide");
            }
        }
        $this->produits = $produits;
    }

    public function setEtat(string $etat): void {
        if (!in_array($etat, [self::ETAT_EN_COURS, self::ETAT_VALIDEE])) {
            throw new InvalidArgumentException("Statut invalide");
        }
        $this->etat = $etat;
    }
}
?>