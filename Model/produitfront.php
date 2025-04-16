<?php
class Produit
{
    private ?int $id_produit;
    private float $prix;
    private string $name;
    private string $description;
    private int $stock;
    public string $image;
    

    public function __construct(
        ?int $id_produit = null,
        float $prix,
        string $name,
        string $description,
        int $stock,
        string $image,
        
    ) {
        $this->id_produit = $id_produit;
        $this->prix = $prix;
        $this->name = $name;
        $this->description = $description;
        $this->stock = $stock;
        $this->image = $image;
        $this->categorie = $categorie;
    }

    // Getters
    public function getId(): ?int { return $this->id_produit; }
    public function getPrix(): float { return $this->prix; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getStock(): int { return $this->stock; }
    public function getImage(): string { return $this->image; }

    // Setters
    public function setId(?int $id): void { $this->id_produit = $id; }
    public function setPrix(float $prix): void { $this->prix = $prix; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(string $desc): void { $this->description = $desc; }
    public function setStock(int $stock): void { $this->stock = $stock; }
    public function setImage(string $image): void { $this->image = $image; }
}
?>