<?php
require_once(__DIR__ . "/../../../../controller/certificatcontroller.php");

$controller = new CertificatController();

$successMessage = "";
$errorMessage = "";

// Vérifier si l'ID est présent
if (!isset($_GET['id'])) {
    die("ID du certificat manquant.");
}

$id = $_GET['id'];
$certificat = $controller->getCertificatById($id);

if (!$certificat) {
    die("certificat introuvable.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id']; // Assurez-vous que l'ID est bien envoyé dans le formulaire
    $nom = $_POST['nom'];
    $type = $_POST['type'];
    $objet = $_POST['objet'];
    $date_demande = $_POST['date_demande'];
    $status = $_POST['status'];
    $niveau = $_POST['niveau'];
    $image = $certificat['image']; // Image actuelle si non modifiée

    // Gestion de l'image si un nouveau fichier est uploadé
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
            $errorMessage = "❌ Format d'image non valide (jpg, jpeg, png, gif).";
        }
    }

    // Validation des champs obligatoires
    if (empty($nom)) {
        $errorMessage = "❌ Le champ 'Nom' est requis.";
    } elseif (!preg_match("/^[A-Za-zÀ-ÿ\s\-']+$/", $nom)) {
        $errorMessage = "❌ Le nom ne doit contenir que des lettres.";
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
    } else {
        // Tous les champs sont OK, on effectue la mise à jour
        $controller->updateCertificat($id, $nom, $type,$objet, $date_demande, $status, $niveau, $image);
        header("Location: certificat.php?update=success");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Certificat</title>
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
    <h1 class="mb-4">Modifier le Certificat</h1>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
    <input type="hidden" name="id" value="<?= htmlspecialchars($certificat['id']) ?>">
    <div class="mb-3">
        <label for="nom" class="form-label">Nom du certificat</label>
        <input type="text" name="nom" id="nom" class="form-control" value="<?= htmlspecialchars($certificat['nom']) ?>">
    </div>

    <div class="mb-3">
        <label for="type" class="form-label">Type</label>
        <textarea name="type" id="type" class="form-control"><?= htmlspecialchars($certificat['type']) ?></textarea>
    </div>

    <div class="mb-3">
        <label for="objet" class="form-label">Objet</label>
        <input type="text" name="objet" id="objet" class="form-control" value="<?= htmlspecialchars($certificat['objet']) ?>">
    </div>

    <div class="mb-3">
        <label for="date_demande" class="form-label">Date de demande</label>
        <input type="date" name="date_demande" id="date_demande" class="form-control" value="<?= htmlspecialchars($certificat['date_demande']) ?>">
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Statut</label>
        <input type="text" name="status" id="status" class="form-control" value="<?= htmlspecialchars($certificat['status']) ?>">
    </div>

    <div class="mb-3">
        <label for="niveau" class="form-label">Niveau</label>
        <input type="text" name="niveau" id="niveau" class="form-control" value="<?= htmlspecialchars($certificat['niveau']) ?>">
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Image :</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
        <div class="file-info">Formats acceptés : JPG, JPEG, PNG, GIF</div>
        <?php if (!empty($certificat['image'])): ?>
            <img id="imagePreview" class="image-preview" src="../uploads/<?= htmlspecialchars($certificat['image']) ?>" alt="Aperçu de l'image">
        <?php else: ?>
            <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image" style="display:none;">
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Mettre à jour</button>
    <a href="certificat.php" class="btn btn-secondary">Annuler</a>
</form>


    <script>
        function validateForm() {
            const nom= document.getElementById("nom").value.trim();
            const type= document.getElementById("type").value.trim();
            const objet= document.getElementById("objet").value.trim();
            const date_demande = document.getElementById("date_demande").value.trim();
            const status = document.getElementById("status").value.trim();
            const niveau = document.getElementById("niveau").value.trim();
            const nameRegex = /^[A-Za-zÀ-ÿ\s\-']+$/;
            const intRegex = /^[0-9]+$/;

            if (!nom|| !type || !objet || !date_demande ||!status || !niveau) {
                alert("Tous les champs sont obligatoires.");
                return false;
            }

            if (!nameRegex.test(nom)) {
                alert("Le nom doit contenir uniquement des lettres.");
                return false;
            }

            if (!intRegex.test(type)) {
                alert("Le type doit être un nombre positif.");
                return falsefunction validateForm() {
    const nom = document.getElementById("nom").value.trim();
    const type = document.getElementById("type").value.trim();
    const objet = document.getElementById("objet").value.trim();
    const date_demande = document.getElementById("date_demande").value.trim();
    const status = document.getElementById("status").value.trim();
    const niveau = document.getElementById("niveau").value.trim();

    const nameRegex = /^[A-Za-zÀ-ÿ\s\-']+$/;

    if (!nom) {
        alert("❌ Le champ 'Nom' est requis.");
        return false;
    }

    if (!nameRegex.test(nom)) {
        alert("❌ Le nom doit contenir uniquement des lettres.");
        return false;
    }

    if (!type) {
        alert("❌ Le champ 'Type' est requis.");
        return false;
    }

    if (!objet) {
        alert("❌ Le champ 'Objet' est requis.");
        return false;
    }

    if (!date_demande) {
        alert("❌ Le champ 'Date de demande' est requis.");
        return false;
    }

    if (!status) {
        alert("❌ Le champ 'Status' est requis.");
        return false;
    }

    if (!niveau) {
        alert("❌ Le champ 'Niveau' est requis.");
        return false;
    }

    return true;
}
;
            }

            if (!intRegex.test(objet)) {
                alert("Le objet doit être un entier positif.");
                return false;
            }

            if (!intRegex.test(date_demande)) {
                alert("Le date_demande doit être un entier positif.");
                return false;
            }

            if (!intRegex.test(status)) {
                alert("Le status doit être un entier positif.");
                return false;
            }

            if (!intRegex.test(niveau)) {
                alert("Le niveau doit être un entier positif.");
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
