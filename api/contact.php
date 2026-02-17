<?php
/**
 * api/contact.php — Contact form submission handler
 * Accepts POST, validates, rate-limits, saves to DB. Returns JSON.
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

// ── Rate Limiting (session-based, simple) ─────────────────────────────────────
$rateKey     = 'contact_rate';
$rateLimit   = 5;       // max submissions
$rateWindow  = 10 * 60; // 10 minutes in seconds

$now = time();
if (!isset($_SESSION[$rateKey])) {
    $_SESSION[$rateKey] = ['count' => 0, 'window_start' => $now];
}

$rate = &$_SESSION[$rateKey];

// Reset window if expired
if ($now - $rate['window_start'] > $rateWindow) {
    $rate = ['count' => 0, 'window_start' => $now];
}

if ($rate['count'] >= $rateLimit) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many requests. Please wait before submitting again.']);
    exit;
}

// ── Validation ────────────────────────────────────────────────────────────────
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (empty($name) || strlen($name) > 150) {
    $errors[] = 'Name is required (max 150 characters).';
}

if (!is_valid_email($email) || strlen($email) > 254) {
    $errors[] = 'A valid email address is required.';
}

if (strlen($subject) > 300) {
    $errors[] = 'Subject is too long (max 300 characters).';
}

if (empty($message) || strlen($message) < 20) {
    $errors[] = 'Message must be at least 20 characters.';
}

if ($errors) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
    exit;
}

// ── Save to Database ──────────────────────────────────────────────────────────
try {
    $pdo  = DB::getConnection();
    $ip   = $_SERVER['HTTP_X_FORWARDED_FOR']
          ?? $_SERVER['REMOTE_ADDR']
          ?? '';
    // Take only the first IP if comma-separated (proxy)
    $ip   = trim(explode(',', $ip)[0]);

    $stmt = $pdo->prepare(
        'INSERT INTO contact_messages (name, email, subject, message, ip_address)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $name,
        $email,
        $subject,
        $message,
        substr($ip, 0, 45),
    ]);

    // Increment rate counter
    $rate['count']++;

    echo json_encode(['success' => true, 'message' => "Thanks {$name}! I'll be in touch soon."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save your message. Please try again.']);
}
