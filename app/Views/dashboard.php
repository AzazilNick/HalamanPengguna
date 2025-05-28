<?php
// niflix_project/app/Views/dashboard.php
// $user_username, $movies, $series akan tersedia dari DashboardController

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php';
?>

    <div class="container py-4">
        <div class="welcome-message mb-4 p-3 rounded">
            <h2 class="text-warning">Welcome, <?= escape_html($user_username) ?>!</h2>
        </div>

        <section class="mb-5">
            <div id="movieCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($movies as $key => $movie): ?>
                        <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>">
                            <img src="<?= escape_html($movie['image']) ?>" class="d-block w-100 rounded" alt="<?= escape_html($movie['title']) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#movieCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#movieCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>

        <section class="mb-5">
            <h1 class="text-warning text-center mb-4">Daftar Film</h1>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
                <?php foreach ($movies as $movie): ?>
                    <div class="col">
                        <div class="card h-100 bg-dark text-white border-secondary shadow-sm">
                            <img src="<?= escape_html($movie['image']) ?>" class="card-img-top" alt="<?= escape_html($movie['title']) ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= escape_html($movie['title']) ?></h5>
                                <a href="<?= $basePath ?>/review_film?film=<?= urlencode($movie['title']) ?>" class="btn btn-niflix btn-sm mt-2">Review</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <h1 class="text-warning text-center mb-4">Daftar Series</h1>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
                <?php foreach ($series as $s): ?>
                    <div class="col">
                        <div class="card h-100 bg-dark text-white border-secondary shadow-sm">
                            <img src="<?= escape_html($s['image']) ?>" class="card-img-top" alt="<?= escape_html($s['title']) ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= escape_html($s['title']) ?></h5>
                                <a href="<?= $basePath ?>/review_series?series=<?= urlencode($s['title']) ?>" class="btn btn-niflix btn-sm mt-2">Review</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

<?php
// Memuat footer (tag main akan ditutup di sini)
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>