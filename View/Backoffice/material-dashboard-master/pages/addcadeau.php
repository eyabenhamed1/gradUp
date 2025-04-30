<?php
require_once(__DIR__ . "/../../../../controller/cadeaucontroller.php");

$controller = new CadeauController();

$successMessage = "";
$errorMessage = "";

// Succès
if (isset($_GET['add']) && $_GET['add'] === "success") {
    $successMessage = "✅ Cadeau ajouté avec succès !";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type_cadeau = trim($_POST['type_cadeau'] ?? '');
    $date_cadeau = trim($_POST['date_cadeau'] ?? '');
    $id = trim($_POST['id'] ?? '');

    if (!$type_cadeau || !$date_cadeau || !$id) {
        $errorMessage = "❌ Tous les champs sont requis.";
    } else if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = "❌ Une image valide est requise.";
    } else {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageExtension, $allowedExtensions)) {
            $errorMessage = "❌ Format d'image non valide (jpg, jpeg, png, gif).";
        } else {
            $targetDir = __DIR__ . "/../uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $newFileName = uniqid('cadeau_', true) . '.' . $imageExtension;
            $targetFilePath = $targetDir . $newFileName;

            if (move_uploaded_file($imageTmp, $targetFilePath)) {
                $controller->createCadeau($type_cadeau, $date_cadeau, $id, $newFileName);

                header("Location: cadeau.php?add=success");
                exit();
            } else {
                $errorMessage = "❌ Erreur lors du téléchargement de l'image.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un cadeau</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            background: #fff;
            margin: 50px auto;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            margin-top: 15px;
            display: block;
            color: #555;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 15px;
        }
        button {
            margin-top: 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .success, .error {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        .input-error {
            border-color: red;
        }
        .image-preview {
            margin-top: 10px;
            max-width: 100%;
            display: none;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .file-info {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Ajouter un cadeau</h1>

    <?php if ($successMessage): ?>
        <div class="success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
    <!-- Champ caché pour l'ID -->
    <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
    
    <label for="type_cadeau">Type de cadeau :</label>
    <input type="text" name="type_cadeau" id="type_cadeau" required>

    <label for="date_cadeau">Date du cadeau :</label>
    <input type="date" name="date_cadeau" id="date_cadeau" required>

    <label for="image">Image :</label>
    <input type="file" name="image" id="image" accept="image/*">
    <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF</div>
    <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image">

    <button type="submit">Ajouter le cadeau</button>
</form>
</div>

<script>
    function validateForm() {
        let id = document.getElementById('id');
        let type_cadeau = document.getElementById('type_cadeau');
        let date_cadeau = document.getElementById('date_cadeau');

        [id, type_cadeau, date_cadeau].forEach(el => el.classList.remove('input-error'));

        if (!id.value.trim()) {
            id.classList.add('input-error');
            alert("❌ Le champ 'ID' est requis.");
            return false;
        }
        if (!type_cadeau.value.trim()) {
            type_cadeau.classList.add('input-error');
            alert("❌ Le champ 'Type de cadeau' est requis.");
            return false;
        }
        if (!date_cadeau.value.trim()) {
            date_cadeau.classList.add('input-error');
            alert("❌ Le champ 'Date de cadeau' est requis.");
            return false;
        }
        return true;
    }

    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
</script>
</body>
</html>

