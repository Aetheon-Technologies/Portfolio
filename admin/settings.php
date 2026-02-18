<?php
require_once __DIR__ . '/includes/auth-check.php';

$pdo = DB::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Invalid form token. Please try again.');
        redirect('/admin/settings.php');
    }

    // ── Password change (separate action) ────────────────────────────────────
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $currentPw  = $_POST['current_password']  ?? '';
        $newPw      = $_POST['new_password']       ?? '';
        $confirmPw  = $_POST['confirm_password']   ?? '';

        $adminId    = (int)$_SESSION['admin_id'];
        $stmt       = $pdo->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
        $stmt->execute([$adminId]);
        $row        = $stmt->fetch();

        if (!$row || !password_verify($currentPw, $row['password_hash'])) {
            flash_set('error', 'Current password is incorrect.');
        } elseif (strlen($newPw) < 8) {
            flash_set('error', 'New password must be at least 8 characters.');
        } elseif ($newPw !== $confirmPw) {
            flash_set('error', 'New passwords do not match.');
        } else {
            $hash = password_hash($newPw, PASSWORD_BCRYPT);
            $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
                ->execute([$hash, $adminId]);
            flash_set('success', 'Password changed successfully.');
        }
        redirect('/admin/settings.php');
    }

    $keys = [
        'site_name', 'tagline', 'hero_summary', 'about_bio_1', 'about_bio_2',
        'stat_years', 'stat_projects', 'stat_technologies',
        'email', 'location', 'github_url', 'linkedin_url', 'twitter_url',
    ];

    foreach ($keys as $key) {
        save_setting($key, trim($_POST[$key] ?? ''));
    }

    // Handle resume upload
    if (!empty($_FILES['resume']['name'])) {
        $file = $_FILES['resume'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            // Accept application/pdf, application/x-pdf, or octet-stream with .pdf extension
            // (Windows finfo often returns octet-stream for valid PDFs)
            $isPdf = in_array($mime, ['application/pdf', 'application/x-pdf'])
                  || ($mime === 'application/octet-stream' && $ext === 'pdf');
            if ($isPdf && $file['size'] <= UPLOAD_MAX_BYTES) {
                $filename = 'resume_' . time() . '.pdf';
                $dest     = SITE_ROOT . '/uploads/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    save_setting('resume_url', BASE_URL . '/uploads/' . $filename);
                }
            } else {
                flash_set('error', 'Resume must be a PDF under 5 MB.');
                redirect('/admin/settings.php');
            }
        }
    }

    // Handle profile image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $file = $_FILES['profile_image'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            if (in_array($mime, ALLOWED_IMAGE_TYPES) && $file['size'] <= UPLOAD_MAX_BYTES) {
                $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . time() . '.' . strtolower($ext);
                $dest     = SITE_ROOT . '/uploads/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    save_setting('profile_image', BASE_URL . '/uploads/' . $filename);
                }
            } else {
                flash_set('error', 'Profile image must be JPEG/PNG/WebP under 5 MB.');
                redirect('/admin/settings.php');
            }
        }
    }

    flash_set('success', 'Settings saved successfully.');
    redirect('/admin/settings.php');
}

$s              = get_all_settings();
$adminPageTitle = 'Settings';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
    <h1>Site Settings</h1>
</div>

<form method="POST" enctype="multipart/form-data" class="admin-settings-form">
    <?= csrf_field() ?>

    <!-- Identity -->
    <fieldset class="admin-fieldset">
        <legend>Identity</legend>
        <div class="admin-form-grid">
            <div class="form-group">
                <label class="form-label" for="site_name">Your Name</label>
                <input type="text" id="site_name" name="site_name" class="form-input"
                       value="<?= htmlspecialchars($s['site_name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="tagline">Tagline / Role</label>
                <input type="text" id="tagline" name="tagline" class="form-input"
                       value="<?= htmlspecialchars($s['tagline'] ?? '') ?>">
            </div>
        </div>
    </fieldset>

    <!-- Hero & About -->
    <fieldset class="admin-fieldset">
        <legend>Hero &amp; About</legend>
        <div class="form-group">
            <label class="form-label" for="hero_summary">Hero Summary (1-2 sentences)</label>
            <textarea id="hero_summary" name="hero_summary" class="form-textarea" rows="3"><?= htmlspecialchars($s['hero_summary'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="about_bio_1">About Bio — Paragraph 1</label>
            <textarea id="about_bio_1" name="about_bio_1" class="form-textarea" rows="4"><?= htmlspecialchars($s['about_bio_1'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="about_bio_2">About Bio — Paragraph 2</label>
            <textarea id="about_bio_2" name="about_bio_2" class="form-textarea" rows="4"><?= htmlspecialchars($s['about_bio_2'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Profile Photo (JPEG/PNG/WebP, max 5 MB)</label>
            <?php if (!empty($s['profile_image'])): ?>
            <img src="<?= htmlspecialchars($s['profile_image']) ?>" id="profile-preview"
                 alt="Current profile photo" style="width:100px;height:100px;object-fit:cover;border-radius:8px;margin-bottom:0.5rem;display:block;">
            <?php else: ?>
            <img id="profile-preview" alt="" style="width:100px;height:100px;object-fit:cover;border-radius:8px;margin-bottom:0.5rem;display:none;">
            <?php endif; ?>
            <input type="file" name="profile_image" class="form-input" accept="image/*"
                   data-preview-target="profile-preview">
        </div>
    </fieldset>

    <!-- Stats -->
    <fieldset class="admin-fieldset">
        <legend>Stats Row</legend>
        <div class="admin-form-grid admin-form-grid--4">
            <div class="form-group">
                <label class="form-label" for="stat_years">Years Experience</label>
                <input type="text" id="stat_years" name="stat_years" class="form-input"
                       value="<?= htmlspecialchars($s['stat_years'] ?? '3+') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="stat_projects">Projects Completed</label>
                <input type="text" id="stat_projects" name="stat_projects" class="form-input"
                       value="<?= htmlspecialchars($s['stat_projects'] ?? '20+') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="stat_technologies">Technologies</label>
                <input type="text" id="stat_technologies" name="stat_technologies" class="form-input"
                       value="<?= htmlspecialchars($s['stat_technologies'] ?? '15+') ?>">
            </div>
        </div>
    </fieldset>

    <!-- Resume -->
    <fieldset class="admin-fieldset">
        <legend>Resume</legend>
        <?php if (!empty($s['resume_url'])): ?>
        <p class="admin-empty" style="margin-bottom:0.75rem;">
            Current: <a href="<?= htmlspecialchars($s['resume_url']) ?>" target="_blank">View Resume</a>
        </p>
        <?php endif; ?>
        <div class="form-group">
            <label class="form-label">Upload New Resume PDF (max 5 MB)</label>
            <input type="file" name="resume" class="form-input" accept="application/pdf">
        </div>
    </fieldset>

    <!-- Contact & Social -->
    <fieldset class="admin-fieldset">
        <legend>Contact &amp; Social</legend>
        <div class="admin-form-grid">
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input"
                       value="<?= htmlspecialchars($s['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="location">Location</label>
                <input type="text" id="location" name="location" class="form-input"
                       value="<?= htmlspecialchars($s['location'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="github_url">GitHub URL</label>
                <input type="url" id="github_url" name="github_url" class="form-input"
                       value="<?= htmlspecialchars($s['github_url'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="linkedin_url">LinkedIn URL</label>
                <input type="url" id="linkedin_url" name="linkedin_url" class="form-input"
                       value="<?= htmlspecialchars($s['linkedin_url'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="twitter_url">Twitter / X URL</label>
                <input type="url" id="twitter_url" name="twitter_url" class="form-input"
                       value="<?= htmlspecialchars($s['twitter_url'] ?? '') ?>">
            </div>
        </div>
    </fieldset>

    <div class="admin-form-actions">
        <button type="submit" class="btn-admin-primary">Save All Changes</button>
    </div>
</form>

<!-- Change Password (separate form / separate action) -->
<fieldset class="admin-fieldset" style="margin-top:2rem;">
    <legend>Change Password</legend>
    <form method="POST" style="max-width:480px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_password">

        <div class="form-group">
            <label class="form-label" for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password"
                   class="form-input" required autocomplete="current-password">
        </div>
        <div class="form-group">
            <label class="form-label" for="new_password">New Password (min 8 characters)</label>
            <input type="password" id="new_password" name="new_password"
                   class="form-input" required autocomplete="new-password" minlength="8">
        </div>
        <div class="form-group">
            <label class="form-label" for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password"
                   class="form-input" required autocomplete="new-password">
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="btn-admin-secondary">Update Password</button>
        </div>
    </form>
</fieldset>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
