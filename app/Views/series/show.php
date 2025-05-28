<?php
// niflix_project/app/Views/series/show.php
// $series, $title akan tersedia dari SeriesController

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php';

// Pastikan base Path tersedia untuk tautan
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}
?>

<div class="container py-4">
    <a href="<?= $basePath ?>/daftar_series" class="btn btn-outline-light mb-4">← Kembali ke Daftar Series</a>

    <?php if ($series): ?>
        <article class="card bg-dark text-white border-secondary shadow-lg p-4">
            <div class="card-body">
                <h1 class="card-title text-warning mb-3 text-center"><?= escape_html($series['title']) ?></h1>
                <?php if (!empty($series['image_url'])): ?>
                    <img src="<?= escape_html($series['image_url']) ?>" class="img-fluid rounded mb-4 d-block mx-auto" alt="<?= escape_html($series['title']) ?>" style="max-width: 500px; object-fit: cover;">
                <?php endif; ?>
                <p class="card-text text-muted text-center mb-4 border-bottom border-secondary pb-3">Tahun Rilis: <strong class="text-white"><?= escape_html($series['release_year']) ?></strong></p>
                <div class="card-text lead">
                    <?= nl2br(escape_html($series['description'])) ?>
                </div>
            </div>
        </article>
    <?php else: ?>
        <p class="text-center text-white-50">Series tidak ditemukan.</p>
    <?php endif; ?>
</div>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>