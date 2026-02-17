<?php
/**
 * Portfolio Configuration
 * Reads from environment variables (Railway) with XAMPP fallbacks.
 */

// ── Database ─────────────────────────────────────────────────────────────────
define('DB_HOST',    getenv('MYSQLHOST')     ?: 'localhost');
define('DB_NAME',    getenv('MYSQLDATABASE') ?: 'portfolio');
define('DB_USER',    getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS',    getenv('MYSQLPASSWORD') ?: '');
define('DB_PORT',    getenv('MYSQLPORT')     ?: '3306');
define('DB_CHARSET', 'utf8mb4');

// ── Paths ─────────────────────────────────────────────────────────────────────
define('SITE_ROOT', dirname(__DIR__));          // Absolute filesystem path
define('BASE_URL',  getenv('BASE_URL') ?: '/portfolio');  // '' on Railway root, '/portfolio' for XAMPP

// ── Environment ───────────────────────────────────────────────────────────────
$isProduction = (bool) getenv('RAILWAY_ENVIRONMENT');
define('DEV_MODE', !$isProduction);

if (DEV_MODE) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// ── Timezone ──────────────────────────────────────────────────────────────────
date_default_timezone_set('America/Edmonton');  // Calgary (Mountain Time)

// ── Upload settings ───────────────────────────────────────────────────────────
define('UPLOAD_MAX_BYTES', 5 * 1024 * 1024);   // 5 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// ── Session ───────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
