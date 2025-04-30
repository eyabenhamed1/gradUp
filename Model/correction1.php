<?php
require_once(__DIR__ . "/../Config.php");

class Correction1 {
    private $conn;

    // Constructor to inject the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Method to get all corrections
    public function getAll() {
        $sql = "SELECT * FROM correction1";
        $result = $this->conn->query($sql);
        
        // Handle error if query fails
        if ($result === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        return $result;
    }

    // Method to get one correction by ID
    public function getOne($id_cor) {
        $sql = "SELECT * FROM correction1 WHERE id_cor = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        // Bind parameters and execute
        $stmt->bindValue(1, $id_cor, PDO::PARAM_INT);  // PDO uses bindValue() instead of bind_param
        $stmt->execute();
        
        // Get result and fetch associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result;
        } else {
            return null; // No record found
        }
    }



    
    // Method to create a new correction
    public function create($id_cor, $image2, $remarque, $note) {
        $sql = "INSERT INTO correction1 (id_cor, image2, remarque, note) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        // Bind parameters and execute
        $stmt->bindValue(1, $id_cor, PDO::PARAM_INT);  // PDO uses bindValue() instead of bind_param
        $stmt->bindValue(2, $image2, PDO::PARAM_STR);  // Bind the second parameter as a string
        $stmt->bindValue(3, $remarque, PDO::PARAM_STR); // Bind the third parameter as a string
        $stmt->bindValue(4, $note, PDO::PARAM_STR);     // Bind the fourth parameter as a string

        $result = $stmt->execute();
        
        if ($result) {
            return true;
        } else {
            return "Error: " . $this->conn->errorInfo();
        }
    }

    // Method to update a correction
    public function update($id_cor, $image2, $remarque, $note) {
        $sql = "UPDATE correction1 SET image2 = ?, remarque = ?, note = ? WHERE id_cor = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        // Bind parameters and execute
        $stmt->bindValue(1, $image2, PDO::PARAM_STR);  // Bind the first parameter as a string
        $stmt->bindValue(2, $remarque, PDO::PARAM_STR); // Bind the second parameter as a string
        $stmt->bindValue(3, $note, PDO::PARAM_STR);     // Bind the third parameter as a string
        $stmt->bindValue(4, $id_cor, PDO::PARAM_INT);   // Bind the fourth parameter as an integer

        $result = $stmt->execute();
        
        if ($result) {
            return true;
        } else {
            return "Error: " . $this->conn->errorInfo();
        }
    }

    // Method to delete a correction by ID
    public function delete($id_cor) {
        $sql = "DELETE FROM correction1 WHERE id_cor = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return "Error: " . $this->conn->errorInfo();
        }

        // Bind parameters and execute
        $stmt->bindValue(1, $id_cor, PDO::PARAM_INT);  // Bind the parameter as an integer
        $result = $stmt->execute();
        
        if ($result) {
            return true;
        } else {
            return "Error: " . $this->conn->errorInfo();
        }
    }
}
?>
