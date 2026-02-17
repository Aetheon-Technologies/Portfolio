<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo = DB::getConnection();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Invalid token.'); redirect('/admin/messages.php');
    }

    $msgId = (int)($_POST['msg_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($msgId) {
        match($action) {
            'mark_read'   => $pdo->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([$msgId]),
            'mark_unread' => $pdo->prepare('UPDATE contact_messages SET is_read=0 WHERE id=?')->execute([$msgId]),
            'archive'     => $pdo->prepare('UPDATE contact_messages SET is_archived=1 WHERE id=?')->execute([$msgId]),
            'unarchive'   => $pdo->prepare('UPDATE contact_messages SET is_archived=0 WHERE id=?')->execute([$msgId]),
            'delete'      => $pdo->prepare('DELETE FROM contact_messages WHERE id=?')->execute([$msgId]),
            default       => null,
        };
        flash_set('success', 'Message updated.');
    }
    redirect('/admin/messages.php');
}

$tab = $_GET['tab'] ?? 'inbox';
$viewId = (int)($_GET['id'] ?? 0);

// Mark as read when viewing
if ($viewId) {
    $pdo->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([$viewId]);
}

$where = match($tab) {
    'all'      => 'is_archived = 0',
    'archived' => 'is_archived = 1',
    default    => 'is_read = 0 AND is_archived = 0',
};

$messages = $pdo->query(
    "SELECT * FROM contact_messages WHERE $where ORDER BY created_at DESC"
)->fetchAll();

$viewMsg = null;
if ($viewId) {
    $stmt = $pdo->prepare('SELECT * FROM contact_messages WHERE id = ?');
    $stmt->execute([$viewId]);
    $viewMsg = $stmt->fetch();
}

$unreadCount = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0 AND is_archived=0")->fetchColumn();

$adminPageTitle = 'Messages';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1>Contact Messages</h1>
</div>

<!-- Tabs -->
<div class="admin-tabs">
    <a href="?tab=inbox" class="admin-tab <?= $tab === 'inbox' ? 'is-active' : '' ?>">
        Unread
        <?php if ($unreadCount > 0): ?><span class="admin-badge"><?= $unreadCount ?></span><?php endif; ?>
    </a>
    <a href="?tab=all" class="admin-tab <?= $tab === 'all' ? 'is-active' : '' ?>">All</a>
    <a href="?tab=archived" class="admin-tab <?= $tab === 'archived' ? 'is-active' : '' ?>">Archived</a>
</div>

<div style="display:grid;grid-template-columns:1fr <?= $viewMsg ? '1fr' : '' ?>;gap:1.5rem;align-items:start;">

<!-- Messages List -->
<div class="admin-card">
    <?php if ($messages): ?>
    <table class="admin-table">
        <thead>
            <tr><th>From</th><th>Subject</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $msg): ?>
            <tr class="<?= !$msg['is_read'] ? 'row-unread' : '' ?> <?= $viewId === (int)$msg['id'] ? 'row-selected' : '' ?>">
                <td>
                    <a href="?tab=<?= htmlspecialchars($tab) ?>&id=<?= $msg['id'] ?>"><?= htmlspecialchars($msg['name']) ?></a>
                    <br><small class="text-muted"><?= htmlspecialchars($msg['email']) ?></small>
                </td>
                <td><?= htmlspecialchars($msg['subject'] ?: '(no subject)') ?></td>
                <td class="text-muted"><?= format_date($msg['created_at'], 'M j, Y') ?></td>
                <td class="admin-actions">
                    <form method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="msg_id" value="<?= $msg['id'] ?>">
                        <?php if ($msg['is_read']): ?>
                        <button name="action" value="mark_unread" class="btn-admin-sm">Unread</button>
                        <?php else: ?>
                        <button name="action" value="mark_read" class="btn-admin-sm">Read</button>
                        <?php endif; ?>
                        <?php if ($msg['is_archived']): ?>
                        <button name="action" value="unarchive" class="btn-admin-sm">Unarchive</button>
                        <?php else: ?>
                        <button name="action" value="archive" class="btn-admin-sm">Archive</button>
                        <?php endif; ?>
                        <button name="action" value="delete" class="btn-admin-sm btn-admin-danger btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="admin-empty">No messages in this tab.</p>
    <?php endif; ?>
</div>

<!-- Message Detail -->
<?php if ($viewMsg): ?>
<div class="admin-card">
    <div class="admin-card__header">
        <h2><?= htmlspecialchars($viewMsg['subject'] ?: '(no subject)') ?></h2>
    </div>
    <p><strong>From:</strong> <?= htmlspecialchars($viewMsg['name']) ?> &lt;<a href="mailto:<?= htmlspecialchars($viewMsg['email']) ?>"><?= htmlspecialchars($viewMsg['email']) ?></a>&gt;</p>
    <p><strong>Date:</strong> <?= format_date($viewMsg['created_at'], 'F j, Y \a\t g:i A') ?></p>
    <hr style="border-color:var(--adm-border);margin:1rem 0;">
    <div style="white-space:pre-wrap;color:var(--adm-muted);line-height:1.7;"><?= htmlspecialchars($viewMsg['message']) ?></div>
    <div style="margin-top:1.5rem;">
        <a href="mailto:<?= htmlspecialchars($viewMsg['email']) ?>?subject=Re: <?= urlencode($viewMsg['subject'] ?? '') ?>"
           class="btn-admin-primary">
            Reply via Email
        </a>
    </div>
</div>
<?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
