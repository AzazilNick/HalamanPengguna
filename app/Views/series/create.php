<?php
// niflix_project/app/Views/series/create.php

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php';

// Pastikan base Path tersedia untuk tautan
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}

// Ambil data dari $series jika ada (untuk pre-fill form setelah validasi gagal)
$seriesTitle = $series['title'] ?? '';
$seriesDescription = $series['description'] ?? '';
$seriesReleaseYear = $series['release_year'] ?? '';
$seriesImageUrl = $series['image_url'] ?? ''; // Ambil URL gambar yang di-submit
$is_popular = $series['is_popular'] ?? '';
?>

<main>
    <div class="form-container">
        <h1><?= escape_html($title) ?></h1>

        <?php if (isset($message) && $message): ?>
            <div class="notification <?= escape_html($message_type ?? '') ?>"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <form action="<?= $basePath ?>/daftar_series/create" method="POST"> <div class="input-group">
                <label for="title">Judul Series:</label>
                <input type="text" id="title" name="title" required value="<?= escape_html($seriesTitle) ?>">
            </div>

            <div class="input-group">
                <label for="description">Deskripsi Series:</label>
                <textarea id="description" name="description" rows="10" required><?= escape_html($seriesDescription) ?></textarea>
            </div>

            <div class="input-group">
                <label for="release_year">Tahun Rilis:</label>
                <input type="number" id="release_year" name="release_year" required value="<?= escape_html($seriesReleaseYear) ?>">
            </div>

            <div class="input-group">
                <label for="image_url">URL Gambar Series (Opsional):</label>
                <input type="text" id="image_url" name="image_url" placeholder="http://example.com/image.jpg" value="<?= escape_html($seriesImageUrl) ?>">
                <small>Masukkan URL lengkap gambar series.</small>
            </div>

            <div class="input-group">
                    <label for="is_popular">Status Popular:</label>
                    <select id="is_popular" name="is_popular">
                        <option value="NO" <?= $series['is_popular'] == 0 ? 'selected' : '' ?>>Tidak</option>
                        <option value="YES" <?= $series['is_popular'] == 1 ? 'selected' : '' ?>>Ya</option>
                    </select>
            </div>

            <button type="submit" class="btn">Tambah Series</button>
            <a href="<?= $basePath ?>/daftar_series" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</main>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>
