<?php
require_once 'C:/xampp/htdocs/projettt/projettt/ProjetWeb2A/Config.php';

$pdo = DB::getConnexion();

// Récupération de la participation à modifier
$stmt = $pdo->prepare("
    SELECT p.*, e.nom, e.prenom 
    FROM participation p
    JOIN etudiant e ON p.id_etudiant = e.id_etudiant
    WHERE p.id_participation = ?
");
$stmt->execute([$_GET['id']]);
$participation = $stmt->fetch();

if (!$participation) {
    die("Participation non trouvée");
}
?>

<form id="editParticipationForm" method="post" action="update_participation.php">
    <input type="hidden" name="id_participation" value="<?= $participation['id_participation'] ?>">
    
    <div class="mb-3">
        <label class="form-label">Étudiant</label>
        <input type="text" class="form-control" 
               value="<?= htmlspecialchars($participation['prenom'] . ' ' . $participation['nom']) ?>" 
               readonly>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Statut</label>
        <select name="status" class="form-select">
            <option value="confirmé" <?= $participation['statut'] === 'confirmé' ? 'selected' : '' ?>>Confirmé</option>
            <option value="en attente" <?= $participation['statut'] === 'en attente' ? 'selected' : '' ?>>En attente</option>
            <option value="annulé" <?= $participation['statut'] === 'annulé' ? 'selected' : '' ?>>Annulé</option>
        </select>
    </div>
    
    <div class="d-grid gap-2">
        <button type="submit" class="btn bg-gradient-dark">Enregistrer</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#editParticipationForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                location.reload(); // Recharger la page après mise à jour
            },
            error: function(xhr, status, error) {
                alert('Erreur lors de la mise à jour');
            }
        });
    });
});
</script>