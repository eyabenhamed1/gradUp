<?php
require_once(__DIR__ . "/../../../../controller/certificatcontroller.php");

$controller = new CertificatController();

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $type = isset($_POST["type"]) ? $_POST["type"] : "";
    $objet = $_POST['objet'];
    $date_demande = $_POST['date_demande'];
    $status = $_POST['status'];
    $niveau = $_POST['niveau'];
    $errorMessage = ""; // Message d'erreur initialisé

    // Vérification des champs
    if (empty($nom)) {
        $errorMessage = "❌ Le champ 'Nom' est requis.";
    } elseif (empty($type)) {
        $errorMessage = "❌ Le champ 'Type' est requis.";
    } elseif (empty($objet)) {
        $errorMessage = "❌ Le champ 'Objet' est requis.";
    } elseif (empty($date_demande)) {
        $errorMessage = "❌ Le champ 'Date de demande' est requis.";
    } elseif (empty($status)) {
        $errorMessage = "❌ Le champ 'Status' est requis.";
    } elseif (empty($niveau)) {
        $errorMessage = "❌ Le champ 'Niveau' est requis.";
    }

    // Gestion de l'image
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

                // Si tous les champs sont valides
                if (empty($errorMessage)) {
                    $controller->createCertificat($nom, $type, $objet, $date_demande, $status, $niveau, $image);
                    header("Location: certificat.php?add=success");
                    exit();
                }
            } else {
                $errorMessage = "❌ Erreur lors de l'upload de l'image.";
            }
        } else {
            $errorMessage = "❌ Format d'image non valide (jpg, jpeg, png, gif).";
        }
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = "❌ Veuillez sélectionner une image.";
    }

    // Si une erreur se certificat, afficher le message
    if (!empty($errorMessage)) {
        echo $errorMessage;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un certificat</title>
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
    <h1>Ajouter un certificat</h1>

    <?php if ($successMessage): ?>
        <div class="success"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="error"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
    <label for="id">ID du certificat :</label>
    <input type="text" name="id" id="id">

    <label for="nom">Nom du certificat :</label>
    <input type="text" name="nom" id="nom">

    <label for="type">Type :</label>
    <input type="text" name="type" id="type">

    <label for="objet">Objet :</label>
    <input type="text" name="objet" id="objet">

    <label for="date_demande">Date de demande :</label>
    <input type="date" name="date_demande" id="date_demande">

    <label for="status">Statut :</label>
    <input type="text" name="status" id="status">

    <label for="niveau">Niveau :</label>
    <input type="text" name="niveau" id="niveau">

    <label for="image">Image :</label>
    <input type="file" name="image" id="image" accept="image/*">
    <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF (max 2MB)</div>
    <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image">

    <button type="submit">Ajouter le certificat</button>
</form>

</div>

<script>
function validateForm() {
    let isValid = true;

    const id = document.getElementById("id");
    const nom = document.getElementById("nom");
    const type = document.getElementById("type");
    const objet = document.getElementById("objet");
    const date_demande = document.getElementById("date_demande");
    const status = document.getElementById("status");
    const niveau = document.getElementById("niveau");

    // Retirer les erreurs précédentes
    [id, nom, type, objet, date_demande, status, niveau].forEach(el => el.classList.remove('input-error'));

    // Vérification de chaque champ
    if (!nom.value.trim()) {
        nom.classList.add("input-error");
        alert("❌ Le champ 'Nom' est requis.");
        isValid = false;
    }

    if (!type.value.trim()) {
        type.classList.add("input-error");
        alert("❌ Le champ 'Type' est requis.");
        isValid = false;
    }

    if (!objet.value.trim()) {
        objet.classList.add("input-error");
        alert("❌ Le champ 'Objet' est requis.");
        isValid = false;
    }

    if (!date_demande.value.trim()) {
        date_demande.classList.add("input-error");
        alert("❌ Le champ 'Date de demande' est requis.");
        isValid = false;
    }

    if (!status.value.trim()) {
        status.classList.add("input-error");
        alert("❌ Le champ 'Status' est requis.");
        isValid = false;
    }

    if (!niveau.value.trim()) {
        niveau.classList.add("input-error");
        alert("❌ Le champ 'Niveau' est requis.");
        isValid = false;
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
