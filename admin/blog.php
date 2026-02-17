<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo = DB::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Invalid token.');
    } else {
        $pdo->prepare('DELETE FROM blog_posts WHERE id = ?')->execute([(int)$_POST['delete_id']]);
        flash_set('success', 'Post deleted.');
    }
    redirect('/admin/blog.php');
}

$posts = $pdo->query(
    'SELECT id, title, status, published_at, read_time FROM blog_posts ORDER BY created_at DESC'
)->fetchAll();

$adminPageTitle = 'Blog Posts';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1>Blog Posts</h1>
    <a href="<?= BASE_URL ?>/admin/blog-edit.php" class="btn-admin-primary">+ New Post</a>
</div>

<?php if ($posts): ?>
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Read Time</th>
                <th>Published</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= htmlspecialchars($post['title']) ?></td>
                <td class="text-muted"><?= (int)$post['read_time'] ?> min</td>
                <td class="text-muted">
                    <?= $post['published_at'] ? format_date($post['published_at'], 'M j, Y') : '—' ?>
                </td>
                <td>
                    <span class="status-badge status-badge--<?= htmlspecialchars($post['status']) ?>">
                        <?= ucfirst($post['status']) ?>
                    </span>
                </td>
                <td class="admin-actions">
                    <a href="<?= BASE_URL ?>/admin/blog-edit.php?id=<?= $post['id'] ?>" class="btn-admin-sm">Edit</a>
                    <form method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="delete_id" value="<?= $post['id'] ?>">
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
    <p class="admin-empty">No posts yet. <a href="<?= BASE_URL ?>/admin/blog-edit.php">Write your first post</a>.</p>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
