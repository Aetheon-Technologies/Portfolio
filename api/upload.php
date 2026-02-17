<?php
/**
 * api/upload.php — Generic image upload handler for admin forms
 * Accepts POST with file field "image" and optional "dir" (projects|blog)
 * Returns JSON: { success, url } or { success: false, error }
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

header('Content-Type: application/json');

// Must be authenticated admin
if (!is_authenticated()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Forbidden.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$dir = in_array($_POST['dir'] ?? '', ['projects', 'blog']) ? $_POST['dir'] : 'projects';

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No valid file received.']);
    exit;
}

$file  = $_FILES['image'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);

if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'File type not allowed. Use JPEG, PNG, or WebP.']);
    exit;
}

if ($file['size'] > UPLOAD_MAX_BYTES) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'File too large. Maximum 5 MB.']);
    exit;
}

$ext      = match($mime) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    default      => 'jpg',
};
$filename = uniqid('img_') . '.' . $ext;
$destDir  = SITE_ROOT . '/uploads/' . $dir . '/';
$dest     = $destDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save file. Check directory permissions.']);
    exit;
}

echo json_encode([
    'success' => true,
    'url'     => BASE_URL . '/uploads/' . $dir . '/' . $filename,
]);
