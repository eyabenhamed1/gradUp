<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'projetweb2a';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

// Traitement de la modification de statut
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_commande'])) {
    $id = $_POST['id_commande'];
    $nouvel_etat = $_POST['etat'];
    
    $stmt = $db->prepare("UPDATE commande SET etat = ? WHERE id_commande = ?");
    $stmt->execute([$nouvel_etat, $id]);
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Récupération des commandes
$query = "SELECT * FROM commande ORDER BY id_commande DESC";
$stmt = $db->query($query);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .commandes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .commandes-table th {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        .commandes-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .commandes-table tr:nth-child(even) {
            background-color: var(--light-gray);
        }
        
        .commandes-table tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-modifier {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-modifier:hover {
            background-color: #27ae60;
        }
        
        .status-select {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            margin-right: 10px;
            font-size: 14px;
        }
        
        .inline-form {
            display: inline-block;
            margin: 0;
        }
        
        .price {
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
        
        .products-link-section {
            margin: 30px 0;
            text-align: center;
        }
        
        .products-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .products-link:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .products-link i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-shopping-cart"></i> Gestion des Commandes</h1>
        
        <table class="commandes-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOM</th>
                    <th>PRENOM</th>
                    <th>TÉLÉPHONE</th>
                    <th>ADRESSE</th>
                    <th>TOTAL</th>
                    <th>STATUT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                <tr>
                    <td><?php echo htmlspecialchars($commande['id_commande']); ?></td>
                    <td><?php echo htmlspecialchars($commande['nom']); ?></td>
                    <td><?php echo htmlspecialchars($commande['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($commande['tlf']); ?></td>
                    <td><?php echo htmlspecialchars($commande['adresse']); ?></td>
                    <td class="price"><?php echo number_format($commande['prix_total'], 2); ?> €</td>
                    <td>
                        <form method="post" class="inline-form">
                            <input type="hidden" name="id_commande" value="<?php echo $commande['id_commande']; ?>">
                            <select name="etat" class="status-select">
                                <option value="en cours" <?php echo $commande['etat'] == 'en cours' ? 'selected' : ''; ?>>En cours</option>
                                <option value="validée" <?php echo $commande['etat'] == 'validée' ? 'selected' : ''; ?>>Validée</option>
                            </select>
                            <button type="submit" class="btn btn-modifier"><i class="fas fa-check"></i> Modifier</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="products-link-section">
            <a href="produit.php" class="products-link">
                <i class="fas fa-boxes"></i> produits
            </a>
        </div>
    </div>
</body>
</html>