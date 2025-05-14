<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Commande.php');

// Redirect if user is not logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$commande = new Commande();
// Get commands for the logged-in user
$mesCommandes = $commande->getCommandesByUserId($_SESSION['user']['id']);

// Vérifier les commandes à livrer aujourd'hui (only for the logged-in user)
$commandesAujourdhui = array_filter($mesCommandes, function($cmd) {
    return isset($cmd['date_livraison']) && date('Y-m-d') === date('Y-m-d', strtotime($cmd['date_livraison']));
});
$showNotification = !empty($commandesAujourdhui);

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Verify that the command belongs to the current user
    $commandeToDelete = $commande->getCommandeById($id);
    if ($commandeToDelete && $commandeToDelete['id_user'] == $_SESSION['user']['id']) {
        $result = $commande->supprimerCommande($id);
        
        if ($result === true) {
            $_SESSION['message'] = "Commande #$id supprimée avec succès";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result;
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Vous n'êtes pas autorisé à supprimer cette commande";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: macommande.php");
    exit();
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modifier_commande'])) {
    $id = (int)$_POST['id_commande'];
    
    // Verify that the command belongs to the current user
    $commandeToModify = $commande->getCommandeById($id);
    if ($commandeToModify && $commandeToModify['id_user'] == $_SESSION['user']['id']) {
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
    } else {
        $_SESSION['message'] = "Vous n'êtes pas autorisé à modifier cette commande";
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
    <link rel="stylesheet" href="css/header.css">
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--text);
            font-size: 2.5rem;
            font-weight: 600;
            position: relative;
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .delivery-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #2ecc71;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 3000;
            display: none;
            max-width: 400px;
        }

        .notification-header {
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        .close-notification {
            cursor: pointer;
            font-size: 1.2em;
        }

        .commande-item {
            margin-bottom: 10px;
            padding: 10px;
            background-color: rgba(255,255,255,0.2);
            border-radius: 4px;
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

        .btn-pdf {
            background-color: #e74c3c;
            color: white;
        }

        .btn-pdf:hover {
            background-color: #c0392b;
        }

        .btn-disabled {
            background-color: var(--medium-gray);
            cursor: not-allowed;
            opacity: 0.6;
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

        .no-orders {
            text-align: center;
            padding: 3rem;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .no-orders p {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
            display: inline-block;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-success {
            background: #E8F5E9;
            color: #2E7D32;
            border: 1px solid #A5D6A7;
        }

        .alert-danger {
            background: #FFEBEE;
            color: #C62828;
            border: 1px solid #FFCDD2;
        }

        /* Modal Styles */
        .modal {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
            padding: 2rem;
        }

        .modal-content h2 {
            color: var(--text);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
            outline: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .commandes-table {
                grid-template-columns: 1fr;
            }

            .order-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .order-actions {
                flex-direction: column;
            }

            .modal-content {
                width: 90%;
                margin: 2rem auto;
            }
        }

        /* Scrollbar Styling */
        .products-list::-webkit-scrollbar {
            width: 8px;
        }

        .products-list::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 4px;
        }

        .products-list::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }

        .products-list::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* Header Styles */
        .header {
            background-color: var(--white);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text);
            font-size: 1.8rem;
            font-weight: 700;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: translateY(-2px);
        }

        .logo i {
            color: var(--primary);
            margin-right: 0.8rem;
            font-size: 2rem;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-list {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 0;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .nav-link:hover:after {
            width: 100%;
        }

        .nav-link.active {
            color: var(--primary);
        }

        .nav-link.active:after {
            width: 100%;
        }

        .nav-icons {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-icon {
            color: var(--dark);
            font-size: 1.2rem;
            margin-left: 1.2rem;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            text-decoration: none;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--light);
        }

        .nav-icon:hover {
            color: var(--accent);
            transform: translateY(-2px);
            background: var(--light-gray);
        }

        .logout-link {
            color: var(--white);
            background-color: var(--accent);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .logout-link:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .logout-link i {
            font-size: 1rem;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--accent);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
        }

        /* Mobile menu button */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        /* Responsive header */
        @media (max-width: 992px) {
            .nav-list {
                gap: 1rem;
            }

            .nav-icons {
                gap: 1rem;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-list {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--white);
                flex-direction: column;
                padding: 1rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .nav-list.show {
                display: flex;
            }

            .nav-item {
                width: 100%;
            }

            .nav-link {
                display: block;
                padding: 0.8rem 0;
            }

            .nav {
                gap: 1rem;
            }
        }

        /* Footer Styles */
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

        .footer-col p {
            color: var(--light-gray);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .footer-links {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .footer-link {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--light-gray);
            font-size: 0.9rem;
        }

        .footer-link i {
            color: var(--accent);
            width: 16px;
            text-align: center;
        }

        .footer-link a {
            color: var(--light-gray);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-link a:hover {
            color: var(--accent);
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
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
            text-decoration: none;
            transition: var(--transition);
        }

        .social-link:hover {
            background-color: var(--accent);
            transform: translateY(-3px);
            color: var(--white);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            color: var(--medium-gray);
        }

        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-col h3::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .footer-link {
                justify-content: center;
            }

            .social-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div id="deliveryNotification" class="delivery-notification">
        <div class="notification-header">
            <span>Livraisons prévues aujourd'hui</span>
            <span class="close-notification">&times;</span>
        </div>
        <div id="deliveryCommandsList">
            <?php foreach ($commandesAujourdhui as $cmd): ?>
                <div class="commande-item">
                    <strong>Commande #<?= $cmd['id_commande'] ?></strong><br>
                    Client: <?= htmlspecialchars($cmd['nom']) ?> <?= htmlspecialchars($cmd['prenom']) ?><br>
                    Tél: <?= htmlspecialchars($cmd['tlf']) ?><br>
                    Adresse: <?= htmlspecialchars($cmd['adresse']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

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
            
            <?php if (empty($mesCommandes)): ?>
                <div class="no-orders">
                    <p>Vous n'avez pas encore de commandes.</p>
                    <a href="essaiee.php" class="btn-primary">Commencer vos achats</a>
                </div>
            <?php else: ?>
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
                        if (!empty($mesCommandes)) {
                            foreach ($mesCommandes as $cmd) {
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
                                    <td class="total-price"><?= number_format($cmd['prix_total'], 2) ?> DT</td>
                                    <td>
                                        <span class="status-badge status-<?= str_replace(' ', '-', $cmd['etat']) ?>">
                                            <?= ucfirst($cmd['etat']) ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="?action=supprimer&id=<?= $cmd['id_commande'] ?>" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')"
                                           class="btn btn-delete <?= !$isEditable ? 'btn-disabled' : '' ?>"
                                           <?= !$isEditable ? 'title="Seulement pour commandes en cours"' : '' ?>>
                                            <i class="fas fa-trash-alt"></i> Supprimer
                                        </a>
                                        <a href="generate_pdf.php?id=<?= $cmd['id_commande'] ?>" class="btn btn-pdf" target="_blank">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="9" class="empty-message">Aucune commande trouvée</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

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
                    <li class="footer-link"><a href="index.php">hh</a></li>
                    <li class="footer-link"><a href="boutique.php">Boutique</a></li>
                    <li class="footer-link"><a href="nouveautes.php">e-learning</a></li>
                    <li class="footer-link"><a href="promotions.php">evenement</a></li>
                    <li class="footer-link"><a href="contact.php">certificat</a></li>
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
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($showNotification): ?>
            const notification = document.getElementById('deliveryNotification');
            if (notification) {
                notification.style.display = 'block';

                // Fermer la notification
                document.querySelector('.close-notification')?.addEventListener('click', function() {
                    notification.style.display = 'none';
                });

                // Fermer automatiquement après 10 secondes
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 10000);
            }
        <?php endif; ?>
    });

    // Fonction de confirmation de suppression
    function confirmDelete(id, etat) {
        if (etat !== 'en cours') {
            alert("Seules les commandes 'en cours' peuvent être supprimées");
            return false;
        }
        return confirm("Êtes-vous sûr de vouloir supprimer la commande #" + id + "?");
    }

    // Mobile menu toggle
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('navList').classList.toggle('show');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const nav = document.querySelector('.nav');
        const navList = document.getElementById('navList');
        const menuToggle = document.getElementById('menuToggle');
        
        if (!nav.contains(event.target) && navList.classList.contains('show')) {
            navList.classList.remove('show');
        }
    });

    // Add smooth scroll padding for fixed header
    document.documentElement.style.setProperty('scroll-padding-top', '80px');
    </script>
</body>
</html>