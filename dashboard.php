<?php
    session_start();
    
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    // Data film dan series
    $movies = [
        // Array berisi data film dengan judul dan URL gambar
        ["title" => "Inception", "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJyjBC4dx19LTH6CBmbDIpNCrelbYJSplrUA&s"],
        ["title" => "Interstellar", "image" => "https://upload.wikimedia.org/wikipedia/id/b/bc/Interstellar_film_poster.jpg"],
        ["title" => "The Dark Knight", "image" => "https://upload.wikimedia.org/wikipedia/id/8/8a/Dark_Knight.jpg"],
        ["title" => "Avatar", "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpypb6nI7UrJtPHuHDnzAsO5_tP1uwd_raIw&s"],
        ["title" => "The Matrix", "image" => "https://images-cdn.ubuy.co.id/63497d4c524b6263e43a00ee-the-matrix-movie-poster-us-version-24x36.jpg"]
    ];

    $series = [
        // Array berisi data series dengan judul dan URL gambar
        ["title" => "Stranger Things", "image" => "https://awsimages.detik.net.id/community/media/visual/2017/10/23/d94f3168-b35d-4db2-844f-93b4d463261b.jpg?w=600&q=90"],
        ["title" => "Breaking Bad", "image" => "https://m.media-amazon.com/images/I/51fWOBx3agL.AC_UF894,1000_QL80.jpg"],
        ["title" => "Game of Thrones", "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTX60Jzu04x_G8OHcBGmy_GK6T4X1jLZgQ-JA&s"],
        ["title" => "The Walking Dead", "image" => "https://upload.wikimedia.org/wikipedia/id/thumb/0/0e/TheWalkingDeadPoster.jpg/220px-TheWalkingDeadPoster.jpg"],
        ["title" => "Sherlock", "image" => "https://m.media-amazon.com/images/I/51+LSKG5-FL.jpg"]
    ];

    include_once('header.php');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style_dashboard.css"> <!-- Menghubungkan file CSS -->
</head>
<body>
    <div class="welcome-message">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?>!</h2>
    </div>
    <main>
    <section>
        <div class="slider-wrapper">
            <div class="slider-container">
                <?php foreach ($movies as $movie): ?>
                    <div class="slider-item">
                        <img src="<?= $movie['image'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>"> <!-- Menampilkan gambar film -->
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section>
        <h1>Daftar Film</h1>
        <div class="grid-container">
            <?php foreach ($movies as $movie): ?>
                <div class="grid-item">
                    <img src="<?= $movie['image'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>"> <!-- Menampilkan gambar film -->
                    <h4><?= htmlspecialchars($movie['title']) ?></h4> <!-- Menampilkan judul film -->
                    <a href="review_film.php?film=<?= urlencode($movie['title']) ?>" class="btn">Review</a> <!-- Tautan untuk mereview film -->
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h1>Daftar Series</h1>
        <div class="grid-container">
            <?php foreach ($series as $s): ?>
                <div class="grid-item">
                    <img src="<?= $s['image'] ?>" alt="<?= htmlspecialchars($s['title']) ?>"> <!-- Menampilkan gambar series -->
                    <h4><?= htmlspecialchars($s['title']) ?></h4> <!-- Menampilkan judul series -->
                    <a href="review_series.php?series=<?= urlencode($s['title']) ?>" class="btn">Review</a> <!-- Tautan untuk mereview series -->
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</body>
</html>

<?php
    include_once('footer.php');
?>
