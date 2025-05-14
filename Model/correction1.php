<?php
require_once(__DIR__ . "/../Config.php");

class Correction1 {
    private $conn;
    private $id_exam;

    // Constructor to inject the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Method to get all corrections
    public function getAll() {
        $sql = "SELECT c.*, t.type_name, t.image as exam_image 
                FROM correction1 c 
                LEFT JOIN typeexam t ON c.id_exam = t.id";
        $stmt = $this->conn->query($sql);
        
        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to get corrections for a specific user
    public function getAllByUserId($user_id) {
        $sql = "SELECT c.*, t.type_name, t.image as exam_image 
                FROM correction1 c 
                LEFT JOIN typeexam t ON c.id_exam = t.id 
                WHERE c.id_user = ?
                ORDER BY c.id_cor DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to get one correction by ID
    public function getOne($id_cor) {
        $sql = "SELECT c.*, t.type_name, t.image as exam_image 
                FROM correction1 c 
                LEFT JOIN typeexam t ON c.id_exam = t.id 
                WHERE c.id_cor = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        $stmt->bindValue(1, $id_cor, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Method to get one correction by exam ID
    public function getOneByExamId($id_exam) {
        $sql = "SELECT c.*, t.type_name, t.image as exam_image 
                FROM correction1 c 
                LEFT JOIN typeexam t ON c.id_exam = t.id 
                WHERE c.id_exam = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return null;
        }

        $stmt->bindValue(1, $id_exam, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Method to create a new correction
    public function create($id_exam, $image2, $remarque, $note, $id_user = null) {
        $sql = "INSERT INTO correction1 (id_exam, image2, remarque, note, id_user) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        $stmt->bindValue(1, $id_exam, PDO::PARAM_INT);
        $stmt->bindValue(2, $image2, PDO::PARAM_STR);
        $stmt->bindValue(3, $remarque, PDO::PARAM_STR);
        $stmt->bindValue(4, $note, PDO::PARAM_STR);
        $stmt->bindValue(5, $id_user, $id_user === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        $result = $stmt->execute();
        
        return $result ? $this->conn->lastInsertId() : "Error: " . $this->conn->errorInfo();
    }

    // Method to update a correction
    public function update($id_cor, $id_exam, $image2, $remarque, $note, $id_user = null) {
        $sql = "UPDATE correction1 
                SET id_exam = ?, image2 = ?, remarque = ?, note = ?, id_user = ? 
                WHERE id_cor = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        $stmt->bindValue(1, $id_exam, PDO::PARAM_INT);
        $stmt->bindValue(2, $image2, PDO::PARAM_STR);
        $stmt->bindValue(3, $remarque, PDO::PARAM_STR);
        $stmt->bindValue(4, $note, PDO::PARAM_STR);
        $stmt->bindValue(5, $id_user, $id_user === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(6, $id_cor, PDO::PARAM_INT);

        $result = $stmt->execute();
        
        return $result ?: "Error: " . $this->conn->errorInfo();
    }

    // Method to update user_id for a correction
    public function updateUserId($id_cor, $id_user) {
        $sql = "UPDATE correction1 SET id_user = ? WHERE id_cor = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        $stmt->bindValue(1, $id_user, PDO::PARAM_INT);
        $stmt->bindValue(2, $id_cor, PDO::PARAM_INT);

        $result = $stmt->execute();
        
        return $result ?: "Error: " . $this->conn->errorInfo();
    }

    // Method to delete a correction by ID
    public function delete($id_cor) {
        $sql = "DELETE FROM correction1 WHERE id_cor = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        $stmt->bindValue(1, $id_cor, PDO::PARAM_INT);
        $result = $stmt->execute();
        
        return $result ?: "Error: " . $this->conn->errorInfo();
    }
}
?>
