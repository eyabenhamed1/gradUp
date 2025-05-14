<?php
session_start();
require_once 'C:/xampp/htdocs/try/ProjetWeb2A/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit();
}

$conn = config::getConnexion();

// Traitement mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id    = intval($_POST['id']);
    $name  = $_POST['name']  ?? '';
    $email = $_POST['email'] ?? '';
    $role  = $_POST['role']  ?? '';

    try {
        $stmt = $conn->prepare(
          "UPDATE user SET name = :name, email = :email, role = :role WHERE id = :id"
        );
        $stmt->execute([
          ':name'  => $name,
          ':email' => $email,
          ':role'  => $role,
          ':id'    => $id
        ]);
        $_SESSION['success_message'] = "Utilisateur mis à jour avec succès";
        header("Location: tables.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur de mise à jour : " . $e->getMessage();
    }
}

// Utilisateurs actifs
$sql = "SELECT id, name, email, role, deleted_at 
        FROM user 
        WHERE deleted_at IS NULL 
        ORDER BY name ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Utilisateurs supprimés
$sqlDeleted = "SELECT id, name, email, role, deleted_at 
               FROM user 
               WHERE deleted_at IS NOT NULL 
               ORDER BY deleted_at DESC";
$stmtDeleted = $conn->prepare($sqlDeleted);
$stmtDeleted->execute();
$deletedUsers = $stmtDeleted->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestion Utilisateurs – GradUp</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --sidebar-bg: #2f3e4e;
      --sidebar-text: #fff;
      --accent: #ff8c42;
    }
    body {
      background-color: #f4f6f9;
    }
    .sidebar {
      width: 220px;
      background: var(--sidebar-bg);
      color: var(--sidebar-text);
      padding: 2rem 1rem;
      position: fixed;
      height: 100vh;
    }
    .sidebar .brand {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 2rem;
      text-align: center;
    }
    .sidebar nav a {
      display: flex;
      align-items: center;
      padding: .75rem 1rem;
      border-radius: 12px;
      color: var(--sidebar-text);
      text-decoration: none;
      margin-bottom: .5rem;
    }
    .sidebar nav a.active,
    .sidebar nav a:hover {
      background: var(--accent);
    }
    .main-content {
      margin-left: 220px;
      padding: 2rem;
      width: calc(100% - 220px);
    }
    .card-header {
      background-color: var(--accent);
      color: #fff;
    }
    .deleted-row {
      background-color: #f8d7da;
    }
    th.sortable {
      cursor: pointer;
    }
    th.sortable:after {
      content: ' ⇅';
      font-size: .8em;
      color: #888;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
<div class="sidebar">
  <div class="brand">GradUp</div>
  <nav>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
    <a href="profile.php"><i class="fas fa-user-circle me-2"></i> Profil</a>
    <a href="tables.php" class="active"><i class="fas fa-users me-2"></i> Utilisateurs</a>
    <a href="../../../Frontoffice/auth/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a>
  </nav>
</div>


  <!-- Main Content -->
  <div class="main-content">
    <?php if (!empty($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['success_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestion des Utilisateurs</h5>
        <!-- Bouton Trier par lettre -->
        <button class="btn btn-light text-dark" data-bs-toggle="modal" data-bs-target="#alphabetModal">
          Trier par lettre
        </button>
      </div>
      <div class="card-body">
        <!-- Barre de recherche -->
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Rechercher un utilisateur…">

        <!-- Utilisateurs actifs -->
        <h6>Utilisateurs Actifs</h6>
        <div class="table-responsive mb-4">
          <table id="activeTable" class="table table-hover">
            <thead>
              <tr>
                <th class="sortable" onclick="sortTable('activeTable', 0)">Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
              <tr>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal"
                          data-id="<?= $u['id'] ?>"
                          data-name="<?= htmlspecialchars($u['name']) ?>"
                          data-email="<?= htmlspecialchars($u['email']) ?>"
                          data-role="<?= htmlspecialchars($u['role']) ?>">
                    Modifier
                  </button>
                  <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('Confirmer la suppression ?')">
                    Supprimer
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Historique des utilisateurs supprimés -->
        <h6>Historique des Utilisateurs Supprimés</h6>
        <div class="table-responsive">
          <table id="deletedTable" class="table table-hover">
            <thead>
              <tr>
                <th class="sortable" onclick="sortTable('deletedTable', 0)">Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Date Suppression</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($deletedUsers as $du): ?>
              <tr class="deleted-row">
                <td><?= htmlspecialchars($du['name']) ?></td>
                <td><?= htmlspecialchars($du['email']) ?></td>
                <td><?= htmlspecialchars($du['role']) ?></td>
                <td><?= htmlspecialchars($du['deleted_at']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Trier par lettre -->
  <div class="modal fade" id="alphabetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Trier par lettre</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="letterSelect" class="form-label">Choisir une lettre</label>
            <select id="letterSelect" class="form-select">
              <option value="">-- Toutes --</option>
              <?php foreach (range('A','Z') as $letter): ?>
                <option value="<?= $letter ?>"><?= $letter ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="table-responsive" style="max-height:300px; overflow:auto;">
            <table class="table table-striped" id="alphaTable">
              <thead><tr><th>Nom</th></tr></thead>
              <tbody><!-- rempli en JS --></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Édition -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modifier l'utilisateur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="modalUserId">
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="name" id="modalName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="modalEmail" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select name="role" id="modalRole" class="form-select" required>
              <option value="admin">Admin</option>
              <option value="user">User</option>
              <option value="editor">Editor</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" name="update_user" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS + Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Recherche en temps réel
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      document.querySelectorAll('#activeTable tbody tr, #deletedTable tbody tr')
        .forEach(tr => tr.style.display = tr.textContent.toLowerCase().includes(filter) ? '' : 'none');
    });

    // Tri générique
    function sortTable(tableId, colIndex) {
      const tb = document.getElementById(tableId).querySelector('tbody');
      const rows = Array.from(tb.rows);
      const asc  = tb.getAttribute('data-sort-dir') !== 'asc';
      rows.sort((a, b) => {
        const x = a.cells[colIndex].textContent.trim().toLowerCase();
        const y = b.cells[colIndex].textContent.trim().toLowerCase();
        return asc ? x.localeCompare(y) : y.localeCompare(x);
      });
      rows.forEach(r => tb.appendChild(r));
      tb.setAttribute('data-sort-dir', asc ? 'asc' : 'desc');
    }

    // Modal "Trier par lettre"
    const alphaModal = document.getElementById('alphabetModal');
    alphaModal.addEventListener('show.bs.modal', () => {
      const names = Array.from(document.querySelectorAll('#activeTable tbody tr td:first-child'))
                         .map(td => td.textContent.trim())
                         .sort((a,b) => a.localeCompare(b, 'fr'));
      const tbody = document.querySelector('#alphaTable tbody');
      tbody.innerHTML = names.map(n => `<tr><td>${n}</td></tr>`).join('');
      document.getElementById('letterSelect').value = '';
    });
    document.getElementById('letterSelect').addEventListener('change', function() {
      const l = this.value;
      document.querySelectorAll('#alphaTable tbody tr').forEach(tr => {
        tr.style.display = (!l || tr.cells[0].textContent.charAt(0).toUpperCase() === l) ? '' : 'none';
      });
    });

    // Modal d'édition
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', e => {
      const btn = e.relatedTarget;
      document.getElementById('modalUserId').value = btn.getAttribute('data-id');
      document.getElementById('modalName').value   = btn.getAttribute('data-name');
      document.getElementById('modalEmail').value  = btn.getAttribute('data-email');
      document.getElementById('modalRole').value   = btn.getAttribute('data-role');
    });
  </script>
</body>
</html>
