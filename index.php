<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
ensure_basic_data();
$pdo = db();

$movies = $pdo->query("SELECT id,title,poster_url,rating,duration_mins,description FROM movies ORDER BY title")->fetchAll();

$showtimes = $pdo->query("
  SELECT s.id, s.movie_id, s.auditorium_id, s.show_datetime, s.price, a.name AS hall
  FROM showtimes s
  JOIN auditoriums a ON a.id = s.auditorium_id
  WHERE s.show_datetime >= NOW()
  ORDER BY s.show_datetime ASC
")->fetchAll();

$byMovie = [];
foreach ($showtimes as $r) { $byMovie[(int)$r['movie_id']][] = $r; }

$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CineMax – Movies</title>
  <link rel="stylesheet" href="/cinemax/assets/style.css">
</head>
<body>
<header class="nav">
  <div class="brand">CineMax</div>
  <nav>
    <a href="/cinemax/index.php">Movies</a>
    <?php if(!$user): ?>
      <a href="/cinemax/login.php">Login</a>
    <?php else: ?>
      <span class="hello">Hi, <?=htmlspecialchars($user['name'])?></span>
      <a href="/cinemax/my_bookings.php">My bookings</a>
      <a href="/cinemax/logout.php">Logout</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
  <h1>Now Showing</h1>
  <div class="grid">
    <?php foreach($movies as $m): ?>
      <div class="card">
        <img src="<?=htmlspecialchars($m['poster_url'] ?: 'https://via.placeholder.com/300x450?text=Poster')?>" alt="Poster">
        <div class="card-body">
          <h3><?=htmlspecialchars($m['title'])?></h3>
          <p class="muted">Rating: <?=htmlspecialchars($m['rating']?:'N/A')?> · <?= (int)$m['duration_mins']?> mins</p>
          <p><?=nl2br(htmlspecialchars($m['description']))?></p>
          <div class="slots">
            <?php foreach(($byMovie[$m['id']] ?? []) as $st): ?>
              <a class="slot" href="/cinemax/book.php?showtime_id=<?=$st['id']?>">
                <b><?=date('M d, H:i', strtotime($st['show_datetime']))?></b> ·
                <?=htmlspecialchars($st['hall'])?> · $<?=number_format($st['price'],2)?>
              </a>
            <?php endforeach; ?>
            <?php if(empty($byMovie[$m['id']] ?? [])): ?>
              <em>No upcoming showtimes.</em>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>
</body>
</html>