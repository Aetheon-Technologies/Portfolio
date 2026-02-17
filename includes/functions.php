<?php
/**
 * Shared utility functions
 */

// ── Input Sanitization ────────────────────────────────────────────────────────

function sanitize(string $str): string
{
    return htmlspecialchars(trim($str), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sanitize_url(string $url): string
{
    $url = filter_var(trim($url), FILTER_SANITIZE_URL);
    return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
}

// ── Slug Utilities ────────────────────────────────────────────────────────────

function slugify(string $str): string
{
    $str = mb_strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}

function slug_unique(PDO $pdo, string $table, string $baseSlug, int $excludeId = 0): string
{
    $slug = $baseSlug;
    $i    = 2;

    while (true) {
        $sql  = "SELECT id FROM `{$table}` WHERE slug = ? AND id != ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$slug, $excludeId]);

        if (!$stmt->fetch()) {
            break;
        }

        $slug = $baseSlug . '-' . $i++;
    }

    return $slug;
}

// ── Settings ──────────────────────────────────────────────────────────────────

function get_setting(string $key, string $default = ''): string
{
    static $cache = null;

    if ($cache === null) {
        $pdo   = DB::getConnection();
        $stmt  = $pdo->query('SELECT setting_key, value FROM settings');
        $cache = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    return $cache[$key] ?? $default;
}

function get_all_settings(): array
{
    $pdo  = DB::getConnection();
    $stmt = $pdo->query('SELECT setting_key, value FROM settings');
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function save_setting(string $key, string $value): void
{
    $pdo  = DB::getConnection();
    $stmt = $pdo->prepare(
        'INSERT INTO settings (setting_key, value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE value = VALUES(value)'
    );
    $stmt->execute([$key, $value]);
}

// ── CSRF Protection ───────────────────────────────────────────────────────────

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

// ── Redirects ─────────────────────────────────────────────────────────────────

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function redirect_abs(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// ── Flash Messages ────────────────────────────────────────────────────────────

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ── Validation ────────────────────────────────────────────────────────────────

function is_valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ── Date Formatting ───────────────────────────────────────────────────────────

function format_date(string $dateStr, string $format = 'M j, Y'): string
{
    try {
        $dt = new DateTime($dateStr);
        return $dt->format($format);
    } catch (Exception) {
        return $dateStr;
    }
}

// ── Pagination ────────────────────────────────────────────────────────────────

function paginate(int $total, int $perPage, int $currentPage): array
{
    $totalPages = (int) ceil($total / $perPage);
    $offset     = ($currentPage - 1) * $perPage;

    return [
        'total_pages'  => $totalPages,
        'offset'       => max(0, $offset),
        'current_page' => $currentPage,
        'per_page'     => $perPage,
        'total'        => $total,
        'has_prev'     => $currentPage > 1,
        'has_next'     => $currentPage < $totalPages,
    ];
}

// ── Tech Tags ─────────────────────────────────────────────────────────────────

function parse_tags(string $tags): array
{
    if (empty(trim($tags))) {
        return [];
    }
    return array_filter(array_map('trim', explode(',', $tags)));
}
