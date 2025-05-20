<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <header>
        <button class="menu-toggle">☰</button>
        <nav class="nav-menu">
            <ul>
                <?php if ($_SESSION['user']['is_admin'] == 1) : ?>
                    <!-- TAMBAHKAN MENU ADMIN -->
                    <li><a href="admin.php">Kelola Akun</a></li>
                <?php endif; ?>
                <li><a href="review_film.php">Review Film</a></li>
                <li><a href="review_series.php">Review Series</a></li>
                <li><a href="daftar_film.php">Daftar Film</a></li>
                <li><a href="daftar_series.php">Daftar Series</a></li>
                <li><a href="komentar_rating.php">Komentar & Rating</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout_process.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
