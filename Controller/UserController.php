<?php
require_once(__DIR__ . "/../Config.php");

class UserController {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function login($email, $password) {
        try {
            $query = "SELECT * FROM user WHERE email = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                return [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
} 