<?php
// Temporary diagnostic — delete after use
header('Content-Type: text/plain');

$vars = ['MYSQLHOST','MYSQLDATABASE','MYSQLUSER','MYSQLPASSWORD','MYSQLPORT','BASE_URL','RAILWAY_ENVIRONMENT'];
foreach ($vars as $v) {
    $val = getenv($v);
    echo "$v = " . ($v === 'MYSQLPASSWORD' ? (empty($val) ? '(empty)' : '(set)') : ($val ?: '(empty)')) . "\n";
}

echo "\nConnecting...\n";
try {
    $host = getenv('MYSQLHOST') ?: 'localhost';
    $port = getenv('MYSQLPORT') ?: '3306';
    $db   = getenv('MYSQLDATABASE') ?: 'portfolio';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: '';

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Connected OK\n";
    $count = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    echo "Settings rows: $count\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
