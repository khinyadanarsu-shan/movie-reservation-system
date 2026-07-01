<?php
require_once __DIR__ . '/db.php';
$pdo = db();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// 1) First visit (POST from showtime.php): show summary
if ($method === 'POST' && empty($_GET['action'])) {
  $showtime_id = (int)($_POST['showtime_id'] ?? 0);
  $seat_ids = array_map('intval', $_POST['seats'] ?? []);
  if ($showtime_id <= 0 || empty($seat_ids)) exit('Please select at least one seat.');

  $st = $pdo->prepare("
    SELECT s.id, s.price, s.show_datetime, a.name AS hall, m.title
    FROM showtimes s
    JOIN auditoriums a ON a.id = s.auditorium_id
    JOIN movies m ON m.id = s.movie_id
    WHERE s.id = ?
  ");
  $st->execute([$showtime_id]);
  $show = $st->fetch() ?: exit('Showtime not found');

  $in = implode(',', array_fill(0, count($seat_ids), '?'));
  $ss = $pdo->prepare("SELECT id, row_label, seat_number, seat_type FROM seats WHERE id IN ($in) ORDER BY row_label, seat_number");
  $ss->execute($seat_ids);
  $seats = $ss->fetchAll();

  $base = (float)$show['price'];
  $total = 0.0;
  foreach ($seats as $s) $total += ($s['seat_type']==='couple') ? $base*1.5 : $base;

  ?>
  <!doctype html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Checkout</title>
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
    <h2>Checkout</h2>
    <p class="muted"><?= htmlspecialchars($show['title']) ?> • <?= htmlspecialchars($show['hall']) ?> • <?= date('D, M j · g:i A', strtotime($show['show_datetime'])) ?></p>

    <div class="card soft">
      <h3>Your seats</h3>
      <ul class="seat-list">
        <?php foreach ($seats as $s): ?>
          <li>Row <b><?= htmlspecialchars($s['row_label']) ?></b> Seat <b><?= (int)$s['seat_number'] ?></b> (<?= $s['seat_type'] ?>)</li>
        <?php endforeach; ?>
      </ul>
     <div class="total">Total: <b>$<?= number_format((float)$total, 2, '.', ',') ?></b></div>
    </div>

    <form action="checkout.php?action=pay" method="post" class="pay-form">
      <input type="hidden" name="showtime_id" value="<?= (int)$showtime_id ?>">
      <?php foreach ($seat_ids as $sid): ?>
        <input type="hidden" name="seats[]" value="<?= (int)$sid ?>">
      <?php endforeach; ?>
      <input class="input" type="text" name="name" placeholder="Your name (optional)">
      <button class="btn large" type="submit">Pay Now</button>
    </form>
  </main>
  </body>
  </html>
  <?php
  exit;
}

// 2) Pay action: create booking rows and redirect to ticket
if ($method === 'POST' && ($_GET['action'] ?? '') === 'pay') {
  $showtime_id = (int)($_POST['showtime_id'] ?? 0);
  $seat_ids = array_map('intval', $_POST['seats'] ?? []);
  $name = trim($_POST['name'] ?? 'Guest');

  if ($showtime_id <= 0 || empty($seat_ids)) { http_response_code(400); exit('Missing data'); }

  try {
    $pdo->beginTransaction();

    // Lock/check seats availability
    $in = implode(',', array_fill(0, count($seat_ids), '?'));
    $chk = $pdo->prepare("
      SELECT bs.seat_id FROM booking_seats bs
      JOIN bookings b ON b.id = bs.booking_id
      WHERE b.showtime_id = ? AND b.status IN ('reserved','paid') AND bs.seat_id IN ($in)
      FOR UPDATE
    ");
    $chk->execute(array_merge([$showtime_id], $seat_ids));
    if ($chk->fetch()) {
      $pdo->rollBack();
      exit('Some seats were just taken. Please go back and refresh.');
    }

    // Create booking (mark paid for demo)
    $ins = $pdo->prepare("INSERT INTO bookings (showtime_id, customer_name, status) VALUES (?, ?, 'paid')");
    $ins->execute([$showtime_id, $name]);
    $booking_id = (int)$pdo->lastInsertId();

    // Link seats
    $bs = $pdo->prepare("INSERT INTO booking_seats (booking_id, seat_id) VALUES (?, ?)");
    foreach ($seat_ids as $sid) $bs->execute([$booking_id, $sid]);

    $pdo->commit();
    header('Location: ticket.php?id=' . $booking_id);
    exit;
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo "Payment failed: " . htmlspecialchars($e->getMessage());
    exit;
  }
}

// Fallback
header('Location: index.php');
exit;