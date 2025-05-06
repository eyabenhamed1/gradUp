<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/Model/Commande.php');

// Démarrer la session
session_start();

// Vérifier si l'ID de commande est passé
if (!isset($_GET['id'])) {
    die("ID de commande non spécifié");
}

$commandeId = (int)$_GET['id'];
$commandeModel = new Commande();
$commande = $commandeModel->getCommandeById($commandeId);

if (!$commande) {
    die("Commande non trouvée");
}

// Décoder les produits
$produits = json_decode($commande['produits'], true);

// Créer un nouveau PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Informations du document
$pdf->SetCreator('Gradup Shop');
$pdf->SetAuthor('Gradup Shop');
$pdf->SetTitle('Commande #' . $commandeId);
$pdf->SetSubject('Détails de commande');

// Marges
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Ajouter une page
$pdf->AddPage();

// Logo de l'entreprise (ajustez le chemin)
$logoPath = $_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A/assets/images/logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 10, 40, 0, 'PNG');
}

// Titre
$pdf->SetY(40);
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 10, 'Facture de commande #' . $commandeId, 0, 1, 'C');

// Informations client
$pdf->SetFont('helvetica', '', 12);
$pdf->SetY(60);
$pdf->Cell(0, 10, 'Informations client:', 0, 1, 'L');
$pdf->Cell(0, 7, 'Nom: ' . $commande['nom'] . ' ' . $commande['prenom'], 0, 1, 'L');
$pdf->Cell(0, 7, 'Téléphone: ' . $commande['tlf'], 0, 1, 'L');
$pdf->Cell(0, 7, 'Adresse: ' . $commande['adresse'], 0, 1, 'L');
$pdf->Cell(0, 7, 'Date: ' . date('d/m/Y', strtotime($commande['date_commande'])), 0, 1, 'L');

// Tableau des produits
$pdf->SetY(100);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(100, 10, 'Produit', 1, 0, 'C');
$pdf->Cell(30, 10, 'Prix unitaire', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantité', 1, 0, 'C');
$pdf->Cell(30, 10, 'Total', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 10);
foreach ($produits as $produit) {
    $pdf->Cell(100, 10, $produit['name'], 1, 0, 'L');
    $pdf->Cell(30, 10, number_format($produit['price'], 2) . ' DT', 1, 0, 'R');
    $pdf->Cell(30, 10, $produit['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($produit['price'] * $produit['quantity'], 2) . ' DT', 1, 1, 'R');
}

// Total
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(160, 10, 'Total:', 1, 0, 'R');
$pdf->Cell(30, 10, number_format($commande['prix_total'], 2) . ' DT', 1, 1, 'R');

// Message de remerciement
$pdf->SetY($pdf->GetY() + 20);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 10, 'Merci pour votre commande chez Gradup Shop!', 0, 1, 'C');

// Générer le PDF
$pdf->Output('commande_' . $commandeId . '.pdf', 'I');