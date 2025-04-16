<?php

include_once '../../model/User.php';

class UserController {

    // Add a new user
    public function addUser(User $user) {
        $db = $this->getDatabaseConnection();

        $query = 'INSERT INTO user (username, password, category, adresse, role) VALUES (?, ?, ?, ?, ?)';
        $stmt = $db->prepare($query);

        if (!$stmt) {
            die('Prepare failed: ' . $db->error);
        }

        $stmt->bind_param(
            'sssss',
            $user->getUsername(),
            $user->getPassword(), // Make sure it's hashed if used in production
            $user->getCategory(),
            $user->getAdresse(),
            $user->getRole()
        );

        return $stmt->execute();
    }

    // Update existing user
    public function updateUser(User $user) {
        $db = $this->getDatabaseConnection();

        $query = 'UPDATE user SET username = ?, password = ?, category = ?, adresse = ?, role = ? WHERE id = ?';
        $stmt = $db->prepare($query);

        if (!$stmt) {
            die('Prepare failed: ' . $db->error);
        }

        $stmt->bind_param(
            'sssssi',
            $user->getUsername(),
            $user->getPassword(), // Also hashed if needed
            $user->getCategory(),
            $user->getAdresse(),
            $user->getRole(),
            $user->getId()
        );

        return $stmt->execute();
    }

    // Delete a user by ID
    public function deleteUser($id) {
        $db = $this->getDatabaseConnection();

        $query = 'DELETE FROM user WHERE id = ?';
        $stmt = $db->prepare($query);

        if (!$stmt) {
            die('Prepare failed: ' . $db->error);
        }

        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    // Get list of all users (password included)
    public function getUserList() {
        $db = $this->getDatabaseConnection();

        $query = 'SELECT * FROM user';
        $result = $db->query($query);

        if (!$result) {
            die('Query failed: ' . $db->error);
        }

        $userList = [];
        while ($row = $result->fetch_assoc()) {
            $userList[] = $row;
        }

        return $userList;
    }

    // Get single user by ID
    public function getUserById($id) {
        $db = $this->getDatabaseConnection();

        $query = 'SELECT * FROM user WHERE id = ?';
        $stmt = $db->prepare($query);

        if (!$stmt) {
            die('Prepare failed: ' . $db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Login using hashed password
    public function login($username, $password) {
        $db = $this->getDatabaseConnection();

        $query = 'SELECT * FROM user WHERE username = ? LIMIT 1';
        $stmt = $db->prepare($query);

        if (!$stmt) {
            die('Prepare failed: ' . $db->error);
        }

        $stmt->bind_param('s', $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // If password is hashed in DB
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return false;
    }

    // Database connection
    private function getDatabaseConnection() {
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database = '2a41';

        $db = new mysqli($host, $username, $password, $database);

        if ($db->connect_error) {
            die('Connection failed: ' . $db->connect_error);
        }

        return $db;
    }
}
?>
