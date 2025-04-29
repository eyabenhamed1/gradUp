<?php
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

// Gestion du panier en session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartCount = array_reduce($_SESSION['cart'], function($carry, $item) {
    return $carry + $item['quantity'];
}, 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - Gradup Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --primary-light: #34495e;
            --primary-dark: #1a252f;
            --secondary: #7f8c8d;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --light-gray: #bdc3c7;
            --medium-gray: #95a5a6;
            --dark: #2c3e50;
            --dark-gray: #34495e;
            --white: #ffffff;
            --black: #000000;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background-color: var(--white);
            color: var(--dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        /* Navigation */
        .nav {
            display: flex;
            align-items: center;
        }
        
        .nav-list {
            display: flex;
            list-style: none;
        }
        
        .nav-item {
            margin-left: 1.5rem;
            position: relative;
        }
        
        .nav-link {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
        }
        
        .nav-link:hover {
            color: var(--accent);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent);
            transition: var(--transition);
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .nav-icons {
            display: flex;
            align-items: center;
            margin-left: 2rem;
        }
        
        .nav-icon {
            color: var(--dark);
            font-size: 1.2rem;
            margin-left: 1.2rem;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            text-decoration: none;
        }
        
        .nav-icon:hover {
            color: var(--accent);
            transform: translateY(-2px);
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--accent);
            color: var(--white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Main Content */
        .main {
            padding: 2rem 0;
            min-height: calc(100vh - 120px);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .page-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 0.5rem;
            text-align: center;
            font-weight: 600;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--accent);
            border-radius: 2px;
        }
        
        /* Table Styles */
        .commandes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        
        .commandes-table th, 
        .commandes-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .commandes-table th {
            background-color: var(--primary);
            color: var(--white);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        .commandes-table tr:nth-child(even) {
            background-color: rgba(236, 240, 241, 0.5);
        }
        
        .commandes-table tr:hover {
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
            background-color: var(--accent);
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #c0392b;
        }
        
        .btn-delete {
            background-color: var(--primary-dark);
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #1a252f;
        }
        
        .btn-disabled {
            background-color: var(--medium-gray);
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: var(--medium-gray);
            font-style: italic;
        }
        
        .total-price {
            font-weight: 600;
            color: var(--accent);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-en-cours {
            background-color: #f39c12;
            color: white;
        }
        
        .status-validee {
            background-color: #2ecc71;
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
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Produits Styles */
        .produits-list {
            max-height: 150px;
            overflow-y: auto;
            padding: 5px;
            border: 1px solid #eee;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        
        .produit-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .produit-item:last-child {
            border-bottom: none;
        }
        
        .produit-name {
            font-weight: 500;
        }
        
        .produit-details {
            font-size: 0.9em;
            color: #666;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 25px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
        }
        
        .close-modal {
            color: var(--medium-gray);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .close-modal:hover {
            color: var(--accent);
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
            padding: 10px;
            border: 1px solid var(--light-gray);
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(44, 62, 80, 0.2);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%232c3e50' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px 12px;
            padding-right: 30px;
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark);
            color: var(--white);
            padding: 3rem 0 1.5rem;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .footer-col h3 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
            color: var(--white);
        }
        
        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--accent);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-link {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-link a {
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }
        
        .footer-link a:hover {
            color: var(--accent);
            padding-left: 5px;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-link {
            color: var(--white);
            background-color: rgba(255, 255, 255, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .social-link:hover {
            background-color: var(--accent);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            color: var(--medium-gray);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                padding: 1rem;
            }
            
            .nav {
                width: 100%;
                margin-top: 1rem;
                justify-content: space-between;
            }
            
            .nav-list {
                display: none;
            }
            
            .nav-icons {
                margin-left: auto;
            }
            
            .commandes-table {
                display: block;
                overflow-x: auto;
            }
            
            .footer-container {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Gradup Shop
            </a>
            
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="index.php" class="nav-link">Accueil</a></li>
                    <li class="nav-item"><a href="essaiee.php" class="nav-link active">Boutique</a></li>
                    <li class="nav-item"><a href="macommande.php" class="nav-link">Ma Commande</a></li>
                    <li class="nav-item"><a href="promotions.php" class="nav-link">Promotions</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
                </ul>
                
                <div class="nav-icons">
                    <a href="#" class="nav-icon"><i class="fas fa-search"></i></a>
                    <a href="evenements.php" class="nav-icon" title="Événements"><i class="fas fa-calendar-alt"></i></a>
                    <a href="certificats.php" class="nav-icon" title="Certificats"><i class="fas fa-certificate"></i></a>
                    <a href="quiz.php" class="nav-icon" title="Quiz"><i class="fas fa-question-circle"></i></a>
                    <a href="compte.php" class="nav-icon" title="Mon compte"><i class="fas fa-user"></i></a>
                    <a href="#" class="nav-icon" id="cartIcon" title="Panier">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-count"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <h1 class="page-title">Mes Commandes</h1>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                    <?= $_SESSION['message'] ?>
                </div>
                <?php 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            <?php endif; ?>
            
            <table class="commandes-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOM</th>
                        <th>PRENOM</th>
                        <th>TÉLÉPHONE</th>
                        <th>ADRESSE</th>
                        <th>PRODUITS</th>
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
                            $produits = json_decode($cmd['produits'], true);
                            ?>
                            <tr>
                                <td><?= $cmd['id_commande'] ?></td>
                                <td><?= htmlspecialchars($cmd['nom']) ?></td>
                                <td><?= htmlspecialchars($cmd['prenom']) ?></td>
                                <td><?= htmlspecialchars($cmd['tlf']) ?></td>
                                <td><?= htmlspecialchars($cmd['adresse']) ?></td>
                                <td>
                                    <div class="produits-list">
                                        <?php if (is_array($produits) && !empty($produits)): ?>
                                            <?php foreach ($produits as $produit): ?>
                                                <div class="produit-item">
                                                    <span class="produit-name"><?= htmlspecialchars($produit['name'] ?? '') ?></span>
                                                    <span class="produit-details">
                                                        <?= $produit['quantity'] ?? 0 ?> x <?= $produit['price'] ?? 0 ?> DT
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div>Aucun produit</div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="total-price"><?= $cmd['prix_total'] ?> DT</td>
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
                        echo '<tr><td colspan="9" class="empty-message">Aucune commande trouvée dans la base de données</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal de modification -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
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
                    <select class="form-control" name="etat" id="modalCommandeEtat" required>
                        <option value="en cours">En cours</option>
                        <option value="validée">Validée</option>
                    </select>
                </div>
                
                <button type="submit" name="modifier_commande" class="btn btn-edit" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <h3>Gradup Shop</h3>
                <p>Votre boutique en ligne préférée pour des produits de qualité à des prix imbattables.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h3>Liens rapides</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="index.php">Accueil</a></li>
                    <li class="footer-link"><a href="boutique.php">Boutique</a></li>
                    <li class="footer-link"><a href="nouveautes.php">Nouveautés</a></li>
                    <li class="footer-link"><a href="promotions.php">Promotions</a></li>
                    <li class="footer-link"><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Informations</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="a-propos.php">À propos de nous</a></li>
                    <li class="footer-link"><a href="livraison.php">Livraison</a></li>
                    <li class="footer-link"><a href="retours.php">Politique de retour</a></li>
                    <li class="footer-link"><a href="conditions.php">Conditions générales</a></li>
                    <li class="footer-link"><a href="confidentialite.php">Politique de confidentialité</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Contactez-nous</h3>
                <ul class="footer-links">
                    <li class="footer-link"><i class="fas fa-map-marker-alt"></i> 123 Rue de la République, Tunis</li>
                    <li class="footer-link"><i class="fas fa-phone"></i> +216 12 345 678</li>
                    <li class="footer-link"><i class="fas fa-envelope"></i> contact@gradupshop.tn</li>
                    <li class="footer-link"><i class="fas fa-clock"></i> Lun-Ven: 9h-18h</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Gradup Shop. Tous droits réservés.</p>
        </div>
    </footer>

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