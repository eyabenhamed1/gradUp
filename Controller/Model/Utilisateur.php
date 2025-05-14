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
}
?>
