<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo  = DB::getConnection();
$id   = (int)($_GET['id'] ?? 0);
$proj = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $proj = $stmt->fetch();
    if (!$proj) { redirect('/admin/projects.php'); }
}

$categories = $pdo->query('SELECT * FROM project_categories ORDER BY display_order ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Invalid token.'); redirect('/admin/projects.php');
    }

    $title      = trim($_POST['title']      ?? '');
    $slug       = slugify(trim($_POST['slug'] ?? '') ?: $title);
    $slug       = slug_unique($pdo, 'projects', $slug, $id);
    $catId      = (int)($_POST['category_id'] ?? 0);
    $shortDesc  = trim($_POST['short_desc']  ?? '');
    $liveUrl    = sanitize_url(trim($_POST['live_url']   ?? ''));
    $githubUrl  = sanitize_url(trim($_POST['github_url'] ?? ''));
    $techTags   = trim($_POST['tech_tags']   ?? '');
    $isRobotics = isset($_POST['is_robotics']) ? 1 : 0;
    $order      = (int)($_POST['display_order'] ?? 0);
    $status     = in_array($_POST['status'] ?? '', ['published','draft']) ? $_POST['status'] : 'draft';

    // Handle thumbnail upload
    $thumbUrl = $proj['thumbnail_url'] ?? '';
    if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $file  = $_FILES['thumbnail'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (in_array($mime, ALLOWED_IMAGE_TYPES) && $file['size'] <= UPLOAD_MAX_BYTES) {
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'proj_' . uniqid() . '.' . strtolower($ext);
            $dest     = SITE_ROOT . '/uploads/projects/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $thumbUrl = BASE_URL . '/uploads/projects/' . $filename;
            }
        }
    }

    if ($id) {
        $pdo->prepare(
            'UPDATE projects SET category_id=?, title=?, slug=?, short_desc=?,
             thumbnail_url=?, live_url=?, github_url=?, tech_tags=?,
             is_robotics=?, display_order=?, status=? WHERE id=?'
        )->execute([$catId, $title, $slug, $shortDesc, $thumbUrl, $liveUrl, $githubUrl,
                    $techTags, $isRobotics, $order, $status, $id]);
    } else {
        $pdo->prepare(
            'INSERT INTO projects (category_id, title, slug, short_desc, thumbnail_url,
             live_url, github_url, tech_tags, is_robotics, display_order, status)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([$catId, $title, $slug, $shortDesc, $thumbUrl, $liveUrl, $githubUrl,
                    $techTags, $isRobotics, $order, $status]);
    }

    flash_set('success', $id ? 'Project updated.' : 'Project created.');
    redirect('/admin/projects.php');
}

$adminPageTitle = $id ? 'Edit Project' : 'Add Project';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1><?= $id ? 'Edit Project' : 'Add Project' ?></h1>
    <a href="<?= BASE_URL ?>/admin/projects.php" class="btn-admin-secondary">Cancel</a>
</div>

<div class="admin-card">
<form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="admin-form-grid">
        <div class="form-group">
            <label class="form-label" for="field-title">Title *</label>
            <input type="text" id="field-title" name="title" class="form-input" required
                   value="<?= htmlspecialchars($proj['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="field-slug">Slug</label>
            <input type="text" id="field-slug" name="slug" class="form-input"
                   value="<?= htmlspecialchars($proj['slug'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-form-grid">
        <div class="form-group">
            <label class="form-label" for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-input" required>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($proj['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-input">
                <option value="draft"     <?= ($proj['status'] ?? 'draft') === 'draft'     ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($proj['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="short_desc">Short Description (shown on card, max 500 chars)</label>
        <textarea id="short_desc" name="short_desc" class="form-textarea" rows="3" maxlength="500"><?= htmlspecialchars($proj['short_desc'] ?? '') ?></textarea>
    </div>

    <div class="admin-form-grid">
        <div class="form-group">
            <label class="form-label" for="tech_tags">Tech Tags (comma-separated)</label>
            <input type="text" id="tech_tags" name="tech_tags" class="form-input"
                   placeholder="PHP, MySQL, JavaScript"
                   value="<?= htmlspecialchars($proj['tech_tags'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="display_order">Display Order</label>
            <input type="number" id="display_order" name="display_order" class="form-input" min="0"
                   value="<?= (int)($proj['display_order'] ?? 0) ?>">
        </div>
    </div>

    <div class="admin-form-grid">
        <div class="form-group">
            <label class="form-label" for="live_url">Live URL</label>
            <input type="url" id="live_url" name="live_url" class="form-input"
                   value="<?= htmlspecialchars($proj['live_url'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="github_url">GitHub URL</label>
            <input type="url" id="github_url" name="github_url" class="form-input"
                   value="<?= htmlspecialchars($proj['github_url'] ?? '') ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Thumbnail Image (JPEG/PNG/WebP, max 5 MB)</label>
        <?php if (!empty($proj['thumbnail_url'])): ?>
        <img src="<?= htmlspecialchars($proj['thumbnail_url']) ?>" id="thumb-preview"
             alt="Current thumbnail" style="max-width:200px;border-radius:6px;margin-bottom:0.5rem;display:block;">
        <?php else: ?>
        <img id="thumb-preview" alt="" style="max-width:200px;border-radius:6px;margin-bottom:0.5rem;display:none;">
        <?php endif; ?>
        <input type="file" name="thumbnail" class="form-input" accept="image/*"
               data-preview-target="thumb-preview">
    </div>

    <div class="form-group">
        <label class="form-checkbox-label">
            <input type="checkbox" name="is_robotics" value="1"
                   <?= ($proj['is_robotics'] ?? 0) ? 'checked' : '' ?>>
            Show in Robotics section
        </label>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="btn-admin-primary">
            <?= $id ? 'Update Project' : 'Create Project' ?>
        </button>
        <a href="<?= BASE_URL ?>/admin/projects.php" class="btn-admin-secondary">Cancel</a>
    </div>
</form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
