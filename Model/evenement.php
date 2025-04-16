<?php
class Evenement {
    private $id;
    private $titre;
    private $description;
    private $date_evenement;
    private $lieu;
    private $type_evenement;
    private $image;

    // Solution 1: Tous les paramètres optionnels
    public function __construct(
        $id = null, 
        $titre = null, 
        $description = null, 
        $date_evenement = null, 
        $lieu = null, 
        $type_evenement = null, 
        $image = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->date_evenement = $date_evenement;
        $this->lieu = $lieu;
        $this->type_evenement = $type_evenement;
        $this->image = $image;
    }

    // Solution 2: Utiliser une méthode hydrate
    public function hydrate(array $data) {
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getDateEvenement() { return $this->date_evenement; }
    public function getLieu() { return $this->lieu; }
    public function getTypeEvenement() { return $this->type_evenement; }
    public function getImage() { return $this->image; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setDateEvenement($date_evenement) { $this->date_evenement = $date_evenement; }
    public function setLieu($lieu) { $this->lieu = $lieu; }
    public function setTypeEvenement($type_evenement) { $this->type_evenement = $type_evenement; }
    public function setImage($image) { $this->image = $image; }
}
?>