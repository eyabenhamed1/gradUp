<?php
require_once(__DIR__ . "/../../../../controller/produitcontroller.php");

$controller = new ProduiController();

$successMessage = "";
$errorMessage = "";

// Vérifier si l'ID est présent
if (!isset($_GET['id'])) {
    die("ID du produit manquant.");
}

$id = $_GET['id'];
$produit = $controller->getProduitById($id);

if (!$produit) {
    die("Produit introuvable.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $image = $produit['image']; // par défaut, garder l'image existante

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
        !empty($name) &&
        !empty($description) &&
        is_numeric($prix) &&
        is_numeric($stock) &&
        intval($stock) == $stock &&
        $stock >= 0 &&
        preg_match("/^[A-Za-zÀ-ÿ\s\-']+$/", $name)
    ) {
        $controller->updateProduit($id, $name, $description, $prix, $stock, $image);
        header("Location: produit.php?update=success");
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
    <title>Modifier un Produit</title>
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
    <h1 class="mb-4">Modifier le Produit</h1>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
        <div class="mb-3">
            <label for="name" class="form-label">Nom du produit</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($produit['name']) ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($produit['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix</label>
            <input type="text" name="prix" id="prix" class="form-control" value="<?= htmlspecialchars($produit['prix']) ?>">
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" name="stock" id="stock" class="form-control" value="<?= htmlspecialchars($produit['stock']) ?>" min="0" step="1">
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
        <a href="produit.php" class="btn btn-secondary">Annuler</a>
    </form>

    <script>
        function validateForm() {
            const name = document.getElementById("name").value.trim();
            const description = document.getElementById("description").value.trim();
            const prix = document.getElementById("prix").value.trim();
            const stock = document.getElementById("stock").value.trim();
            const nameRegex = /^[A-Za-zÀ-ÿ\s\-']+$/;
            const intRegex = /^[0-9]+$/;

            if (!name || !description || !prix || !stock) {
                alert("Tous les champs sont obligatoires.");
                return false;
            }

            if (!nameRegex.test(name)) {
                alert("Le nom doit contenir uniquement des lettres.");
                return false;
            }

            if (isNaN(prix) || parseFloat(prix) < 0) {
                alert("Le prix doit être un nombre positif.");
                return false;
            }

            if (!intRegex.test(stock)) {
                alert("Le stock doit être un entier positif.");
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
