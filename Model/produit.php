<?php
class Produit
{
    private ?int $id_produit;
    private float $prix;
    private string $name;
    private string $description;
    private int $stock;
    private string $image;
    private string $categorie;

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
        $this->categorie = $categorie;
    }

    // Getters
    public function getId(): ?int 
    { 
        return $this->id_produit; 
    }

    public function getPrix(): float 
    { 
        return $this->prix; 
    }

    public function getName(): string 
    { 
        return $this->name; 
    }

    public function getDescription(): string 
    { 
        return $this->description; 
    }

    public function getStock(): int 
    { 
        return $this->stock; 
    }

    public function getImage(): string 
    { 
        return $this->image; 
    }

    public function getCategorie(): string 
    { 
        return $this->categorie; 
    }

    // Setters
    public function setId(int $id_produit): void 
    {
        $this->id_produit = $id_produit;
    }

    public function setPrix(float $prix): void 
    {
        $this->prix = $prix;
    }

    public function setName(string $name): void 
    {
        $this->name = $name;
    }

    public function setDescription(string $description): void 
    {
        $this->description = $description;
    }

    public function setStock(int $stock): void 
    {
        $this->stock = $stock;
    }

    public function setImage(string $image): void 
    {
        $this->image = $image;
    }

    public function setCategorie(string $categorie): void 
    {
        $this->categorie = $categorie;
    }

    // Méthode pour convertir le produit en tableau associatif
    public function toArray(): array
    {
        return [
            'id_produit' => $this->id_produit,
            'prix' => $this->prix,
            'name' => $this->name,
            'description' => $this->description,
            'stock' => $this->stock,
            'image' => $this->image,
            'categorie' => $this->categorie
        ];
    }

    // Méthode pour créer un Produit à partir d'un tableau associatif
    public static function fromArray(array $data): Produit
    {
        return new Produit(
            $data['id_produit'] ?? null,
            $data['prix'],
            $data['name'],
            $data['description'],
            $data['stock'],
            $data['image'],
            $data['categorie']
        );
    }
}