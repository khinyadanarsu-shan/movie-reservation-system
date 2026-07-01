<?php
require_once __DIR__ . '/auth.php';
require_login();
if (!is_admin()) exit('Forbidden');

require_once __DIR__ . '/db.php';
$pdo = db();

// ✅ Insert new showtime
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'create')) {
  $stmt = $pdo->prepare('INSERT INTO showtimes(movie_id, auditorium_id, show_datetime, price) VALUES(?,?,?,?)');
  $stmt->execute([
      (int)$_POST['movie_id'],
      (int)$_POST['auditorium_id'],
      $_POST['show_datetime'],
      (float)$_POST['price']
  ]);
}

// ✅ Load Movies & Halls
$movies = $pdo->query('SELECT id, title FROM movies ORDER BY title')->fetchAll();
$halls  = $pdo->query('SELECT id, name FROM auditoriums ORDER BY id')->fetchAll();

$rows   = $pdo->query('SELECT s.id, m.title, a.name AS hall, s.show_datetime, s.price
                       FROM showtimes s
                       JOIN movies m ON m.id = s.movie_id
                       JOIN auditoriums a ON a.id = s.auditorium_id
                       ORDER BY s.show_datetime DESC')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Showtimes</title>
  <link rel="stylesheet" href="/cinemax/assets/style.css">
  <style>
    .back-btn {
      display: inline-block;
      background: #0066cc;
      padding: 8px 15px;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      margin-bottom: 15px;
      transition: 0.2s;
    }
    .back-btn:hover {
      background: #004a99;
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

  <a href="/cinemax/admin.php" class="back-btn">← Back to Dashboard</a>

  <h2>Manage Showtimes</h2>

  <form method="post" class="card">
    <input type="hidden" name="action" value="create">

    <label>Movie
      <select name="movie_id" required>
        <?php foreach ($movies as $m): ?>
          <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Hall
      <select name="auditorium_id" required>
        <?php foreach ($halls as $h): ?>
          <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Date & Time
      <input type="datetime-local" name="show_datetime" required>
    </label>

    <label>Price ($)
      <input type="number" step="0.01" name="price" value="5.00" required>
    </label>

    <button>Add Showtime</button>
  </form>

  <table class="table">
    <tr>
      <th>ID</th>
      <th>Movie</th>
      <th>Hall</th>
      <th>Date/Time</th>
      <th>Price</th>
    </tr>

    <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= $r['id'] ?></td>
      <td><?= htmlspecialchars($r['title']) ?></td>
      <td><?= htmlspecialchars($r['hall']) ?></td>
      <td><?= date('M d, Y H:i', strtotime($r['show_datetime'])) ?></td>
      <td>$<?= number_format($r['price'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

</main>

</body>
</html>