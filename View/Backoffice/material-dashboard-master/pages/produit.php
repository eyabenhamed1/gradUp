<?php
require_once(__DIR__ . "/../../../../controller/produitcontroller.php");
$controller = new ProduiController();
$produits = $controller->listeProduit();

// Traitement du formulaire d'ajout
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $categorie = $_POST['categorie'];

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
                    !empty($prix) &&
                    !empty($stock) &&
                    !empty($categorie) &&
                    is_numeric($prix) &&
                    is_numeric($stock) &&
                    intval($stock) == $stock &&
                    $stock >= 0 &&
                    preg_match("/^[A-Za-zÀ-ÿ\s\-']+$/", $name)
                ) {
                    $controller->createProduit($name, $description, $prix, $stock, $image, $categorie);
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

// Traitement de la mise à jour
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $categorie = $_POST['categorie'];
    $image = $_POST['current_image'];

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
        !empty($categorie) &&
        is_numeric($prix) &&
        is_numeric($stock) &&
        intval($stock) == $stock &&
        $stock >= 0 &&
        preg_match("/^[A-Za-zÀ-ÿ\s\-']+$/", $name)
    ) {
        $controller->updateProduit($id, $name, $description, $prix, $stock, $image, $categorie);
        header("Location: produit.php?update=success");
        exit();
    } else {
        $errorMessage = "❌ Veuillez remplir tous les champs correctement.";
    }
}

// Récupérer le produit à modifier si ID présent
$editProduct = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editProduct = $controller->getProduitById($id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Gestion des Produits</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .product-image {
      max-width: 80px;
      max-height: 80px;
      border-radius: 4px;
      object-fit: cover;
    }
    .action-buttons .btn {
      margin: 2px;
      padding: 0.3rem 0.6rem;
    }
    .modal-form label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    .modal-form input, .modal-form textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .modal-form .file-info {
      font-size: 12px;
      color: #666;
      margin-top: 5px;
    }
    .image-preview {
      max-width: 100px;
      max-height: 100px;
      display: none;
      margin-top: 10px;
    }
    .input-error {
      border-color: #f44336 !important;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <!-- Sidebar -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="#">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="ms-1 text-sm text-dark">Gestion Produits</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/dashboard.html">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="commande.php">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">voir les commandes</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/produit.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">Produits</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Produits</li>
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
              <label class="form-label">Rechercher...</label>
              <input type="text" class="form-control" id="searchInput">
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Liste des produits</h6>
                <div class="container mt-2">
                  <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="material-symbols-rounded">add</i> Ajouter un produit
                  </button>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <?php if (isset($_GET['add']) && $_GET['add'] === 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                  Produit ajouté avec succès!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php endif; ?>
              
              <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                  Produit mis à jour avec succès!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php endif; ?>
              
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="productsTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Image</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Prix</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stock</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Catégorie</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($produits as $p): ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($p['name']) ?></h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <?php if (!empty($p['image'])): ?>
                          <img src="<?php echo '../uploads/' . htmlspecialchars($p['image']); ?>" class="product-image" alt="Image produit">
                        <?php else: ?>
                          <span class="text-muted">Aucune image</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($p['description']) ?></p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold"><?= number_format($p['prix'], 2) ?> €</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="badge badge-sm <?= $p['stock'] > 0 ? 'bg-gradient-success' : 'bg-gradient-danger' ?>">
                          <?= htmlspecialchars($p['stock']) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">
                          <?= htmlspecialchars($p['categorie']) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center action-buttons">
                        <a href="produit.php?edit=<?= $p['id_produit'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                          <i class="material-symbols-rounded">edit</i>
                        </a>
                        <a href="produit_delete.php?id=<?= $p['id_produit'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                          <i class="material-symbols-rounded">delete</i>
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal d'ajout de produit -->
  <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProductModalLabel">Ajouter un nouveau produit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php if ($errorMessage && isset($_POST['add_product'])): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
          <?php endif; ?>
          
          <form method="POST" enctype="multipart/form-data" id="addProductForm" class="modal-form">
            <input type="hidden" name="add_product" value="1">
            
            <div class="mb-3">
              <label for="name">Nom du produit</label>
              <input type="text" name="name" id="name" class="form-control" required>
            </div>
            
            <div class="mb-3">
              <label for="description">Description</label>
              <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="prix">Prix</label>
                <input type="text" name="prix" id="prix" class="form-control" placeholder="ex: 19.99" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" min="0" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="categorie">Catégorie</label>
              <select name="categorie" id="categorie" class="form-control" required>
                <option value="">Sélectionnez une catégorie</option>
                <option value="informatique">Informatique</option>
                <option value="vetement">Vêtement</option>
                <option value="fourniture">Fourniture scolaire</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label for="image">Image du produit</label>
              <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
              <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF (max 2MB)</div>
              <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          <button type="submit" form="addProductForm" class="btn btn-primary">Ajouter</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de modification de produit -->
  <?php if ($editProduct): ?>
  <div class="modal fade show" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-modal="true" role="dialog" style="display: block; padding-right: 17px;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProductModalLabel">Modifier le produit</h5>
          <a href="produit.php" class="btn-close" aria-label="Close"></a>
        </div>
        <div class="modal-body">
          <?php if ($errorMessage && isset($_POST['update_product'])): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
          <?php endif; ?>
          
          <form method="POST" enctype="multipart/form-data" id="editProductForm" class="modal-form">
            <input type="hidden" name="update_product" value="1">
            <input type="hidden" name="id" value="<?= $editProduct['id_produit'] ?>">
            <input type="hidden" name="current_image" value="<?= $editProduct['image'] ?>">
            
            <div class="mb-3">
              <label for="edit_name">Nom du produit</label>
              <input type="text" name="name" id="edit_name" class="form-control" value="<?= htmlspecialchars($editProduct['name']) ?>" required>
            </div>
            
            <div class="mb-3">
              <label for="edit_description">Description</label>
              <textarea name="description" id="edit_description" class="form-control" rows="3" required><?= htmlspecialchars($editProduct['description']) ?></textarea>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="edit_prix">Prix</label>
                <input type="text" name="prix" id="edit_prix" class="form-control" value="<?= htmlspecialchars($editProduct['prix']) ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_stock">Stock</label>
                <input type="number" name="stock" id="edit_stock" class="form-control" value="<?= htmlspecialchars($editProduct['stock']) ?>" min="0" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="edit_categorie">Catégorie</label>
              <select name="categorie" id="edit_categorie" class="form-control" required>
                <option value="informatique" <?= $editProduct['categorie'] == 'informatique' ? 'selected' : '' ?>>Informatique</option>
                <option value="vetement" <?= $editProduct['categorie'] == 'vetement' ? 'selected' : '' ?>>Vêtement</option>
                <option value="fourniture" <?= $editProduct['categorie'] == 'fourniture' ? 'selected' : '' ?>>Fourniture scolaire</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label for="edit_image">Image du produit</label>
              <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
              <div class="file-info">Formats acceptés: JPG, JPEG, PNG, GIF (max 2MB)</div>
              <?php if (!empty($editProduct['image'])): ?>
                <img id="editImagePreview" class="image-preview" src="../uploads/<?= htmlspecialchars($editProduct['image']) ?>" alt="Image actuelle">
              <?php else: ?>
                <img id="editImagePreview" class="image-preview" src="#" alt="Aperçu de l'image" style="display: none;">
              <?php endif; ?>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <a href="produit.php" class="btn btn-secondary">Annuler</a>
          <button type="submit" form="editProductForm" class="btn btn-primary">Mettre à jour</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-backdrop fade show"></div>
  <?php endif; ?>

  <!-- Scripts -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    // Recherche en temps réel
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const input = this.value.toLowerCase();
      const rows = document.querySelectorAll('#productsTable tbody tr');
      
      rows.forEach(row => {
        const name = row.querySelector('td:first-child h6').textContent.toLowerCase();
        const description = row.querySelector('td:nth-child(3) p').textContent.toLowerCase();
        
        if (name.includes(input) || description.includes(input)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // Initialisation du scrollbar
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    // Validation du formulaire d'ajout
    document.getElementById('addProductForm')?.addEventListener('submit', function(e) {
      let isValid = true;
      const name = document.getElementById('name');
      const description = document.getElementById('description');
      const prix = document.getElementById('prix');
      const stock = document.getElementById('stock');
      const categorie = document.getElementById('categorie');
      const image = document.getElementById('image');

      // Réinitialiser les erreurs
      [name, description, prix, stock, categorie, image].forEach(el => el.classList.remove('is-invalid', 'input-error'));

      // Validation du nom
      const nameRegex = /^[A-Za-zÀ-ÿ\s\-']+$/;
      if (!name.value.trim() || !nameRegex.test(name.value)) {
        name.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation de la description
      if (!description.value.trim()) {
        description.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation du prix
      if (!prix.value.trim() || isNaN(prix.value) || parseFloat(prix.value) <= 0) {
        prix.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation du stock
      if (!stock.value.trim() || isNaN(stock.value) || parseInt(stock.value) < 0) {
        stock.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation de la catégorie
      if (!categorie.value) {
        categorie.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation de l'image
      if (!image.files || image.files.length === 0) {
        image.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
        alert('Veuillez corriger les erreurs dans le formulaire.');
      }
    });

    // Validation du formulaire de modification
    document.getElementById('editProductForm')?.addEventListener('submit', function(e) {
      let isValid = true;
      const name = document.getElementById('edit_name');
      const description = document.getElementById('edit_description');
      const prix = document.getElementById('edit_prix');
      const stock = document.getElementById('edit_stock');
      const categorie = document.getElementById('edit_categorie');

      // Réinitialiser les erreurs
      [name, description, prix, stock, categorie].forEach(el => el.classList.remove('is-invalid', 'input-error'));

      // Validation du nom
      const nameRegex = /^[A-Za-zÀ-ÿ\s\-']+$/;
      if (!name.value.trim() || !nameRegex.test(name.value)) {
        name.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation de la description
      if (!description.value.trim()) {
        description.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation du prix
      if (!prix.value.trim() || isNaN(prix.value) || parseFloat(prix.value) <= 0) {
        prix.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation du stock
      if (!stock.value.trim() || isNaN(stock.value) || parseInt(stock.value) < 0) {
        stock.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      // Validation de la catégorie
      if (!categorie.value) {
        categorie.classList.add('is-invalid', 'input-error');
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
        alert('Veuillez corriger les erreurs dans le formulaire.');
      }
    });

    // Aperçu de l'image pour l'ajout
    document.getElementById('image')?.addEventListener('change', function(e) {
      const file = e.target.files[0];
      const preview = document.getElementById('imagePreview');
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
      } else {
        preview.style.display = 'none';
      }
    });

    // Aperçu de l'image pour la modification
    document.getElementById('edit_image')?.addEventListener('change', function(e) {
      const file = e.target.files[0];
      const preview = document.getElementById('editImagePreview');
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
      } else if (!preview.src.includes('uploads/')) {
        preview.style.display = 'none';
      }
    });

    // Fermer le modal après succès si nécessaire
    <?php if (isset($_GET['add']) && $_GET['add'] === 'success'): ?>
      var addModal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
      if (addModal) addModal.hide();
    <?php endif; ?>
  </script>
</body>
</html>