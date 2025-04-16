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
        string $categorie
    ) {
        $this->id_produit = $id_produit;
        $this->prix = $prix;
        $this->name = $name;
        $this->description = $description;
        $this->stock = $stock;
        $this->image = $image;
    }

    // Getters
    public function getId(): ?int { return $this->id_produit; }
    public function getPrix(): float { return $this->prix; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getStock(): int { return $this->stock; }
    public function getImage(): string { return $this->image; }
}
?>