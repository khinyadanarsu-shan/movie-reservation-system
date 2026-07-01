<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/db.php';

$pdo = db();
$user = current_user();

$rows = $pdo->prepare("SELECT b.id, b.created_at, b.total_price, s.show_datetime, m.title, a.name AS hall
  FROM bookings b
  JOIN showtimes s ON s.id=b.showtime_id
  JOIN movies m ON m.id=s.movie_id
  JOIN auditoriums a ON a.id=s.auditorium_id
  WHERE b.user_id=?
  ORDER BY b.created_at DESC");
$rows->execute([$user['id']]);
$list = $rows->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CineMax – My Bookings</title>
  <link rel="stylesheet" href="/cinemax/assets/style.css">

  <style>
    .back-btn {
      display:inline-block;
      background:darkblue;
      color:#fff;
      padding:10px 16px;
      border-radius:6px;
      text-decoration:none;
      font-weight:600;
      margin-bottom:20px;
      transition:0.2s ease;
    }
    .back-btn:hover {
      background:#003f8a;
    }
    .success-box {
      background:#1e4620;
      color:#b6f2c5;
      padding:15px 20px;
      border-radius:8px;
      margin-bottom:20px;
      border:1px solid #2e8b57;
      font-size:16px;
    }
  </style>
</head>
<body>

<header class="nav">
  <div class="brand">CineMax</div>
  <nav>
    <a href="/cinemax/index.php">Movies</a>
    <span class="hello">Hi, <?=htmlspecialchars($user['name'])?></span>
    <a href="/cinemax/my_bookings.php">My bookings</a>
    <a href="/cinemax/logout.php">Logout</a>
  </nav>
</header>

<main class="container">

  <!-- ✅ Back Button -->
  <a href="/cinemax/index.php" class="back-btn">← Back to Movies</a>

  <h2>My Bookings</h2>

  <!-- ✅ Confirmation Message -->
  <div class="success-box">
      ✅ Your booking has been confirmed. You can make the payment when you come to the cinema.
  </div>

  <?php if(empty($list)): ?>
    <p>No bookings yet. <a href="/cinemax/index.php">Browse movies</a> to start booking!</p>
  <?php else: ?>
    <table class="table">
      <tr>
        <th>#</th>
        <th>Movie</th>
        <th>Hall</th>
        <th>Showtime</th>
        <th>Total</th>
        <th>Booked at</th>
      </tr>

      <?php foreach($list as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['hall']) ?></td>
        <td><?= date('M d, Y H:i', strtotime($r['show_datetime'])) ?></td>
        <td>$<?= number_format($r['total_price'], 2) ?></td>
        <td><?= date('M d, Y H:i', strtotime($r['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>

    </table>
  <?php endif; ?>

</main>

</body>
</html>