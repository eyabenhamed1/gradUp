<?php
require_once(__DIR__ . "/../../../../controller/evenementcontroller.php");
$controller = new evenementController();

$successMessage = "";
$errorMessage = "";

// Vérifier si l'ID est présent
if (!isset($_GET['id'])) {
    die("ID de l'événement manquant.");
}

$id = $_GET['id'];
$evenement = $controller->getevenementById($id);

if (!$evenement) {
    die("Événement introuvable.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_evenement = $_POST['date_evenement'];
    $lieu = $_POST['lieu'];
    $type_evenement = $_POST['type_evenement'];

    $image = $evenement['image']; // par défaut, garder l'image existante

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageExtension, $allowedExtensions)) {
            $targetDir = __DIR__ . "/../uploads/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $newFileName = uniqid() . '.' . $imageExtension;
            $targetFilePath = $targetDir . $newFileName;

            if (move_uploaded_file($imageTmp, $targetFilePath)) {
                $image = $newFileName;
            } else {
                $errorMessage = "❌ Erreur lors de l'upload de la nouvelle image.";
            }
        } else {
            $errorMessage = "❌ Format d'image non valide.";
        }
    }

    if (
        !empty($titre) &&
        !empty($description) &&
        !empty($date_evenement) &&
        !empty($lieu) &&
        !empty($type_evenement)
    ) {
        $controller->updateEvenement($id, $titre, $description, $date_evenement, $lieu, $type_evenement, $image);
        header("Location: evenement.php?update=success");
        exit();
    } else {
        $errorMessage = "❌ Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un événement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .image-preview {
            margin-top: 10px;
            max-width: 200px;
            display: block;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .file-info {
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body class="container py-5">
    <h1 class="mb-4">Modifier l'événement</h1>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre de l'événement</label>
            <input type="text" name="titre" id="titre" class="form-control" value="<?= htmlspecialchars($evenement['titre']) ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($evenement['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="lieu" class="form-label">Lieu</label>
            <textarea name="lieu" id="lieu" class="form-control"><?= htmlspecialchars($evenement['lieu']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="date_evenement" class="form-label">Date</label>
            <input type="date" name="date_evenement" id="date_evenement" class="form-control" value="<?= htmlspecialchars($evenement['date_evenement']) ?>">
        </div>
        <div class="mb-3">
            <label for="type_evenement" class="form-label">Type</label>
            <input type="text" name="type_evenement" id="type_evenement" class="form-control" value="<?= htmlspecialchars($evenement['type_evenement']) ?>">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image :</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF</div>
            <?php if (!empty($evenement['image'])): ?>
                <img id="imagePreview" class="image-preview" src="../uploads/<?= htmlspecialchars($evenement['image']) ?>" alt="Aperçu de l'image">
            <?php else: ?>
                <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image" style="display:none;">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="evenement.php" class="btn btn-secondary">Annuler</a>
    </form>

    <script>
        function validateForm() {
            const titre = document.getElementById("titre").value.trim();
            const description = document.getElementById("description").value.trim();
            const date_evenement = document.getElementById("date_evenement").value.trim();
            const lieu = document.getElementById("lieu").value.trim();
            const type_evenement = document.getElementById("type_evenement").value.trim();

            if (!titre || !description || !date_evenement || !lieu || !type_evenement) {
                alert("Veuillez remplir tous les champs obligatoires.");
                return false;
            }

            return true;
        }

        // Aperçu dynamique si on change l'image
        document.getElementById("image").addEventListener("change", function () {
            const file = this.files[0];
            const preview = document.getElementById("imagePreview");

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = "none";
            }
        });
    </script>
</body>
</html>