<?PHP
class Utilisateur {
    private ?int $id = null;
    private string $name;
    private string $email;
    private string $password;
    private string $role;

    // Constructeur
    function __construct(string $name, string $email, string $password, string $role) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    // Getters
    function getId(): ?int {
        return $this->id;
    }

    function getName(): string {
        return $this->name;
    }

    function getEmail(): string {
        return $this->email;
    }

    function getPassword(): string {
        return $this->password;
    }

    function getRole(): string {
        return $this->role;
    }

    // Setters
    function setId(int $id): void {
        $this->id = $id;
    }

    function setName(string $name): void {
        $this->name = $name;
    }

    function setEmail(string $email): void {
        $this->email = $email;
    }

    function setPassword(string $password): void {
        $this->password = $password;
    }

    function setRole(string $role): void {
        $this->role = $role;
    }
    ///////tebaa gestion evenement
/////// Ajoutez cette méthode depuis EtudiantModel
    public function getUtilisateurById($id) {
        $db = config::getConnexion();
        $sql = "SELECT id, name, email, role FROM utilisateur WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    
      public function getEtudiants() {
        $db = config::getConnexion();
        $sql = "SELECT id, name, email FROM utilisateur WHERE role = 'etudiant'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
