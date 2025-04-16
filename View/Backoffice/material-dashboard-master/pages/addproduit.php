<?php
require_once(__DIR__ . "/../../../../controller/produitcontroller.php");

$controller = new ProduiController();

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];

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
                    !empty($name) &&
                    !empty($description) &&
                    !empty($prix)&&
                    !empty($stock)&&
                    is_numeric($prix) &&
                    is_numeric($stock) &&
                    intval($stock) == $stock &&
                    $stock >= 0 &&
                    preg_match("/^[A-Za-zÀ-ÿ\s\-']+$/", $name)
                ) {
                    $controller->createProduit($name, $description, $prix, $stock, $image);
                    header("Location: produit.php?add=success");
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
    <title>Ajouter un produit</title>
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
    <h1>Ajouter un Produit</h1>

    <?php if ($successMessage): ?>
        <div class="success"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="error"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
        <label for="name">Nom du produit :</label>
        <input type="text" name="name" id="name">

        <label for="description">Description :</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <label for="prix">Prix :</label>
        <input type="text" name="prix" id="prix" placeholder="ex: 19.99">

        <label for="stock">Stock :</label>
        <input type="number" name="stock" id="stock" min="0" step="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')">

        <label for="image">Image :</label>
        <input type="file" name="image" id="image" accept="image/*">
        <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF (max 2MB)</div>
        <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image">

        <button type="submit">Ajouter le produit</button>
    </form>
</div>

<script>
    function validateForm() {
        let isValid = true;

        const name = document.getElementById("name");
        const description = document.getElementById("description");
        const prix = document.getElementById("prix");
        const stock = document.getElementById("stock");
        const image = document.getElementById("image");

        const nameRegex = /^[A-Za-zÀ-ÿ\s\-']+$/;
        const intRegex = /^[0-9]+$/;

        [name, description, prix, stock, image].forEach(el => el.classList.remove('input-error'));

        if (!name.value.trim() || !nameRegex.test(name.value)) {
            name.classList.add("input-error");
            isValid = false;
        }

        if (!description.value.trim()) {
            description.classList.add("input-error");
            isValid = false;
        }

        if (!prix.value.trim() || isNaN(prix.value) || parseFloat(prix.value) < 0) {
            prix.classList.add("input-error");
            isValid = false;
        }

        if (!stock.value.trim() || !intRegex.test(stock.value) || parseInt(stock.value) < 0) {
            stock.classList.add("input-error");
            isValid = false;
        }

        if (!image.files || image.files.length === 0) {
            image.classList.add("input-error");
            isValid = false;
        }

        if (!isValid) {
            alert("❌ Veuillez remplir tous les champs correctement.");
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
