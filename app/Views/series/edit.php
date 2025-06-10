<?php
// niflix_project/app/Views/series/edit.php

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php'; //

// Pastikan base Path tersedia untuk tautan
$basePath = dirname($_SERVER['SCRIPT_NAME']); //
if ($basePath === '/') { //
    $basePath = ''; //
} else {
    $basePath = rtrim($basePath, '/'); //
}

// Path lengkap ke gambar series - langsung dari database
$seriesImageUrl = escape_html($series['image_url']); //
?>

<main>
    <div class="form-container">
        <h1><?= escape_html($title) ?>: <?= escape_html($series['title']) ?></h1>

        <?php if (isset($message) && $message): ?>
            <div class="notification <?= escape_html($message_type ?? '') ?>"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <?php if ($series): ?>
            <form id="editSeriesForm" action="<?= $basePath ?>/daftar_series/edit/<?= escape_html($series['id']) ?>" method="POST">
                <div class="input-group">
                    <label for="title">Judul Series:</label>
                    <input type="text" id="title" name="title" required value="<?= escape_html($series['title']) ?>">
                    <span id="titleValidation" class="validation-message"></span>
                </div>

                <div class="input-group">
                    <label for="description">Deskripsi Series:</label>
                    <textarea id="description" name="description" rows="10" required><?= escape_html($series['description']) ?></textarea>
                </div>

                <div class="input-group">
                    <label for="release_year">Tahun Rilis:</label>
                    <input type="number" id="release_year" name="release_year" required value="<?= escape_html($series['release_year']) ?>">
                    <span id="releaseYearValidation" class="validation-message"></span>
                </div>

                <div class="input-group">
                    <label for="image_url">URL Gambar Series:</label>
                    <?php if ($seriesImageUrl): ?>
                        <img src="<?= $seriesImageUrl ?>" alt="Current Series Image" style="max-width: 150px; height: auto; margin-bottom: 10px; border-radius: 5px;">
                        <br>
                    <?php endif; ?>
                    <input type="text" id="image_url" name="image_url" placeholder="http://example.com/image.jpg" value="<?= $seriesImageUrl ?>">
                    <span id="imageUrlValidation" class="validation-message"></span>
                    <small>Masukkan URL lengkap gambar series.</small>
                </div>

                <div class="input-group">
                    <label for="is_popular">Status Popular:</label>
                    <select id="is_popular" name="is_popular">
                        <option value="0" <?= $series['is_popular'] == 0 ? 'selected' : '' ?>>Tidak</option>
                        <option value="1" <?= $series['is_popular'] == 1 ? 'selected' : '' ?>>Ya</option>
                    </select>
                </div>

                <?php if (!empty($series['creator_username'])): ?>
                    <div class="input-group">
                        <label>Dibuat Oleh:</label>
                        <p><strong><?= escape_html($series['creator_fullname'] ?: $series['creator_username']) ?></strong></p>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn">Perbarui Series</button>
                <a href="<?= $basePath ?>/daftar_series/edit/<?= escape_html($series['id']) ?>" class="btn btn-cancel">Batal</a>
            </form>
        <?php else: ?>
            <p>Series tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</main>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php'; //
?>