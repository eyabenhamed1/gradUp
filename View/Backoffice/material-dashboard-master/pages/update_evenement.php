<?php
session_start();
require_once(__DIR__ . "/../../../../controller/evenementcontroller.php");

$controller = new evenementController();

// Vérifier si l'ID est présent dans POST
if (!isset($_POST['id'])) {
    $_SESSION['error'] = "ID de l'événement manquant";
    header("Location: evenement.php");
    exit;
}

$id = $_POST['id'];
$evenement = $controller->getEvenementById($id);

if (!$evenement) {
    $_SESSION['error'] = "Événement introuvable";
    header("Location: evenement.php");
    exit;
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Validation des données
        $requiredFields = ['titre', 'description', 'date_evenement', 'lieu', 'type_evenement'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Le champ $field est obligatoire");
            }
        }

        $titre = htmlspecialchars($_POST['titre']);
        $description = htmlspecialchars($_POST['description']);
        $date_evenement = $_POST['date_evenement'];
        $lieu = htmlspecialchars($_POST['lieu']);
        $type_evenement = htmlspecialchars($_POST['type_evenement']);
        $image = $evenement['image']; // Conserver l'image existante par défaut

        // Gestion de l'upload de la nouvelle image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageTmp = $_FILES['image']['tmp_name'];
            $imageName = basename($_FILES['image']['name']);
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($imageExtension, $allowedExtensions)) {
                throw new Exception("Format d'image non valide. Formats acceptés: JPG, JPEG, PNG, GIF");
            }

            $targetDir = __DIR__ . "/../uploads/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Supprimer l'ancienne image si elle existe
            if (!empty($evenement['image']) && file_exists($targetDir . $evenement['image'])) {
                unlink($targetDir . $evenement['image']);
            }

            $newFileName = uniqid() . '.' . $imageExtension;
            $targetFilePath = $targetDir . $newFileName;

            if (!move_uploaded_file($imageTmp, $targetFilePath)) {
                throw new Exception("Erreur lors de l'upload de l'image");
            }

            $image = $newFileName;
        }

        // Mise à jour de l'événement
        $success = $controller->updateEvenement(
            $id,
            $titre,
            $description,
            $date_evenement,
            $lieu,
            $type_evenement,
            $image
        );

        if ($success) {
            $_SESSION['success'] = "Événement mis à jour avec succès";
        } else {
            throw new Exception("Erreur lors de la mise à jour de l'événement");
        }

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: evenement.php");
    exit;
}