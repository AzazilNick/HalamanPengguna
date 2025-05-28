<?php
// niflix_project/app/Views/series/index.php
// $series, $title, $message, $message_type akan tersedia dari SeriesController

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
    <div class="card bg-dark p-4 rounded shadow-lg text-white series-container">
        <h1 class="text-warning text-center mb-4"><?= escape_html($title) ?></h1>

        <?php if (isset($message) && $message): ?>
            <div class="alert <?= escape_html($message_type ?? '') == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-4" role="alert"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($series)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($series as $s): ?>
                <div class="col">
                    <div class="card h-100 bg-secondary text-white border-dark shadow-sm">
                        <?php if (!empty($s['image_url'])): ?>
                            <img src="<?= escape_html($s['image_url']) ?>" class="card-img-top" alt="<?= escape_html($s['title']) ?>" style="height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h2 class="card-title h5"><a href="<?= $basePath ?>/daftar_series/show/<?= escape_html($s['id']) ?>"><?= escape_html($s['title']) ?></a></h2>
                            <p class="card-text text-muted small">Tahun Rilis: <?= escape_html($s['release_year']) ?></p>
                            <p class="card-text"><?= escape_html(substr($s['description'], 0, 150)) ?>...</p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-end">
                            <a href="<?= $basePath ?>/daftar_series/show/<?= escape_html($s['id']) ?>" class="btn btn-niflix btn-sm">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-white-50">Belum ada series yang ditambahkan.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>