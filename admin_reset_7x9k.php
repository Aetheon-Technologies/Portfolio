<?php
// ONE-TIME admin password reset — DELETE THIS FILE IMMEDIATELY AFTER USE
// Access: /admin_reset_7x9k.php?t=mccusker2026

if (($_GET['t'] ?? '') !== 'mccusker2026') {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$newPassword = 'Portfolio2026!';
$hash = password_hash($newPassword, PASSWORD_BCRYPT);

$pdo = DB::getConnection();
$stmt = $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE username = ?');
$stmt->execute([$hash, 'admin']);

echo '<pre>';
echo "Done. New credentials:\n";
echo "Username: admin\n";
echo "Password: " . $newPassword . "\n\n";
echo "DELETE THIS FILE NOW: /admin_reset_7x9k.php\n";
echo '</pre>';
