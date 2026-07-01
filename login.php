<?php
require_once __DIR__ . '/auth.php';
if (current_user()) {
  header('Location: ' . (is_admin() ? '/cinemax/admin.php' : '/cinemax/index.php'));
  exit;
}
$error = '';
$next  = $_GET['next'] ?? '/cinemax/index.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'login') {
    $idOrEmail = trim($_POST['id_or_email'] ?? '');
    $password  = $_POST['password'] ?? '';
    if (login_user($idOrEmail, $password)) {
      $go = is_admin() ? '/cinemax/admin.php' : ($next ?: '/cinemax/index.php');
      header("Location: $go");
      exit;
    } else {
      $error = 'Invalid credentials.';
    }
  } elseif ($action === 'register') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !$password) {
      $error = 'All fields required.';
    } else {
      try {
        register_user($name, $email, $password);
        if (login_user($email, $password)) {
          $go = is_admin() ? '/cinemax/admin.php' : ($next ?: '/cinemax/index.php');
          header("Location: $go"); exit;
        }
        header('Location: /cinemax/index.php'); exit;
      } catch (Throwable $e) {
        $error = 'Email already used.';
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login – CineMax</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Keep your base styles if you like -->
  <link rel="stylesheet" href="/cinemax/assets/style.css">
  <style>
    /* ===== Pretty Login/Signup Page ===== */

    /* Full page gradient background */
    body {
      min-height: 100vh;
      margin: 0;
      background:
        radial-gradient(1200px 600px at -10% -10%, #0d1b2a 0%, rgba(13,27,42,0) 70%),
        radial-gradient(900px 500px at 120% 20%, #1b263b 0%, rgba(27,38,59,0) 70%),
        linear-gradient(160deg, #0b1020 0%, #0a1628 50%, #0c1f35 100%);
      color: #dbe6ff;
      font: 16px/1.5 system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
      display: flex;
      flex-direction: column;
    }

    /* Top nav tweaks to sit on dark bg */
    .nav {
      background: transparent;
      border-bottom: 1px solid rgba(255,255,255,0.07);
      backdrop-filter: blur(4px);
    }
    .nav .brand { color: #9cc1ff; font-weight: 800; letter-spacing: .3px; }
    .nav a { color: #c6d8ff; }
    .nav a:hover { color: #fff; }

    /* Center wrapper */
    .auth-wrap {
      flex: 1;
      display: grid;
      place-items: center;
      padding: 36px 18px 60px;
    }

    /* Glass card container with two columns (responsive) */
    .auth-card {
      width: 100%;
      max-width: 980px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 18px;
      box-shadow:
        0 10px 30px rgba(0,0,0,.35),
        inset 0 1px 0 rgba(255,255,255,0.04);
      backdrop-filter: blur(10px);
      padding: 28px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 22px;
    }
    @media (max-width: 880px) {
      .auth-card { grid-template-columns: 1fr; max-width: 560px; }
    }

    /* Side headings and helper text */
    .auth-side h2 {
      margin: 0 0 6px;
      font-size: 26px;
      font-weight: 800;
      color: #e8f1ff;
    }
    .auth-side p.muted {
      margin: 0 0 16px;
      color: #9db3d9;
      font-size: 14px;
    }

    /* Panels */
    .panel {
      background: rgba(7,16,33,0.55);
      border: 1px solid rgba(255,255,255,0.06);
      border-radius: 14px;
      padding: 20px;
    }

    /* Error message box */
    .error {
      background: #2a1111;
      border: 1px solid #b54949;
      color: #ffd7d7;
      padding: 10px 12px;
      border-radius: 10px;
      margin-bottom: 14px;
      font-weight: 600;
    }

    /* Forms */
    form { display: grid; gap: 12px; }

    .field {
      display: grid;
      gap: 6px;
    }
    .field label {
      font-size: 13px;
      color: #a9c1ee;
    }
    .input {
      appearance: none;
      width: 100%;
      padding: 12px 14px;
      background: #0e1a2b;
      color: #e6eeff;
      border: 1px solid #22344f;
      border-radius: 10px;
      outline: none;
      transition: border-color .15s, box-shadow .15s, transform .03s;
    }
    .input::placeholder { color: #8aa2c8; }
    .input:focus {
      border-color: #5fa8ff;
      box-shadow: 0 0 0 3px rgba(95,168,255,.18);
      transform: translateY(-1px);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 16px;
      border-radius: 12px;
      border: 1px solid transparent;
      font-weight: 800;
      cursor: pointer;
      background: linear-gradient(135deg, #2f7cff, #5ad0ff);
      color: #08111f;
      text-shadow: 0 1px 0 rgba(255,255,255,.35);
      box-shadow:
        0 8px 18px rgba(47,124,255,.35),
        inset 0 1px 0 rgba(255,255,255,.25);
      transition: transform .08s ease, box-shadow .15s ease, filter .15s ease;
    }
    .btn:hover {
      filter: saturate(1.1) brightness(1.02);
      box-shadow:
        0 10px 24px rgba(47,124,255,.45),
        inset 0 1px 0 rgba(255,255,255,.25);
    }
    .btn:active { transform: translateY(1px); }

    .btn-ghost {
      background: transparent;
      color: #cfe1ff;
      border-color: #2a4370;
    }
    .btn-ghost:hover {
      background: rgba(42,67,112,.25);
    }

    .split {
      display: grid;
      gap: 18px;
    }

    /* Titles inside panels */
    .panel h3 {
      margin: 0 0 6px;
      font-size: 18px;
      font-weight: 800;
      color: #e9f2ff;
    }

    /* Small helper link */
    .helper {
      font-size: 13px;
      color: #9db3d9;
      margin-top: 8px;
    }
    .helper a { color: #a9c8ff; text-decoration: none; }
    .helper a:hover { text-decoration: underline; }
  </style>
</head>
<body>
<header class="nav">
  <div class="brand">CineMax</div>
  <nav><a href="/cinemax/index.php">Movies</a></nav>
</header>

<div class="auth-wrap">
  <div class="auth-card">
    <!-- Left: Welcome/branding -->
    <div class="auth-side">
      <h2>Welcome to CineMax</h2>
      <p class="muted">Book tickets in a few clicks. Log in or create a new account to continue.</p>

      <div class="panel">
        <h3>Why CineMax?</h3>
        <ul style="margin: 10px 0 0 18px; color:#bcd2f3; padding:0; line-height:1.6;">
          <li>Fast & secure booking</li>
          <li>Clean seat selection UI</li>
          <li>No online payment needed — pay at the cinema</li>
        </ul>
        <p class="helper">Need help? <a href="/cinemax/index.php">Browse movies</a> first.</p>
      </div>
    </div>

    <!-- Right: Forms -->
    <div class="split">
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <div class="panel">
        <h3>Login</h3>
        <form method="post" autocomplete="on">
          <input type="hidden" name="action" value="login">

          <div class="field">
            <label for="id_or_email">Email or Username</label>
            <input class="input" id="id_or_email" name="id_or_email" type="text" placeholder="you@example.com or yourname" required>
          </div>

          <div class="field">
            <label for="password">Password</label>
            <input class="input" id="password" name="password" type="password" placeholder="••••••••" required>
          </div>

          <button class="btn" type="submit">Login</button>
        </form>
      </div>

      <div class="panel">
        <h3>Create account</h3>
        <form method="post" autocomplete="on">
          <input type="hidden" name="action" value="register">

          <div class="field">
            <label for="name">Full name</label>
            <input class="input" id="name" name="name" type="text" placeholder="John Doe" required>
          </div>

          <div class="field">
            <label for="email">Email</label>
            <input class="input" id="email" name="email" type="email" placeholder="you@example.com" required>
          </div>

          <div class="field">
            <label for="reg_password">Password</label>
            <input class="input" id="reg_password" name="password" type="password" placeholder="At least 8 characters" required>
          </div>

          <button class="btn" type="submit">Register</button>
          <div class="helper">Already have an account? Use the Login form above.</div>
        </form>
      </div>

      <button class="btn-ghost" type="button" onclick="location.href='/cinemax/index.php'">← Back to Movies</button>
    </div>
  </div>
</div>

</body>
</html>