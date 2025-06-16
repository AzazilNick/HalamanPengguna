<?php
    require_once APP_ROOT . '/app/Views/includes/header.php';

    // Pastikan base Path tersedia untuk tautan
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath === '/') {
        $basePath = '';
    } else {
        $basePath = rtrim($basePath, '/');
    }
?>

<main>
    <div class="review-container">
        <a href="<?= $basePath ?>/review_series" class="btn-back">← Kembali ke Daftar Review</a>

        <h2><?= htmlspecialchars($review['series_title'] ?? 'Tidak Diketahui') ?></h2>
        <p><strong>Reviewer:</strong> <?= htmlspecialchars($review['username'] ?? 'Anonim') ?></p>

        <?php if (isset($review['rating'])): ?>
            <p><strong>Rating:</strong> <?= (int)$review['rating'] ?>/10</p>
        <?php endif; ?>

        <p><strong>Ulasan:</strong></p>
        <p><?= nl2br(htmlspecialchars($review['review_text'] ?? '')) ?></p>
    </div>
</main>

<?php
    require_once APP_ROOT . '/app/Views/includes/footer.php';
?>