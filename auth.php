<?php
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

/* ---------- Session helpers ---------- */
function current_user(): ?array {
  return $_SESSION['user'] ?? null;
}
function is_admin(): bool {
  $u = current_user();
  return $u && (($u['role'] ?? 'customer') === 'admin');
}
function require_login(): void {
  if (!current_user()) {
    $next = urlencode($_SERVER['REQUEST_URI'] ?? '/cinemax/');
    header("Location: /cinemax/login.php?next=$next");
    exit;
  }
}
function require_admin(): void {
  require_login();
  if (!is_admin()) {
    header("Location: /cinemax/index.php");
    exit;
  }
}

/* ---------- DB helpers ---------- */
function find_user_by_email(string $email): ?array {
  $st = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
  $st->execute([$email]);
  return $st->fetch() ?: null;
}
function find_user_by_username(string $username): ?array {
  // username column မရှိသေးလည်း error မပစ်အောင် try/catch
  try {
    $st = db()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $st->execute([$username]);
    return $st->fetch() ?: null;
  } catch (Throwable $e) {
    return null;
  }
}

/* ---------- Register / Login ---------- */
function register_user(string $name, string $email, string $password): int {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $st = db()->prepare('INSERT INTO users(name, email, password_hash) VALUES(?,?,?)');
  $st->execute([$name, $email, $hash]); // role defaults to 'customer'
  return (int)db()->lastInsertId();
}

/** Login with username OR email */
function login_user(string $idOrEmail, string $password): bool {
  // email ဖြစ်ရင် email နဲ့, မဟုတ်ရင် username နဲ့ စမ်းပြီးမတွေ့ရင် email နဲ့လည်းပြန်စမ်း
  $u = filter_var($idOrEmail, FILTER_VALIDATE_EMAIL)
      ? find_user_by_email($idOrEmail)
      : (find_user_by_username($idOrEmail) ?? find_user_by_email($idOrEmail));

  if (!$u) return false;
  if (!password_verify($password, $u['password_hash'])) return false;

  $_SESSION['user'] = [
    'id'       => (int)$u['id'],
    'name'     => $u['name'],
    'email'    => $u['email'],
    'role'     => $u['role'] ?? 'customer',
    'username' => $u['username'] ?? null,
  ];
  return true;
}

function logout_user(): void {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
  }
  session_destroy();
}