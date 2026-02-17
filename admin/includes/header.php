<?php
// Get unread message count for sidebar badge
$_unreadCount = 0;
try {
    $_unreadStmt  = DB::getConnection()->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0 AND is_archived = 0");
    $_unreadCount = (int)$_unreadStmt->fetchColumn();
} catch (Exception $e) { /* fail silently */ }

$_currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= isset($adminPageTitle) ? htmlspecialchars($adminPageTitle) . ' — ' : '' ?>Portfolio Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar__logo">
        <a href="<?= BASE_URL ?>/admin/" class="admin-logo">
            <span>&lt;</span>Admin<span>/&gt;</span>
        </a>
    </div>

    <nav class="admin-nav" aria-label="Admin navigation">
        <a href="<?= BASE_URL ?>/admin/" class="admin-nav__item <?= $_currentPage === 'index.php' ? 'is-active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="<?= BASE_URL ?>/admin/projects.php" class="admin-nav__item <?= in_array($_currentPage, ['projects.php','project-edit.php']) ? 'is-active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            Projects
        </a>
        <a href="<?= BASE_URL ?>/admin/skills.php" class="admin-nav__item <?= in_array($_currentPage, ['skills.php','skill-edit.php']) ? 'is-active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Skills
        </a>
        <a href="<?= BASE_URL ?>/admin/blog.php" class="admin-nav__item <?= in_array($_currentPage, ['blog.php','blog-edit.php']) ? 'is-active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Blog
        </a>
        <a href="<?= BASE_URL ?>/admin/messages.php" class="admin-nav__item <?= $_currentPage === 'messages.php' ? 'is-active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Messages
            <?php if ($_unreadCount > 0): ?>
            <span class="admin-badge"><?= $_unreadCount ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/admin/settings.php" class="admin-nav__item <?= $_currentPage === 'settings.php' ? 'is-active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            Settings
        </a>
    </nav>

    <div class="admin-sidebar__bottom">
        <a href="<?= BASE_URL ?>/" class="admin-nav__item" target="_blank">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            View Site
        </a>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="admin-nav__item admin-nav__logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Log Out
        </a>
    </div>
</aside>

<main class="admin-main">
<?php
// Display and clear flash message
$flash = flash_get();
if ($flash):
?>
<div class="flash flash--<?= htmlspecialchars($flash['type']) ?>" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button class="flash__close" aria-label="Dismiss">&times;</button>
</div>
<?php endif; ?>
