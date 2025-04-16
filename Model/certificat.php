<?php 
class Certificat
{
    private ?int $id; 
    private string $nom;
    private string $type; 
    private string $objet; 
    private Date $date_demande;
    private string $status;
    private string $niveau;
    private string $image;

    // Constructeur
    public function __construct(?int $id = null, string $nom, string $type, string $objet, Date $date_demande,string $status,string $niveau, string $image)
    {
        $this->id= $id;
        $this->nom= $nom;
        $this->type = $type;
        $this->objet = $objet;
        $this->date_demande = $date_demande;
        $this->status = $status;
        $this->niveau = $niveau;
        $this->image = $image;
    }

    // Getter et Setter pour certificat
    public function getId(): ?int {
        return $this->id;
    }
    
    public function setId(?int $id): void {
        $this->id = $id;
    }
    
    public function getNom(): string {
        return $this->nom;
    }
    
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }
    
    public function getType(): string {
        return $this->type;
    }
    
    public function setType(string $type): void {
        $this->type = $type;
    }
    
    public function getObjet(): string {
        return $this->objet;
    }
    
    public function setObjet(string $objet): void {
        $this->objet = $objet;
    }
    
    public function getDateDemande(): string {
        return $this->date_demande;
    }
    
    public function setDateDemande(string $date_demande): void {
        $this->date_demande = $date_demande;
    }
    
    public function getStatus(): string {
        return $this->status;
    }
    
    public function setStatus(string $status): void {
        $this->status = $status;
    }
    
    public function getNiveau(): string {
        return $this->niveau;
    }
    
    public function setNiveau(string $niveau): void {
        $this->niveau = $niveau;
    }
    

    // Getter et Setter pour image
    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }
}
?>
