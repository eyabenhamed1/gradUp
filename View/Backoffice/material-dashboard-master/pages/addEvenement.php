<?php
require_once(__DIR__ . "/../../../../controller/evenementcontroller.php");

$controller = new EvenementController();

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_evenement = $_POST['date_evenement'];
    $lieu = $_POST['lieu'];
    $type_evenement = $_POST['type_evenement'];

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

                if (
                    !empty($titre) &&
                    !empty($description) &&
                    !empty($date_evenement) &&
                    !empty($lieu) &&
                    !empty($type_evenement) 
                ) {
                    $controller->createEvenement($titre, $description, $date_evenement, $lieu, $type_evenement,$image);
                    header("Location: evenement.php?add=success");
                    exit();
                } else {
                    $errorMessage = "❌ Veuillez remplir tous les champs correctement.";
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
    <title>Ajouter un Événement</title>
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

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
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
    </style>
</head>
<body>
<div class="container">
    <h1>Ajouter un Événement</h1>

    <?php if ($successMessage): ?>
        <div class="success"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="error"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
        <label for="titre">Titre :</label>
        <input type="text" name="titre" id="titre">

        <label for="description">Description :</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <label for="date_evenement">Date :</label>
        <input type="text" name="date_evenement" id="date_evenement" placeholder="YYYY-MM-DD">

        <label for="lieu">Lieu :</label>
        <input type="text" name="lieu" id="lieu">
       
       
        <label for="type_evenement">Type :</label>
        <input type="text" name="type_evenement" id="type_evenement">

        <label for="image">Image :</label>
        <input type="file" name="image" id="image" accept="image/*">
        <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF (max 2MB)</div>
        <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image">

        <button type="submit">Ajouter l'événement</button>
    </form>
</div>

<script>
    function validateForm() {
        let isValid = true;

        const titre = document.getElementById("titre");
        const description = document.getElementById("description");
        const date = document.getElementById("date_evenement");
        const lieu = document.getElementById("lieu");
        const type_evenemnt = document.getElementById("type_evenement");
        const image = document.getElementById("image");

        // Reset styles
        [titre, description, date, lieu,type_evenement,image].forEach(el => el.classList.remove('input-error'));

        if (titre.value.trim() === "") {
            titre.classList.add("input-error");
            isValid = false;
        }
        if (description.value.trim() === "") {
            description.classList.add("input-error");
            isValid = false;
        }
        if (lieu.value.trim() === "") {
            lieu.classList.add("input-error");
            isValid = false;
        }
        if (type_evenement.value.trim() === "") {
            type_evenement.classList.add("input-error");
            isValid = false;
        }
        if (!image.files || image.files.length === 0) {
            image.classList.add("input-error");
            isValid = false;}
        const datePattern = /^\d{4}-\d{2}-\d{2}$/;
        if (!datePattern.test(date.value.trim())) {
            alert("⚠️ La date doit être au format YYYY-MM-DD.");
            date.classList.add("input-error");
            isValid = false;
        }

        
        if (!isValid) {
            alert("Merci de corriger les champs surlignés.");
        }

        return isValid;
    }
    // Aperçu d’image côté front
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