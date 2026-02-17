<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo = DB::getConnection();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Invalid token.');
    } else {
        $pdo->prepare('DELETE FROM projects WHERE id = ?')->execute([(int)$_POST['delete_id']]);
        flash_set('success', 'Project deleted.');
    }
    redirect('/admin/projects.php');
}

$projects = $pdo->query(
    'SELECT p.*, pc.name AS cat_name FROM projects p
     JOIN project_categories pc ON p.category_id = pc.id
     ORDER BY p.display_order ASC, p.created_at DESC'
)->fetchAll();

$adminPageTitle = 'Projects';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1>Projects</h1>
    <a href="<?= BASE_URL ?>/admin/project-edit.php" class="btn-admin-primary">+ Add Project</a>
</div>

<?php if ($projects): ?>
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Robotics</th>
                <th>Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($p['cat_name']) ?></td>
                <td><?= $p['is_robotics'] ? '<span class="status-badge status-badge--published">Yes</span>' : '—' ?></td>
                <td class="text-muted"><?= (int)$p['display_order'] ?></td>
                <td>
                    <span class="status-badge status-badge--<?= htmlspecialchars($p['status']) ?>">
                        <?= ucfirst($p['status']) ?>
                    </span>
                </td>
                <td class="admin-actions">
                    <a href="<?= BASE_URL ?>/admin/project-edit.php?id=<?= $p['id'] ?>" class="btn-admin-sm">Edit</a>
                    <form method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn-admin-sm btn-admin-danger btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="admin-card">
    <p class="admin-empty">No projects yet. <a href="<?= BASE_URL ?>/admin/project-edit.php">Add your first project</a>.</p>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
