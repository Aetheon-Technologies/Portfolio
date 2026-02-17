<?php
/**
 * Admin Authentication
 */

function auth_login(string $username, string $password): bool
{
    $pdo  = DB::getConnection();
    $stmt = $pdo->prepare('SELECT id, password_hash FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    $_SESSION['admin_id']   = $user['id'];
    $_SESSION['admin_user'] = $username;

    // Update last login timestamp
    $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')
        ->execute([$user['id']]);

    return true;
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']
        );
    }
    session_destroy();
    redirect('/admin/login.php');
}

function is_authenticated(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_auth(): void
{
    if (!is_authenticated()) {
        redirect('/admin/login.php');
    }
}
