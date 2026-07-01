<?php
require_once __DIR__ . '/db.php';
$pdo = db();

$email = 'admin@example.com';
$newPassword = 'admin123';

$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$update = $pdo->prepare("UPDATE users SET password_hash=?, role='admin' WHERE email=?");
$update->execute([$hash, $email]);

echo "Admin password reset successfully";