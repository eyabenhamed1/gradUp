<?php

class User {
    private $id;
    private $username;
    private $password;
    private $category;
    private $adresse;
    private $role;

    // Constructor
    public function __construct($id = null, $username = '', $password = '', $category = '', $adresse = '', $role = '') {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->category = $category;
        $this->adresse = $adresse;
        $this->role = $role;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getRole() {
        return $this->role;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

    public function setRole($role) {
        $this->role = $role;
    }
}
?>
