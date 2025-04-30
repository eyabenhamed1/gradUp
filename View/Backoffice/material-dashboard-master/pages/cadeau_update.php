<?php
require_once(__DIR__ . "/../../../../Controller/cadeaucontroller.php");

$controller = new CadeauController();

// Récupération des données existantes
if (isset($_GET['id_cadeau'])) {
    $idCadeau = $_GET['id_cadeau'];
    $cadeau = $controller->getCadeauById($idCadeau);

    if (!$cadeau) {
        die("Cadeau non trouvé !");
    }
} else {
    die("ID Cadeau manquant !");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type_cadeau'];
    $date = $_POST['date_cadeau'];
    $imagePath = $cadeau['image']; // Keep existing image by default

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../../../Uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Debugging: Log upload details
        error_log("Image upload detected: " . $_FILES['image']['name']);

        // Calculate hash of the uploaded image
        $uploadedImageHash = md5_file($_FILES['image']['tmp_name']);

        // Check if the existing image exists
        $existingImagePath = !empty($cadeau['image']) ? $uploadDir . $cadeau['image'] : null;
        $regenerateFilename = true; // Flag to determine if we need a new filename

        if ($existingImagePath && file_exists($existingImagePath)) {
            $existingImageHash = md5_file($existingImagePath);
            // Check if it's the same image and if the filename uses a hyphen
            if ($uploadedImageHash === $existingImageHash && strpos($cadeau['image'], '-') === false) {
                // Same image and filename already uses dot, reuse it
                $imagePath = $cadeau['image'];
                $regenerateFilename = false;
                error_log("Reusing existing filename: $imagePath (same image, dot format)");
            }
        }

        if ($regenerateFilename) {
            // Generate new filename with cadeau_ prefix and dot
            $imageName = 'cadeau_' . uniqid() . '.' . basename($_FILES['image']['name']);
            $imageFullPath = $uploadDir . $imageName;

            // Debugging: Log new filename
            error_log("Generating new filename: $imageName");

            // Move uploaded file to destination
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imageFullPath)) {
                // Delete old image if exists
                if ($existingImagePath && file_exists($existingImagePath)) {
                    unlink($existingImagePath);
                    error_log("Deleted old image: $existingImagePath");
                }
                $imagePath = $imageName; // Store new filename with cadeau_ prefix and dot
            } else {
                $error = "Erreur lors du téléchargement de l'image.";
                error_log("Failed to move uploaded file to: $imageFullPath");
            }
        }
    } else {
        // Debugging: Log if no image was uploaded
        error_log("No image uploaded or upload error: " . ($_FILES['image']['error'] ?? 'No file'));
    }

    $controller->updateCadeau($idCadeau, $type, $date, $imagePath);

    header("Location: cadeau.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Cadeau</title>
    <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier un Cadeau</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id_cadeau" value="<?= htmlspecialchars($cadeau['id_cadeau']) ?>">

            <div class="mb-3">
                <label for="type_cadeau" class="form-label">Type du Cadeau</label>
                <input type="text" class="form-control" name="type_cadeau" id="type_cadeau" value="<?= htmlspecialchars($cadeau['type_cadeau']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="date_cadeau" class="form-label">Date du Cadeau</label>
                <input type="date" class="form-control" name="date_cadeau" id="date_cadeau" value="<?= htmlspecialchars($cadeau['date_cadeau']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image Ascenseur Image du Cadeau</label>
                <?php if (!empty($cadeau['image'])) : ?>
                    <div class="mb-2">
                        <img src="/Uploads/<?= htmlspecialchars($cadeau['image']) ?>" alt="Current Image" style="max-width: 200px;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" name="image" id="image" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="cadeau.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>