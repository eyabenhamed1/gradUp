<?php
// DEBUT DU FICHIER - Doit être la toute première ligne sans aucun espace avant
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Commande.php');

$commande = new Commande();

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $commande->supprimerCommande($id);
    
    if ($result === true) {
        $_SESSION['message'] = "Commande #$id supprimée avec succès";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = $result;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: macommande.php");
    exit();
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modifier_commande'])) {
    $id = (int)$_POST['id_commande'];
    $data = [
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'tlf' => $_POST['tlf'],
        'adresse' => $_POST['adresse'],
        'etat' => $_POST['etat']
    ];
    
    $result = $commande->modifierCommande($id, $data);
    
    if ($result === true) {
        $_SESSION['message'] = "Commande #$id mise à jour avec succès";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = $result;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: macommande.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
        }
        
        /* Barre de navigation */
        .navbar {
            background-color: var(--primary-color);
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .navbar-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand i {
            margin-right: 10px;
        }
        
        .navbar-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.2);
        }
        
        .nav-link.active {
            background-color: rgba(255,255,255,0.3);
        }
        
        /* Style général */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: var(--text-color);
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        tr:nth-child(even) {
            background-color: var(--light-gray);
        }
        
        tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .btn-edit {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #27ae60;
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
        }
        
        .btn-disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        
        .total-price {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-en-cours {
            background-color: var(--warning-color);
            color: white;
        }
        
        .status-validee {
            background-color: var(--success-color);
            color: white;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 600px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="navbar-brand">
                <i class="fas fa-store"></i> MonSite
            </a>
            <div class="navbar-links">
                <a href="essaiee.php" class="nav-link">
                    <i class="fas fa-shopping-bag"></i> Boutique
                </a>
                <a href="macommande.php" class="nav-link active">
                    <i class="fas fa-shopping-cart"></i> Commandes
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>
        
        <h1><i class="fas fa-shopping-cart"></i> Gestion des Commandes</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOM</th>
                    <th>PRENOM</th>
                    <th>TÉLÉPHONE</th>
                    <th>ADRESSE</th>
                    <th>TOTAL</th>
                    <th>STATUT</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $commandes = $commande->getAllCommandes();
                
                if (!empty($commandes)) {
                    foreach ($commandes as $cmd) {
                        $isEditable = ($cmd['etat'] == 'en cours');
                        ?>
                        <tr>
                            <td><?= $cmd['id_commande'] ?></td>
                            <td><?= htmlspecialchars($cmd['nom']) ?></td>
                            <td><?= htmlspecialchars($cmd['prenom']) ?></td>
                            <td><?= htmlspecialchars($cmd['tlf']) ?></td>
                            <td><?= htmlspecialchars($cmd['adresse']) ?></td>
                            <td class="total-price"><?= $cmd['prix_total'] ?> €</td>
                            <td>
                                <span class="status-badge status-<?= str_replace(' ', '-', $cmd['etat']) ?>">
                                    <?= ucfirst($cmd['etat']) ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <button onclick="openEditModal(
                                    '<?= $cmd['id_commande'] ?>',
                                    '<?= addslashes($cmd['nom']) ?>',
                                    '<?= addslashes($cmd['prenom']) ?>',
                                    '<?= addslashes($cmd['tlf']) ?>',
                                    '<?= addslashes($cmd['adresse']) ?>',
                                    '<?= $cmd['etat'] ?>'
                                )" 
                                class="btn btn-edit <?= !$isEditable ? 'btn-disabled' : '' ?>"
                                <?= !$isEditable ? 'title="Seulement pour commandes en cours"' : '' ?>>
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                
                                <a href="macommande.php?action=supprimer&id=<?= $cmd['id_commande'] ?>" 
                                   onclick="return confirmDelete('<?= $cmd['id_commande'] ?>', '<?= $cmd['etat'] ?>')"
                                   class="btn btn-delete <?= !$isEditable ? 'btn-disabled' : '' ?>"
                                   <?= !$isEditable ? 'title="Seulement pour commandes en cours"' : '' ?>>
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="8" class="empty-message">Aucune commande trouvée dans la base de données</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de modification -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Modifier la commande</h2>
            <form method="POST">
                <input type="hidden" name="id_commande" id="modalCommandeId">
                
                <div class="form-group">
                    <label for="modalCommandeNom">Nom:</label>
                    <input type="text" class="form-control" name="nom" id="modalCommandeNom" required>
                </div>
                
                <div class="form-group">
                    <label for="modalCommandePrenom">Prénom:</label>
                    <input type="text" class="form-control" name="prenom" id="modalCommandePrenom" required>
                </div>
                
                <div class="form-group">
                    <label for="modalCommandeTlf">Téléphone:</label>
                    <input type="text" class="form-control" name="tlf" id="modalCommandeTlf" required>
                </div>
                
                <div class="form-group">
                    <label for="modalCommandeAdresse">Adresse:</label>
                    <textarea class="form-control" name="adresse" id="modalCommandeAdresse" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="modalCommandeEtat">État:</label>
                    <select name="etat" id="modalCommandeEtat" class="form-control" required>
                        <option value="en cours">En cours</option>
                        <option value="validée">Validée</option>
                    </select>
                </div>
                
                <button type="submit" name="modifier_commande" class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour ouvrir le modal de modification
        function openEditModal(id, nom, prenom, tlf, adresse, etat) {
            if (etat !== 'en cours') {
                alert("Seules les commandes 'en cours' peuvent être modifiées");
                return;
            }
            
            document.getElementById('modalCommandeId').value = id;
            document.getElementById('modalCommandeNom').value = nom;
            document.getElementById('modalCommandePrenom').value = prenom;
            document.getElementById('modalCommandeTlf').value = tlf;
            document.getElementById('modalCommandeAdresse').value = adresse;
            document.getElementById('modalCommandeEtat').value = etat;
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        // Fonction pour fermer le modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Fonction de confirmation de suppression
        function confirmDelete(id, etat) {
            if (etat !== 'en cours') {
                alert("Seules les commandes 'en cours' peuvent être supprimées");
                return false;
            }
            return confirm("Êtes-vous sûr de vouloir supprimer la commande #" + id + "?");
        }
        
        // Fermer le modal si on clique en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>