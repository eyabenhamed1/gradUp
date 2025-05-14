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

public function deleteTypeExam($id) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("DELETE FROM typeexam WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $query->execute();
        if ($result && $query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression du type : " . $e->getMessage());
        return false;
    }
}

public function insertType($type, $image, $image3) {
    $db = config::getConnexion();
    $query = "INSERT INTO typeexam (type_name, image, image3) VALUES (:type, :image, :image3)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':image', $image);
    $stmt->bindParam(':image3', $image3);
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

public function updateTypeExamImage($id, $imageName) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("UPDATE typeexam SET image = :image WHERE id = :id");
        $query->bindParam(':image', $imageName);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $query->execute();
        if ($result && $query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de l'image : " . $e->getMessage());
        return false;
    }
}

public function updateTypeExamImage3($id, $imageName3) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("UPDATE typeexam SET image3 = :image3 WHERE id = :id");
        $query->bindParam(':image3', $imageName3);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $query->execute();
        if ($result && $query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de l'image3 : " . $e->getMessage());
        return false;
    }
}

public function updateTypeExamImages($id, $imageName, $imageName3) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare("UPDATE typeexam SET image = :image, image3 = :image3 WHERE id = :id");
        $query->bindParam(':image', $imageName);
        $query->bindParam(':image3', $imageName3);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $query->execute();
        if ($result && $query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour des images : " . $e->getMessage());
        return false;
    }
}
}
?>