<?php
// niflix_project/app/Views/series/index.php
// $popularSeries, $allSeries, $title, $message, $message_type akan tersedia dari SeriesController

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php';

// Pastikan base Path tersedia untuk tautan
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}

// Cek apakah pengguna adalah admin
$currentUser = Session::get('user');
$isAdmin = isset($currentUser) && $currentUser['is_admin'] == 1;
?>

<main>
    <div class="series-container articles-container">
        <h1>Series Populer</h1>

        <?php if (isset($message) && $message): ?>
            <div class="notification <?= escape_html($message_type ?? '') ?>"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <?php if ($isAdmin): // Tampilkan tombol tambah series hanya untuk admin ?>
            <p><a href="<?= $basePath ?>/daftar_series/create" class="btn btn-create-article">Tambah Series Baru</a></p>
        <?php endif; ?>

        <?php if (!empty($popularSeries)): // Gunakan $popularSeries untuk slider ?>
            <div class="slider-wrapper">
                <button class="slider-arrow left-arrow">&#10094;</button>
                <div class="slider-container">
                    <?php foreach ($popularSeries as $s): ?>
                        <div class="slider-item">
                            <a href="<?= $basePath ?>/daftar_series/show/<?= escape_html($s['id']) ?>">
                                <?php if (!empty($s['image_url'])): ?>
                                    <img src="<?= escape_html($s['image_url']) ?>" alt="<?= escape_html($s['title']) ?>" class="series-thumbnail">
                                <?php else: ?>
                                    <img src="<?= $basePath ?>/assets/img/default_series_thumb.png" alt="No Image" class="series-thumbnail">
                                <?php endif; ?>
                                <h4><?= escape_html($s['title']) ?></h4>
                            </a>

                            <?php if ($isAdmin): // Tampilkan tombol edit/hapus hanya untuk admin ?>
                                <div class="series-actions">
                                    <a href="<?= $basePath ?>/daftar_series/edit/<?= escape_html($s['id']) ?>" class="btn-edit">Edit</a>
                                    <a href="<?= $basePath ?>/daftar_series/delete/<?= escape_html($s['id']) ?>"
                                       onclick="return confirm('Yakin ingin menghapus series ini?')" class="btn-delete">Hapus</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="slider-arrow right-arrow">&#10095;</button>
            </div>
        <?php else: ?>
            <p class="info-message">Belum ada series populer yang ditambahkan.</p>
        <?php endif; ?>

        <h1>Daftar Series</h1>

        <?php if (!empty($allSeries)): ?>
            <div class="series-list article-list">
                <?php foreach ($allSeries as $s): ?>
                        <div class="series-item article-item">
                            <?php if (!empty($s['image_url'])): ?>
                                <a href="<?= $basePath ?>/daftar_series/show/<?= escape_html($s['id']) ?>">
                                <img src="<?= escape_html($s['image_url']) ?>" alt="<?= escape_html($s['title']) ?>" class="series-thumbnail">
                                </a>
                            <?php endif; ?>
                        </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="info-message">Belum ada series yang ditambahkan.</p>
        <?php endif; ?>

    </div>
</main>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>
