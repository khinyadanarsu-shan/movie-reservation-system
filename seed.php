<?php
require_once __DIR__ . '/db.php';
ensure_basic_data();
$pdo = db();

$movies = [
  ['title'=>'The Grand Heist','poster_url'=>'https://image.tmdb.org/t/p/w342/2.jpg','rating'=>'PG-13','duration_mins'=>118,'description'=>'High-stakes thriller.'],
  ['title'=>'Starlight','poster_url'=>'https://image.tmdb.org/t/p/w342/3.jpg','rating'=>'U','duration_mins'=>95,'description'=>'Family space adventure.'],
];

foreach($movies as $m){
  $stmt = $pdo->prepare('SELECT id FROM movies WHERE title=?'); $stmt->execute([$m['title']]);
  if(!$stmt->fetch()){
    $ins = $pdo->prepare('INSERT INTO movies(title,poster_url,rating,duration_mins,description) VALUES(?,?,?,?,?)');
    $ins->execute([$m['title'],$m['poster_url'],$m['rating'],$m['duration_mins'],$m['description']]);
  }
}

$movie_id = (int)$pdo->query("SELECT id FROM movies ORDER BY id ASC LIMIT 1")->fetchColumn();
if ($movie_id) {
  $exists = (int)$pdo->query("SELECT COUNT(*) FROM showtimes WHERE movie_id={$movie_id}")->fetchColumn();
  if ($exists === 0) {
    $ins=$pdo->prepare('INSERT INTO showtimes(movie_id,auditorium_id,show_datetime,price) VALUES(1,1,?,?)');
    $ins->execute([date('Y-m-d 18:00:00', strtotime('+1 day')), 5.00]);
    $ins->execute([date('Y-m-d 20:00:00', strtotime('+1 day')), 5.00]);
  }
}
echo "Seeded.";