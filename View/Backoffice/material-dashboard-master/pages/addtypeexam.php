<?php
require_once(__DIR__ . "/../../../../controller/typeexamcontroller.php");

$controller = new typeexamcontroller();

$successMessage = "";
$errorMessage = "";

// Liste des types d'examen mis à jour
$typesExistants = [
    ['id' => 1, 'type_name' => 'QCM (Questionnaire à Choix Multiples)'],
    ['id' => 2, 'type_name' => 'TP (Travaux Pratiques)'],
    ['id' => 3, 'type_name' => 'Question-Réponse'],
    ['id' => 4, 'type_name' => 'Listening'],
   
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedTypeId = $_POST['type'] ?? '';
    $selectedType = '';
    
    foreach ($typesExistants as $type) {
        if ($type['id'] == $selectedTypeId) {
            $selectedType = $type['type_name'];
            break;
        }
    }
    
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

                // Valider le type sélectionné
                if (!empty($selectedType)) {
                    $controller->insertType($selectedType, $image); // ⚠️ Assurez-vous que createtype accepte 2 arguments
                    header("Location: typeexam.php?add=success");
                    exit();
                } else {
                    $errorMessage = "❌ Veuillez sélectionner un type d'examen.";
                }
            } else {
                $errorMessage = "❌ Erreur lors de l'upload de l'image.";
            }
        } else {
            $errorMessage = "❌ Format d'image non valide (jpg, jpeg, png, gif).";
        }
    } else {
        $errorMessage = "❌ Veuillez sélectionner une image.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un type</title>
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
            background: white;
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

        input, select {
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
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .success, .error {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
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
    <h1>Ajouter un type</h1>

    <?php if ($successMessage): ?>
        <div class="success"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="error"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
        <label for="type">Sélectionner un type d'examen :</label>
        <select name="type" id="type">
            <option value="">-- Choisissez un type --</option>
            <?php foreach ($typesExistants as $type): ?>
                <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['type_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="image">Image :</label>
        <input type="file" name="image" id="image" accept="image/*">
        <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF (max 2MB)</div>
        <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image">

        <button type="submit">Ajouter le type</button>
    </form>
</div>

<script>
    function validateForm() {
        let isValid = true;

        const type = document.getElementById("type");

        if (!type.value.trim()) {
            type.classList.add("input-error");
            isValid = false;
        } else {
            type.classList.remove("input-error");
        }

        if (!isValid) {
            alert("❌ Veuillez remplir tous les champs correctement.");
        }

        return isValid;
    }

    // Aperçu image
    document.getElementById("image").addEventListener("change", function () {
        const file = this.files[0];
        const preview = document.getElementById("imagePreview");

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";
        }
    });
</script>
</body>
</html>
