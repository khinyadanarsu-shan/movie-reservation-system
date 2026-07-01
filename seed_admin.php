<?php
require_once __DIR__ . '/db.php';
$pdo = db();

$name  = 'Admin';
$email = 'admin@example.com'; // ပြောင်းချင်ရင် ပြောင်း
$pass  = 'admin123';

$hash = password_hash($pass, PASSWORD_DEFAULT);

// admin ရှိ/မရှိ စစ်
$st = $pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$st->execute([$email]);
$u = $st->fetch();

if ($u) {
  // ✅ ရှိနေပြီဆို role + password_hash နှစ်ခုလုံး reset
  $pdo->prepare("UPDATE users SET role='admin', password_hash=? WHERE id=?")
      ->execute([$hash, $u['id']]);
  echo "Admin updated: $email (role=admin, password reset to $pass)\n";
  exit;
}

// username column ရှိ/မရှိ စစ်
$col = $pdo->query("SHOW COLUMNS FROM users LIKE 'username'")->fetch();

if ($col) {
  $ins = $pdo->prepare(
    "INSERT INTO users(name,username,email,password_hash,role) VALUES(?,?,?,?, 'admin')"
  );
  $ins->execute([$name, 'admin', $email, $hash]);
} else {
  $ins = $pdo->prepare(
    "INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?, 'admin')"
  );
  $ins->execute([$name, $email, $hash]);
}
echo "Admin created: email=$email, password=$pass\n";