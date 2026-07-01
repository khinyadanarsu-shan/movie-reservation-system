<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/db.php';

$pdo  = db();
$user = current_user();

$showtime_id = (int)($_POST['showtime_id'] ?? 0);
$seat_ids    = array_map('intval', $_POST['seat_ids'] ?? []);
if ($showtime_id <= 0 || empty($seat_ids)) { http_response_code(400); exit('Invalid request'); }

$st = $pdo->prepare("
  SELECT s.*, m.title AS movie, a.name AS hall
  FROM showtimes s
  JOIN movies m ON m.id = s.movie_id
  JOIN auditoriums a ON a.id = s.auditorium_id
  WHERE s.id = ?
");
$st->execute([$showtime_id]);
$show = $st->fetch();
if (!$show) exit('Showtime not found');

$total = (float)$show['price'] * count($seat_ids);

$pdo->beginTransaction();
try {
  $stmt = $pdo->prepare("INSERT INTO bookings(user_id, showtime_id, total_price) VALUES(?,?,?)");
  $stmt->execute([$user['id'], $showtime_id, $total]);
  $booking_id = (int)$pdo->lastInsertId();

  $ins = $pdo->prepare("INSERT INTO booking_seats(booking_id, showtime_id, seat_id) VALUES(?,?,?)");
  foreach ($seat_ids as $sid) {
    $ins->execute([$booking_id, $showtime_id, $sid]);
  }

  $pdo->commit();
} catch (\Exception $e) {
  $pdo->rollBack();
  if (strpos($e->getMessage(), 'uniq_showtime_seat') !== false) {
    exit('One or more seats were just booked by another user. Please pick different seats.');
  }
  exit('Booking failed: ' . htmlspecialchars($e->getMessage()));
}

header('Location: /cinemax/my_bookings.php');
exit;