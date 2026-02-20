<?php
/**
 * One-time script to fix the profile image path in the database.
 * DELETE THIS FILE after running it once.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$pdo = DB::getConnection();
$stmt = $pdo->prepare("UPDATE settings SET value = '/uploads/profile_matthew.jpeg' WHERE setting_key = 'profile_image'");
$stmt->execute();

echo "Done! Profile image path updated to /uploads/profile_matthew.jpeg\n";
echo "NOW DELETE THIS FILE (fix-profile-image.php) from your project.";
