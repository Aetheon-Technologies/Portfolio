<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

// Already logged in?
if (is_authenticated()) {
    redirect('/admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password && auth_login($username, $password)) {
        redirect('/admin/');
    } else {
        $error = 'Invalid username or password.';
        // Small delay to slow brute force
        usleep(500000);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login — Portfolio</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-login-page">

<div class="login-card">
    <div class="login-card__header">
        <h1 class="login-logo"><span>&lt;</span>Admin<span>/&gt;</span></h1>
        <p class="login-subtitle">Sign in to manage your portfolio</p>
    </div>

    <?php if ($error): ?>
    <div class="flash flash--error" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="login-form" novalidate>
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-input"
                   required autocomplete="username" autofocus
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-input"
                   required autocomplete="current-password">
        </div>

        <button type="submit" class="btn-admin-primary" style="width:100%;margin-top:0.5rem;">
            Sign In
        </button>
    </form>
</div>

</body>
</html>
