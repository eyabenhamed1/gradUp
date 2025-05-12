<?php
class typeexam {
    private string $type;

    public function __construct(string $type) {
        $this->type = $type;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): self {
        $this->type = $type;
        return $this;
    }

    public function insertType($type, $image) {
        $sql = "INSERT INTO typeexam (type, image) VALUES (:type, :image)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':image', $image);
        $stmt->execute();
    }
    
}
?>
