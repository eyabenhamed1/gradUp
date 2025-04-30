<?php
require_once(__DIR__ . "/../Config.php");
class TypeExamController {

public function createTypeExam($type) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("INSERT INTO typeexam (type_name) VALUES (:type)");
        $query->bindParam(':type', $type);
        $query->execute();
    } catch (PDOException $e) {
        echo "Erreur lors de la création du type : " . $e->getMessage();
    }
}

public function getAllTypes() {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("SELECT * FROM typeexam");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des types : " . $e->getMessage();
        return [];
    }
}

public function updateTypeExam($oldType, $newType) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("UPDATE typeexam SET type_name = :newType WHERE type_name = :oldType");
        $query->bindParam(':newType', $newType);
        $query->bindParam(':oldType', $oldType);
        $query->execute();
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour du type : " . $e->getMessage();
    }
}

public function deleteTypeExam($type) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("DELETE FROM typeexam WHERE type_name = :type");
        $query->bindParam(':type', $type);
        $query->execute();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression du type : " . $e->getMessage();
    }
}

public function insertType($type, $image) {
    $db = config::getConnexion();
    $query = "INSERT INTO typeexam (type_name, image) VALUES (:type, :image)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':image', $image);
    return $stmt->execute();
}

public function getEXAMByTYPE($type) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("SELECT * FROM typeexam WHERE type_name = :type");
        $query->bindParam(':type', $type);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération du type : " . $e->getMessage();
        return null;
    }
}
}
?>