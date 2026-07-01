<?php
require_once __DIR__ . '/auth.php';
require_login();
if (!is_admin()) exit('Forbidden');

require_once __DIR__ . '/db.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (($_POST['action'] ?? '') === 'create') {
    $stmt = $pdo->prepare('INSERT INTO movies(title,poster_url,rating,duration_mins,description) VALUES(?,?,?,?,?)');
    $stmt->execute([
        $_POST['title'],
        $_POST['poster_url'],
        $_POST['rating'],
        (int)$_POST['duration_mins'],
        $_POST['description']
    ]);
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM movies WHERE id=?');
    $stmt->execute([(int)$_POST['id']]);
  }
}

$movies = $pdo->query('SELECT * FROM movies ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Movies</title>
  <link rel="stylesheet" href="/cinemax/assets/style.css">
  <style>
    .back-btn {
      display: inline-block;
      background: #005CCC;
      color: #fff;
      padding: 8px 15px;
      margin-bottom: 15px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.2s;
    }
    .back-btn:hover {
      background: #003f8a;
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

  <h2>Movies</h2>

  <form method="post" class="card">
    <input type="hidden" name="action" value="create">

    <input name="title" placeholder="Title" required>

    <input name="poster_url" placeholder="Poster URL">

    <input name="rating" placeholder="PG-13, U, etc">

    <input type="number" name="duration_mins" placeholder="Duration (mins)">

    <textarea name="description" placeholder="Description"></textarea>

    <button>Add Movie</button>
  </form>

  <table class="table">
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Rating</th>
      <th>Duration</th>
      <th></th>
    </tr>

    <?php foreach($movies as $m): ?>
    <tr>
      <td><?= $m['id'] ?></td>
      <td><?= htmlspecialchars($m['title']) ?></td>
      <td><?= htmlspecialchars($m['rating']) ?></td>
      <td><?= (int)$m['duration_mins'] ?></td>
      <td>
        <form method="post" onsubmit="return confirm('Delete movie?')">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $m['id'] ?>">
          <button>Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>

  </table>

</main>

</body>
</html>