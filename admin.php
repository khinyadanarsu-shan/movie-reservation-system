<?php
require_once __DIR__ . '/auth.php';
require_login();
if (!is_admin()) {
  header('Location: /cinemax/index.php');
  exit;
}

require_once __DIR__ . '/db.php';
$pdo = db();

/* ---------- Load Dashboard Stats from Database ---------- */
$totalMovies = (int)$pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$totalShowtimes = (int)$pdo->query("SELECT COUNT(*) FROM showtimes")->fetchColumn();
$totalBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$totalSales = (float)$pdo->query("SELECT SUM(total_price) FROM bookings")->fetchColumn();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard – CineMax</title>
  <link rel="stylesheet" href="/cinemax/assets/style.css">

  <style>
    /* Dashboard styling */
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      margin-top: 30px;
    }

    .card {
      background: linear-gradient(135deg, #0073e6, #003d80);
      color: #fff;
      padding: 25px;
      border-radius: 14px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transition: 0.25s;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 18px rgba(0,0,0,0.25);
    }

    .card h3 {
      font-size: 22px;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .card .value {
      font-size: 42px;
      font-weight: 900;
      margin-bottom: 6px;
    }

    .card small {
      opacity: 0.9;
      font-size: 14px;
    }

    /* Big buttons */
    .quick-links {
      margin-top: 45px;
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
    }

    .quick-links a {
      flex: 1;
      text-align: center;
      min-width: 260px;
      padding: 18px 30px;
      background: #1a2e5bff;
      color: #fff;
      border-radius: 12px;
      text-decoration: none;
      font-size: 20px;
      font-weight: 700;
      box-shadow: 0 3px 8px rgba(0,0,0,0.2);
      transition: 0.25s ease-in-out;
    }

    .quick-links a:hover {
      background: #004a99;
      transform: scale(1.03);
      box-shadow: 0 5px 12px rgba(0,0,0,0.3);
    }

  </style>

</head>
<body>

<header class="nav">
  <div class="brand">CineMax Admin</div>
  <nav>
    <a href="/cinemax/admin.php">Dashboard</a>
    <a href="/cinemax/admin_movies.php">Movies</a>
    <a href="/cinemax/admin_showtimes.php">Showtimes</a>
    <a href="/cinemax/admin_bookings.php">Bookings</a>
    <a href="/cinemax/logout.php">Logout</a>
  </nav>
</header>

<main class="container">

  <h2>📊 Admin Dashboard</h2>
  <p>Welcome back! Here is your real-time overview.</p>

  <div class="dashboard">
    <div class="card">
      <h3>Total Movies</h3>
      <div class="value"><?= $totalMovies ?></div>
      <small>Movies in database</small>
    </div>

    <div class="card">
      <h3>Total Showtimes</h3>
      <div class="value"><?= $totalShowtimes ?></div>
      <small>Scheduled showtimes</small>
    </div>

    <div class="card">
      <h3>Total Bookings</h3>
      <div class="value"><?= $totalBookings ?></div>
      <small>Customer bookings</small>
    </div>

    <div class="card">
      <h3>Total Sales</h3>
      <div class="value">$<?= number_format($totalSales,2) ?></div>
      <small>Lifetime revenue</small>
    </div>
  </div>

  <h3 style="margin-top:40px;">Quick Actions 🔧</h3>

  <div class="quick-links">
    <a href="/cinemax/admin_movies.php">➕ Add New Movie</a>
    <a href="/cinemax/admin_showtimes.php">🕒 Add New Showtime</a>
    <a href="/cinemax/admin_bookings.php">📄 View All Bookings</a>
  </div>

</main>

</body>
</html>