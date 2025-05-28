<?php
// niflix_project/app/Views/includes/header.php

$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Niflix App' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/niflix_custom.css">
    <?php
    // Dynamically load page-specific override CSS based on the current URI
    $requestUri = $_SERVER['REQUEST_URI'];
    $path = parse_url($requestUri, PHP_URL_PATH);
    $pathSegments = explode('/', trim($path, '/'));

    $overrideCss = '';
    if (in_array('auth', $pathSegments)) {
        $overrideCss = 'auth_override.css';
    } elseif (in_array('dashboard', $pathSegments)) {
        $overrideCss = 'dashboard_override.css';
    } elseif (in_array('articles', $pathSegments) || in_array('comment', $pathSegments)) {
        $overrideCss = 'articles_override.css';
    } elseif (in_array('admin', $pathSegments)) {
        $overrideCss = 'admin_override.css';
    } elseif (in_array('profile', $pathSegments)) {
        $overrideCss = 'profile_override.css';
    } elseif (in_array('daftar_series', $pathSegments)) {
        $overrideCss = 'series_override.css';
    }

    if ($overrideCss) {
        echo '<link rel="stylesheet" href="' . $basePath . '/assets/css/' . $overrideCss . '">';
    }
    ?>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= $basePath ?>/dashboard">Niflix</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/articles">Artikel</a>
                        </li>
                        <?php if (Session::has('user') && Session::get('user')['is_admin'] == 1) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $basePath ?>/admin">Kelola Akun</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/review_film">Review Film</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/review_series">Review Series</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/daftar_film">Daftar Film</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/daftar_series">Daftar Series</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/komentar_rating">Komentar & Rating</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/profile">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?>/auth/logout">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="flex-grow-1 pt-4 pb-4">