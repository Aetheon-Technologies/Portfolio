<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo  = DB::getConnection();
$id   = (int)($_GET['id'] ?? 0);
$post = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) { redirect('/admin/blog.php'); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Invalid token.'); redirect('/admin/blog.php');
    }

    $title     = trim($_POST['title']     ?? '');
    $slug      = slugify(trim($_POST['slug'] ?? '') ?: $title);
    $slug      = slug_unique($pdo, 'blog_posts', $slug, $id);
    $excerpt   = trim($_POST['excerpt']   ?? '');
    $body      = $_POST['body']           ?? '';   // HTML, trusted admin input
    $readTime  = max(1, (int)($_POST['read_time'] ?? 5));
    $tags      = trim($_POST['tags']      ?? '');
    $status    = in_array($_POST['status'] ?? '', ['published','draft']) ? $_POST['status'] : 'draft';
    $publishedAt = trim($_POST['published_at'] ?? '');
    $publishedAt = $publishedAt ?: ($status === 'published' ? date('Y-m-d H:i:s') : null);

    // Featured image upload
    $featImg = $post['featured_image'] ?? '';
    if (!empty($_FILES['featured_image']['name']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $file  = $_FILES['featured_image'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (in_array($mime, ALLOWED_IMAGE_TYPES) && $file['size'] <= UPLOAD_MAX_BYTES) {
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'blog_' . uniqid() . '.' . strtolower($ext);
            $dest     = SITE_ROOT . '/uploads/blog/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $featImg = BASE_URL . '/uploads/blog/' . $filename;
            }
        }
    }

    if ($id) {
        $pdo->prepare(
            'UPDATE blog_posts SET title=?, slug=?, excerpt=?, body=?, featured_image=?,
             read_time=?, tags=?, status=?, published_at=? WHERE id=?'
        )->execute([$title, $slug, $excerpt, $body, $featImg, $readTime, $tags, $status, $publishedAt, $id]);
    } else {
        $pdo->prepare(
            'INSERT INTO blog_posts (title, slug, excerpt, body, featured_image, read_time, tags, status, published_at)
             VALUES (?,?,?,?,?,?,?,?,?)'
        )->execute([$title, $slug, $excerpt, $body, $featImg, $readTime, $tags, $status, $publishedAt]);
    }

    flash_set('success', $id ? 'Post updated.' : 'Post created.');
    redirect('/admin/blog.php');
}

$adminPageTitle = $id ? 'Edit Post' : 'New Post';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1><?= $id ? 'Edit Post' : 'New Post' ?></h1>
    <a href="<?= BASE_URL ?>/admin/blog.php" class="btn-admin-secondary">Cancel</a>
</div>

<div class="admin-card">
<form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="admin-form-grid">
        <div class="form-group">
            <label class="form-label" for="field-title">Title *</label>
            <input type="text" id="field-title" name="title" class="form-input" required
                   value="<?= htmlspecialchars($post['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="field-slug">Slug</label>
            <input type="text" id="field-slug" name="slug" class="form-input"
                   value="<?= htmlspecialchars($post['slug'] ?? '') ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="excerpt">Excerpt (shown on blog cards, max 600 chars)</label>
        <textarea id="excerpt" name="excerpt" class="form-textarea" rows="3" maxlength="600"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label class="form-label" for="body">Post Body (HTML)</label>
        <textarea id="body" name="body" class="form-textarea" rows="20" style="font-family:var(--mono);font-size:0.85rem;"><?= htmlspecialchars($post['body'] ?? '') ?></textarea>
        <small style="color:var(--adm-muted);">Standard HTML tags accepted. Use &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;code&gt;, &lt;pre&gt;, etc.</small>
    </div>

    <div class="admin-form-grid admin-form-grid--4">
        <div class="form-group">
            <label class="form-label" for="tags">Tags (comma-separated)</label>
            <input type="text" id="tags" name="tags" class="form-input"
                   placeholder="PHP, Robotics, Arduino"
                   value="<?= htmlspecialchars($post['tags'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="read_time">Read Time (minutes)</label>
            <input type="number" id="read_time" name="read_time" class="form-input" min="1" max="99"
                   value="<?= (int)($post['read_time'] ?? 5) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-input">
                <option value="draft"     <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($post['status'] ?? '') === 'published'   ? 'selected' : '' ?>>Published</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="published_at">Publish Date</label>
            <input type="datetime-local" id="published_at" name="published_at" class="form-input"
                   value="<?= htmlspecialchars(
                       $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : date('Y-m-d\TH:i')
                   ) ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Featured Image (JPEG/PNG/WebP, max 5 MB)</label>
        <?php if (!empty($post['featured_image'])): ?>
        <img src="<?= htmlspecialchars($post['featured_image']) ?>" id="blog-img-preview"
             alt="Current featured image" style="max-width:300px;border-radius:6px;margin-bottom:0.5rem;display:block;">
        <?php else: ?>
        <img id="blog-img-preview" alt="" style="max-width:300px;border-radius:6px;margin-bottom:0.5rem;display:none;">
        <?php endif; ?>
        <input type="file" name="featured_image" class="form-input" accept="image/*"
               data-preview-target="blog-img-preview">
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="btn-admin-primary"><?= $id ? 'Update Post' : 'Create Post' ?></button>
        <a href="<?= BASE_URL ?>/admin/blog.php" class="btn-admin-secondary">Cancel</a>
    </div>
</form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
