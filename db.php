<?php
function cfg() {
  static $c = null;
  if (!$c) { $c = require __DIR__ . '/config.php'; }
  return $c;
}

function db() {
  static $pdo = null;
  if ($pdo) return $pdo;
  $c = cfg()['db'];
  $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset={$c['charset']}";
  $pdo = new PDO($dsn, $c['user'], $c['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}

function ensure_basic_data() {
  $pdo = db();
  // Auditorium
  $pdo->exec("INSERT IGNORE INTO auditoriums(id,name,`rows`,`cols`) VALUES (1,'Hall A',10,12)");
  // Seats for Hall A if empty
  $count = (int)$pdo->query("SELECT COUNT(*) FROM seats WHERE auditorium_id=1")->fetchColumn();
  if ($count === 0) {
    for ($r = 0; $r < 10; $r++) {
      $row_label = chr(65 + $r);
      for ($c = 1; $c <= 12; $c++) {
        $stmt = $pdo->prepare("INSERT INTO seats(auditorium_id,row_label,seat_number) VALUES(1,?,?)");
        $stmt->execute([$row_label, $c]);
      }
    }
  }
}