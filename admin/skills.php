<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo = DB::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Invalid token.');
    } else {
        $pdo->prepare('DELETE FROM skills WHERE id = ?')->execute([(int)$_POST['delete_id']]);
        flash_set('success', 'Skill deleted.');
    }
    redirect('/admin/skills.php');
}

$categories = $pdo->query('SELECT * FROM skill_categories ORDER BY display_order ASC')->fetchAll();
$skills     = $pdo->query(
    'SELECT s.*, sc.name AS cat_name FROM skills s
     JOIN skill_categories sc ON s.category_id = sc.id
     ORDER BY s.category_id, s.display_order ASC'
)->fetchAll();

$byCategory = [];
foreach ($skills as $skill) {
    $byCategory[$skill['category_id']][] = $skill;
}

$adminPageTitle = 'Skills';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1>Skills</h1>
    <a href="<?= BASE_URL ?>/admin/skill-edit.php" class="btn-admin-primary">+ Add Skill</a>
</div>

<?php foreach ($categories as $cat): ?>
<div class="admin-card" style="margin-bottom:1.5rem;">
    <div class="admin-card__header">
        <h2><?= htmlspecialchars($cat['name']) ?></h2>
        <a href="<?= BASE_URL ?>/admin/skill-edit.php?cat=<?= $cat['id'] ?>" class="btn-admin-sm">+ Add</a>
    </div>

    <?php $catSkills = $byCategory[$cat['id']] ?? []; ?>
    <?php if ($catSkills): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Order</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($catSkills as $skill): ?>
            <tr>
                <td>
                    <?php if ($skill['icon_url']): ?>
                    <img src="<?= htmlspecialchars($skill['icon_url']) ?>" alt="" width="16" height="16" style="vertical-align:middle;margin-right:4px;">
                    <?php endif; ?>
                    <?= htmlspecialchars($skill['name']) ?>
                </td>
                <td class="text-muted"><?= (int)$skill['display_order'] ?></td>
                <td class="admin-actions">
                    <a href="<?= BASE_URL ?>/admin/skill-edit.php?id=<?= $skill['id'] ?>" class="btn-admin-sm">Edit</a>
                    <form method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="delete_id" value="<?= $skill['id'] ?>">
                        <button type="submit" class="btn-admin-sm btn-admin-danger btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="admin-empty">No skills in this category.</p>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
