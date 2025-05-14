<?php
// Inclure la configuration de la base de données
require_once $_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Config.php';

// Récupérer la connexion à la base de données
$conn = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/uploads/';
    $fileName = basename($_FILES['photo']['name']);
    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
        // Ici, vous pouvez également enregistrer les informations du fichier dans la base de données si nécessaire
        echo "Le fichier a été téléchargé avec succès.";
    } else {
        echo "Erreur lors du téléchargement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Upload Photo</title>
</head>
<body>
    <h2>Upload de photo</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="photo">Sélectionnez une photo:</label>
        <input type="file" name="photo" id="photo" accept="image/*"><br><br>
        <button type="submit">Télécharger</button>
    </form>
</body>
</html>
