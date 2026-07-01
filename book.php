<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/db.php';
$pdo = db();

$showtime_id = (int)($_GET['showtime_id'] ?? 0);
if ($showtime_id <= 0) { http_response_code(404); exit('Not found'); }

$st = $pdo->prepare("SELECT s.*, m.title AS movie, a.name AS hall, a.`rows`, a.`cols`, a.id AS aud_id
  FROM showtimes s
  JOIN movies m ON m.id=s.movie_id
  JOIN auditoriums a ON a.id=s.auditorium_id
  WHERE s.id=?");
$st->execute([$showtime_id]);
$show = $st->fetch(); if(!$show){ exit('Showtime not found'); }

$booked = $pdo->prepare("SELECT seat_id FROM booking_seats WHERE showtime_id=?");
$booked->execute([$showtime_id]);
$booked_ids = array_column($booked->fetchAll(), 'seat_id');
$booked_map = array_flip($booked_ids);

$seats = $pdo->prepare("SELECT id,row_label,seat_number FROM seats WHERE auditorium_id=? ORDER BY row_label, seat_number");
$seats->execute([$show['aud_id']]);
$byRow = [];
foreach($seats as $s){ $byRow[$s['row_label']][] = $s; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><title>Choose seats – <?=htmlspecialchars($show['movie'])?></title>
  <link rel="stylesheet" href="/cinemax/assets/style.css">
</head>
<body>
<header class="nav"><div class="brand">CineMax</div></header>
<main class="container">
  <h2><?=htmlspecialchars($show['movie'])?> — <?=date('M d, H:i', strtotime($show['show_datetime']))?> — <?=htmlspecialchars($show['hall'])?></h2>
  <form method="post" action="/cinemax/book_submit.php">
    <input type="hidden" name="showtime_id" value="<?=$showtime_id?>">
    <div class="seats">
      <?php foreach($byRow as $row=>$list): ?>
        <div class="row"><span class="rowlabel"><?=$row?></span>
          <?php foreach($list as $s): $taken = isset($booked_map[$s['id']]); ?>
            <label class="seat <?=$taken?'taken':''?>">
              <input type="checkbox" name="seat_ids[]" value="<?=$s['id']?>" <?=$taken?'disabled':''?>>
              <?=$s['seat_number']?>
            </label>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <p>Price per seat: $<?=number_format($show['price'],2)?>. Selected seats will be reserved when you confirm.</p>
    <button type="submit">Confirm Booking</button>
  </form>
</main>
</body>
</html>