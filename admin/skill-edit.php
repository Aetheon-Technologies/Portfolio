<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo   = DB::getConnection();
$id    = (int)($_GET['id']  ?? 0);
$defCat = (int)($_GET['cat'] ?? 0);
$skill = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM skills WHERE id = ?');
    $stmt->execute([$id]);
    $skill = $stmt->fetch();
    if (!$skill) { redirect('/admin/skills.php'); }
}

$categories = $pdo->query('SELECT * FROM skill_categories ORDER BY display_order ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Invalid token.'); redirect('/admin/skills.php');
    }

    $name    = trim($_POST['name']       ?? '');
    $catId   = (int)($_POST['category_id'] ?? 0);
    $iconUrl = trim($_POST['icon_url']   ?? '');
    $order   = (int)($_POST['display_order'] ?? 0);

    if ($id) {
        $pdo->prepare('UPDATE skills SET category_id=?, name=?, icon_url=?, display_order=? WHERE id=?')
            ->execute([$catId, $name, $iconUrl, $order, $id]);
    } else {
        $pdo->prepare('INSERT INTO skills (category_id, name, icon_url, display_order) VALUES (?,?,?,?)')
            ->execute([$catId, $name, $iconUrl, $order]);
    }

    flash_set('success', $id ? 'Skill updated.' : 'Skill added.');
    redirect('/admin/skills.php');
}

$adminPageTitle = $id ? 'Edit Skill' : 'Add Skill';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1><?= $id ? 'Edit Skill' : 'Add Skill' ?></h1>
    <a href="<?= BASE_URL ?>/admin/skills.php" class="btn-admin-secondary">Cancel</a>
</div>

<div class="admin-card">
<form method="POST">
    <?= csrf_field() ?>

    <div class="admin-form-grid">
        <div class="form-group">
            <label class="form-label" for="field-title">Skill Name *</label>
            <input type="text" id="field-title" name="name" class="form-input" required
                   value="<?= htmlspecialchars($skill['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-input" required>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= ($skill['category_id'] ?? $defCat) == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="admin-form-grid">
        <div class="form-group">
            <label class="form-label" for="icon_url">Icon URL (optional, e.g. devicons CDN)</label>
            <input type="url" id="icon_url" name="icon_url" class="form-input"
                   value="<?= htmlspecialchars($skill['icon_url'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="display_order">Display Order</label>
            <input type="number" id="display_order" name="display_order" class="form-input" min="0"
                   value="<?= (int)($skill['display_order'] ?? 0) ?>">
        </div>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="btn-admin-primary"><?= $id ? 'Update Skill' : 'Add Skill' ?></button>
        <a href="<?= BASE_URL ?>/admin/skills.php" class="btn-admin-secondary">Cancel</a>
    </div>
</form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
