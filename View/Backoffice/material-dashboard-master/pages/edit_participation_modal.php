<?php
require_once 'C:/xampp/htdocs/ProjetWeb2A/configg.php';
$pdo = config::getConnexion();

// Récupérer l'ID de la participation
$id_participation = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_participation) {
    die("ID de participation invalide");
}

// Récupérer les informations de la participation
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        u.name,
        u.email
    FROM participation p
    INNER JOIN user u ON p.id_utilisateur = u.id
    WHERE p.id_participation = ?
");

$stmt->execute([$id_participation]);
$participation = $stmt->fetch();

if (!$participation) {
    die("Participation non trouvée");
}
?>

<form id="editParticipationForm">
    <input type="hidden" name="id_participation" value="<?= htmlspecialchars($participation['id_participation']) ?>">
    
    <div class="mb-3">
        <label class="form-label">Participant</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($participation['name']) ?>" readonly>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($participation['email']) ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($participation['telephone']) ?>">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Commentaire du participant</label>
        <textarea class="form-control" readonly rows="2"><?= htmlspecialchars($participation['commentaire']) ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Commentaire administrateur</label>
        <textarea name="commentaire_admin" class="form-control" rows="3"><?= htmlspecialchars($participation['commentaire_admin']) ?></textarea>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="submit" class="btn bg-gradient-dark">
            <i class="fas fa-save me-2"></i> Enregistrer les modifications
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#editParticipationForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'update_participation.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#editParticipationModal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Erreur lors de la modification: ' + error);
            }
        });
    });
});
</script> 