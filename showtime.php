<?php
require_once __DIR__ . '/db.php';
$pdo = db();

$showtime_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($showtime_id <= 0) { http_response_code(400); exit('Invalid showtime'); }

// Fetch showtime + hall + movie
$st = $pdo->prepare("
  SELECT s.id, s.show_datetime, s.price,
         a.id AS auditorium_id, a.name AS hall, a.total_rows, a.seats_per_row,
         m.title
  FROM showtimes s
  JOIN auditoriums a ON a.id = s.auditorium_id
  JOIN movies m ON m.id = s.movie_id
  WHERE s.id = ?
");
$st->execute([$showtime_id]);
$show = $st->fetch();
if (!$show) exit('Showtime not found');

// Seats for this hall
$seats = $pdo->prepare("
  SELECT id, row_label, seat_number, seat_type
  FROM seats
  WHERE auditorium_id = ?
  ORDER BY row_label, seat_number
");
$seats->execute([$show['auditorium_id']]);
$allSeats = $seats->fetchAll();

// Already taken seats (reserved/paid)
$taken = $pdo->prepare("
  SELECT bs.seat_id
  FROM booking_seats bs
  JOIN bookings b ON b.id = bs.booking_id
  WHERE b.showtime_id = ? AND b.status IN ('reserved','paid')
");
$taken->execute([$showtime_id]);
$takenIds = array_column($taken->fetchAll(), 'seat_id');

// Group seats by row_label
$byRow = [];
foreach ($allSeats as $s) $byRow[$s['row_label']][] = $s;
$rows = array_keys($byRow); sort($rows);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($show['title']) ?> – <?= htmlspecialchars($show['hall']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="assets/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<header class="nav">
  <a class="brand" href="index.php">🎬 CINEMAX</a>
  <nav><a href="index.php">Home</a></nav>
</header>

<main class="container narrow">
  <div class="ticket-head">
    <div>
      <h2><?= htmlspecialchars($show['title']) ?></h2>
      <p class="muted"><?= htmlspecialchars($show['hall']) ?> • <?= date('D, M j · g:i A', strtotime($show['show_datetime'])) ?></p>
    </div>
   <div class="pill">Base $<?= number_format((float)$show['price'], 2, '.', ',') ?></div>
  </div>

  <form action="checkout.php" method="post" id="seatForm">
    <input type="hidden" name="showtime_id" value="<?= (int)$showtime_id ?>">

    <div class="legend">
      <span><i class="box single"></i>Single</span>
      <span><i class="box couple"></i>Couple</span>
      <span><i class="box taken"></i>Taken</span>
      <span><i class="box selected"></i>Selected</span>
    </div>

    <div class="screen">SCREEN</div>

    <div class="seats">
      <?php foreach ($rows as $rl): ?>
        <div class="seat-row">
          <div class="row-label"><?= htmlspecialchars($rl) ?></div>
          <div class="row-seats">
            <?php foreach ($byRow[$rl] as $seat):
              $isTaken = in_array($seat['id'], $takenIds, true);
              $classes = ['seat', $seat['seat_type']];
              if ($isTaken) $classes[] = 'taken';
            ?>
              <label class="<?= implode(' ', $classes) ?>" title="Row <?= htmlspecialchars($seat['row_label']) ?> Seat <?= (int)$seat['seat_number'] ?>">
                <input type="checkbox" name="seats[]" value="<?= (int)$seat['id'] ?>" <?= $isTaken ? 'disabled' : '' ?>>
                <span><?= (int)$seat['seat_number'] ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="checkout-bar">
      <div><strong id="count">0</strong> seat(s) • Est. Total: $<strong id="total">0.00</strong></div>
      <button class="btn large" type="submit">Continue</button>
    </div>
  </form>
</main>

<script>
  window.BASE_PRICE = <?= json_encode((float)$show['price']) ?>;
</script>
<script src="assets/script.js"></script>
</body>
</html>