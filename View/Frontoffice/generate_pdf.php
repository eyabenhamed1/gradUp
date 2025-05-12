<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Commande.php');
require_once __DIR__ . '/../../vendor/autoload.php';

// Vérifier si l'ID de commande est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de commande invalide');
}

$id_commande = (int)$_GET['id'];
$commandeModel = new Commande();

// Récupérer les détails de la commande
$commande = $commandeModel->getCommandeById($id_commande);

if (!$commande) {
    die('Commande non trouvée');
}

// Décoder les produits JSON
$produits = json_decode($commande['produits'], true);

// Créer un nouveau PDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 25,
    'margin_bottom' => 20,
    'margin_header' => 10,
    'margin_footer' => 10
]);

// Chemin du logo (dans le même dossier que ce script)
$logoPath = __DIR__ . '/logo.jpg';

// HTML pour le PDF
$html = '
<style>
    body { font-family: Arial, sans-serif; }
    .header { text-align: center; margin-bottom: 20px; }
    .logo { max-width: 150px; margin-bottom: 10px; }
    .title { font-size: 24px; font-weight: bold; margin-bottom: 20px; color: #2c3e50; }
    .info { margin-bottom: 20px; }
    .info-table { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
    .info-table td { padding: 8px; border: 1px solid #ddd; }
    .products-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    .products-table th, .products-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    .products-table th { background-color: #2c3e50; color: white; }
    .total { text-align: right; font-weight: bold; font-size: 18px; margin-top: 20px; }
    .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #7f8c8d; border-top: 1px solid #ddd; padding-top: 10px; }
    .status-badge { padding: 3px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
    .status-en-cours { background-color: #f39c12; color: white; }
    .status-validee { background-color: #2ecc71; color: white; }
</style>

<div class="header">
    <img src="file://' . $logoPath . '" class="logo" alt="Gradup Shop">
    <div class="title">Facture de commande #' . $commande['id_commande'] . '</div>
</div>

<div class="info">
    <table class="info-table">
        <tr>
            <td width="30%"><strong>Date:</strong> ' . date('d/m/Y', strtotime($commande['date_livraison'])) . '</td>
            <td width="35%"><strong>Nom:</strong> ' . htmlspecialchars($commande['nom']) . '</td>
            <td width="35%"><strong>Prénom:</strong> ' . htmlspecialchars($commande['prenom']) . '</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Adresse:</strong> ' . htmlspecialchars($commande['adresse']) . '</td>
        </tr>
    </table>
</div>

<table class="products-table">
    <thead>
        <tr>
            <th width="50%">Produit</th>
            <th width="20%">Prix unitaire</th>
            <th width="15%">Quantité</th>
            <th width="15%">Total</th>
        </tr>
    </thead>
    <tbody>';

foreach ($produits as $produit) {
    $html .= '
        <tr>
            <td>' . htmlspecialchars($produit['name']) . '</td>
            <td>' . number_format($produit['price'], 2, ',', ' ') . ' DT</td>
            <td>' . $produit['quantity'] . '</td>
            <td>' . number_format($produit['price'] * $produit['quantity'], 2, ',', ' ') . ' DT</td>
        </tr>';
}

$html .= '
    </tbody>
</table>

<div class="total">
    Total: ' . number_format($commande['prix_total'], 2, ',', ' ') . ' DT
</div>

<div class="footer">
    <p>Merci pour votre confiance !</p>
    <p>Gradup Shop - ' . date('Y') . '</p>
    <p>Tél: +216 12 345 678 | Email: contact@gradupshop.tn</p>
</div>';

// Configuration du PDF
$mpdf->SetTitle('Commande #' . $commande['id_commande']);
$mpdf->SetAuthor('Gradup Shop');
$mpdf->SetCreator('Gradup Shop System');

// Ajouter le contenu HTML
$mpdf->WriteHTML($html);

// Générer et afficher le PDF
$mpdf->Output('Commande_' . $commande['id_commande'] . '.pdf', 'I');