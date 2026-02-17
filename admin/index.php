<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo = DB::getConnection();

$stats = [
    'projects'  => (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE status='published'")->fetchColumn(),
    'blog'      => (int)$pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status='published'")->fetchColumn(),
    'messages'  => (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0 AND is_archived=0")->fetchColumn(),
    'skills'    => (int)$pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn(),
];

$recentMessages = $pdo->query(
    "SELECT * FROM contact_messages WHERE is_archived=0 ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

$recentProjects = $pdo->query(
    "SELECT p.*, pc.name AS cat_name FROM projects p
     JOIN project_categories pc ON p.category_id = pc.id
     ORDER BY p.created_at DESC LIMIT 5"
)->fetchAll();

$adminPageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1>Dashboard</h1>
    <p class="admin-page-subtitle">Welcome back, <?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?>.</p>
</div>

<!-- Stats Grid -->
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['projects'] ?></div>
        <div class="admin-stat-label">Published Projects</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['blog'] ?></div>
        <div class="admin-stat-label">Published Posts</div>
    </div>
    <div class="admin-stat-card <?= $stats['messages'] > 0 ? 'admin-stat-card--alert' : '' ?>">
        <div class="admin-stat-value"><?= $stats['messages'] ?></div>
        <div class="admin-stat-label">Unread Messages</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['skills'] ?></div>
        <div class="admin-stat-label">Skills Listed</div>
    </div>
</div>

<div class="admin-dashboard-grid">
    <!-- Recent Messages -->
    <section class="admin-card">
        <div class="admin-card__header">
            <h2>Recent Messages</h2>
            <a href="<?= BASE_URL ?>/admin/messages.php">View all &rarr;</a>
        </div>
        <?php if ($recentMessages): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentMessages as $msg): ?>
                <tr class="<?= !$msg['is_read'] ? 'row-unread' : '' ?>">
                    <td><?= htmlspecialchars($msg['name']) ?></td>
                    <td><?= htmlspecialchars($msg['subject'] ?: '(no subject)') ?></td>
                    <td class="text-muted"><?= format_date($msg['created_at'], 'M j') ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/admin/messages.php?id=<?= $msg['id'] ?>" class="btn-admin-sm">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="admin-empty">No messages yet.</p>
        <?php endif; ?>
    </section>

    <!-- Recent Projects -->
    <section class="admin-card">
        <div class="admin-card__header">
            <h2>Recent Projects</h2>
            <a href="<?= BASE_URL ?>/admin/projects.php">View all &rarr;</a>
        </div>
        <?php if ($recentProjects): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentProjects as $proj): ?>
                <tr>
                    <td><?= htmlspecialchars($proj['title']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($proj['cat_name']) ?></td>
                    <td>
                        <span class="status-badge status-badge--<?= $proj['status'] ?>">
                            <?= ucfirst($proj['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/admin/project-edit.php?id=<?= $proj['id'] ?>" class="btn-admin-sm">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="admin-empty">No projects yet. <a href="<?= BASE_URL ?>/admin/project-edit.php">Add one</a>.</p>
        <?php endif; ?>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
