<?php
require_once __DIR__ . '/auth.php';
require_login();
if (!is_admin()) exit('Forbidden');

require_once __DIR__ . '/db.php';
$pdo = db();

$rows = $pdo->query("
SELECT b.id, u.name as customer, u.email, b.total_price, b.created_at,
       m.title, a.name AS hall, s.show_datetime
FROM bookings b
JOIN users u      ON u.id = b.user_id
JOIN showtimes s  ON s.id = b.showtime_id
JOIN movies m     ON m.id = s.movie_id
JOIN auditoriums a ON a.id = s.auditorium_id
ORDER BY b.created_at DESC
")->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Bookings</title>
  <link rel="stylesheet" href="/cinemax/assets/style.css">
  <style>
    .back-btn {
      display:inline-block; background:#0066cc; color:#fff; text-decoration:none;
      padding:8px 14px; border-radius:6px; margin:10px 0 18px; transition:.2s;
      font-weight:600;
    }
    .back-btn:hover { background:#004a99; }
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
  <a href="/cinemax/admin.php" class="back-btn">← Back to Dashboard</a>

  <h2>All bookings</h2>
  <table class="table">
    <tr>
      <th>#</th>
      <th>Customer</th>
      <th>Email</th>
      <th>Movie</th>
      <th>Hall</th>
      <th>Showtime</th>
      <th>Total</th>
      <th>Booked at</th>
    </tr>
    <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= $r['id'] ?></td>
      <td><?= htmlspecialchars($r['customer']) ?></td>
      <td><?= htmlspecialchars($r['email']) ?></td>
      <td><?= htmlspecialchars($r['title']) ?></td>
      <td><?= htmlspecialchars($r['hall']) ?></td>
      <td><?= date('M d, Y H:i', strtotime($r['show_datetime'])) ?></td>
      <td>$<?= number_format($r['total_price'], 2) ?></td>
      <td><?= date('M d, Y H:i', strtotime($r['created_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</main>

</body>
</html>