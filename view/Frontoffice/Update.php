<?php
require_once __DIR__ . '/../../controller/RecetteController.php';

$error = "";
$recette = null;

// Create an instance of the controller
$recetteController = new RecetteController();

if (
    isset($_POST["titre"], $_POST["ingredients"], $_POST["video_url"], $_POST["id_recette"]) &&
    !empty($_POST["titre"]) &&
    !empty($_POST["ingredients"]) &&
    !empty($_POST["video_url"]) &&
    !empty($_POST["id_recette"])
) {
    $recette = new Recette(
        $_POST['id_recette'],
        $_POST['titre'],
        $_POST['ingredients'],
        $_POST['video_url'],
        $_POST['id_plats'] ?? null // use null if not passed
    );

    if ($recetteController->updateRecette($recette)) {
        header('Location: recetteList.php');
        exit();
    } else {
        $error = "Failed to update the recipe.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Recipe</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Update Recipe</h1>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <?php
                            if (isset($_POST['id'])) {
                                $recette = $recetteController->getRecetteById($_POST['id']);
                                if ($recette) {
                            ?>
                            <form method="POST">
                                <input type="hidden" name="id_recette" value="<?php echo htmlspecialchars($recette['id_recette']); ?>">

                                <div class="form-group">
                                    <label for="titre">Title:</label>
                                    <input type="text" name="titre" id="titre" class="form-control" value="<?php echo htmlspecialchars($recette['titre']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="ingredients">Ingredients:</label>
                                    <textarea name="ingredients" id="ingredients" class="form-control" required><?php echo htmlspecialchars($recette['ingredients']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="video_url">Video URL:</label>
                                    <input type="text" name="video_url" id="video_url" class="form-control" value="<?php echo htmlspecialchars($recette['video_url']); ?>" required>
                                </div>

                                <input type="hidden" name="id_plats" value="<?php echo htmlspecialchars($recette['id_plats'] ?? ''); ?>">

                                <div class="text-danger"><?php echo $error; ?></div>

                                <button type="submit" class="btn btn-primary">Update Recipe</button>
                            </form>
                            <?php
                                } else {
                                    echo "<div class='text-danger'>Recipe not found.</div>";
                                }
                            } else {
                                echo "<div class='text-danger'>No ID provided.</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto text-center">
                    <span>&copy; Travel Booking 2024</span>
                </div>
            </footer>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
