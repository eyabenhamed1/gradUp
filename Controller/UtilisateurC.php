<?php
include "../config.php";
require_once '../Model/Utilisateur.php';

class UtilisateurC {

    // Ajouter un utilisateur
    function ajouterUtilisateur($utilisateur){
        $sql = "INSERT INTO utilisateur (name, email, password, role) 
                VALUES (:name, :email, :password, :role)";
        $db = config::getConnexion();
        try {
            // Hachage du mot de passe pour la sécurité
            $hashedPassword = password_hash($utilisateur->getPassword(), PASSWORD_DEFAULT);

            $query = $db->prepare($sql);
            $query->execute([
                'name' => $utilisateur->getName(),
                'email' => $utilisateur->getEmail(),
                'password' => $hashedPassword,
                'role' => $utilisateur->getRole()  // Récupérer et ajouter le rôle
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Afficher tous les utilisateurs
    function afficherUtilisateurs(){
        $sql = "SELECT * FROM utilisateur";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Supprimer un utilisateur
    function supprimerUtilisateur($id){
        $sql = "DELETE FROM utilisateur WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Modifier un utilisateur
    function modifierUtilisateur($utilisateur, $id){
        $sql = 'UPDATE utilisateur SET 
                name = :name, 
                email = :email,
                password = :password,
                role = :role
                WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            // Hachage du mot de passe si modifié
            $hashedPassword = password_hash($utilisateur->getPassword(), PASSWORD_DEFAULT);
            $query->execute([
                'name' => $utilisateur->getName(),
                'email' => $utilisateur->getEmail(),
                'password' => $hashedPassword,
                'role' => $utilisateur->getRole(),
                'id' => $id
            ]);
            echo $query->rowCount() . " records UPDATED successfully <br>";
        } catch (PDOException $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Récupérer un utilisateur par son ID
    function recupererUtilisateur($id){
        $sql = "SELECT * FROM utilisateur WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Connexion de l'utilisateur (vérification du mot de passe)
    function connexionUser($email, $password){
        $sql = "SELECT * FROM utilisateur WHERE email = :email";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            $user = $query->fetch(PDO::FETCH_OBJ);

            if ($user && password_verify($password, $user->password)) {
                // Connexion réussie
                $_SESSION['id'] = $user->id;
                $_SESSION['role'] = $user->role;
                return $user->role;
            } else {
                return "Pseudo ou mot de passe incorrect";
            }
        } catch (Exception $e) {
            return "Erreur: " . $e->getMessage();
        }
    }

    // Rechercher des utilisateurs par nom
    function rechercherUsers($name) {        
        $sql = "SELECT * FROM utilisateur WHERE name LIKE :name";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['name' => $name . '%']);
            return $query;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>
