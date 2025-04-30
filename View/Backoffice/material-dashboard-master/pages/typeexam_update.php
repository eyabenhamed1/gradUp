<?php
require_once(__DIR__ . "/../../../../controller/typeexamcontroller.php");

$controller = new typeexamController();

$successMessage = "";
$errorMessage = "";

// Vérifier si type est présent
if (!isset($_GET['type'])) {
    die("type d' examan manquant.");
}

$type = $_GET['type'];
$produit = $controller->getEXAMByTYPE($type);

if (!$type) {
    die("type introuvable.");
}

/*if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST['type'];
    

   

    if (
        !empty($type) 
       >= 0 &&
        preg_match("/^[A-Za-zÀ-ÿ\s\-']+$/", $type)
    ) {
        $controller->updatetypeexam($type);
        header("Location: typeexam.php?update=success");
        exit();
    } else {
        $errorMessage = "❌ Veuillez remplir tous les champs correctement.";
    }
}*/
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un type</title>
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
    <h1 class="mb-4">Modifier le type</h1>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
        <div class="mb-3">
            <label for="type" class="form-label">Nom du type</label>
            <input name="text" id ="type" class="form-control" value="<?= htmlspecialchars($typeexam['type']) ?>">
        </div>


        <div class="mb-3">
            <label for="image" class="form-label">Image :</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF</div>
            <?php if (!empty($produit['image'])): ?>
                <img id="imagePreview" class="image-preview" src="../uploads/<?= htmlspecialchars($produit['image']) ?>" alt="Aperçu de l'image">
            <?php else: ?>
                <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image" style="display:none;">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="typeexam.php" class="btn btn-secondary">Annuler</a>
    </form>

    <script>
        function validateForm() {
            const name = document.getElementById("type").value.trim();
         
            const nameRegex = /^[A-Za-zÀ-ÿ\s\-']+$/;
            const intRegex = /^[0-9]+$/;

            if (!type ) {
                alert("Tous les champs sont obligatoires.");
                return false;
            }

            /*if (!nameRegex.test(type)) {
                alert("Le type contenir uniquement des lettres.");
                return false;
            }*/

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
