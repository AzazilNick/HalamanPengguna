<?php
// niflix_project/app/Views/komentar_rating/show.php

require_once APP_ROOT . '/app/Views/includes/header.php';

$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}

// Ensure $item is available
if (!$item) {
    echo "<main><div class='articles-container'><p>Item tidak ditemukan.</p></div></main>";
    require_once APP_ROOT . '/app/Views/includes/footer.php';
    exit();
}
?>

<main>
    <div class="article-detail-container">
        <a href="<?= $basePath ?>/komentar_rating" class="btn btn-back">← Kembali ke Daftar Komentar & Rating</a>

        <?php if (isset($message) && $message): ?>
            <div class="notification <?= escape_html($message_type ?? '') ?>"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <article class="single-article">
            <h1><?= escape_html($item['title']) ?></h1>
            <?php if (!empty($item['image_url'])): ?>
                <img src="<?= escape_html($item['image_url']) ?>" alt="<?= escape_html($item['title']) ?>" class="<?= $itemType ?>-full-image">
            <?php endif; ?>
            <p class="<?= $itemType ?>-meta">Tahun Rilis: <?= escape_html($item['release_year']) ?></p>
            <p class="<?= $itemType ?>-meta">Deskripsi: <?= nl2br(escape_html($item['description'])) ?></p>
            <p class="<?= $itemType ?>-meta">Rating Rata-rata: <strong><?= number_format($item['average_rating'], 1) ?>/10</strong> (dari <?= escape_html($item['total_reviews']) ?> ulasan)</p>
            <p class="<?= $itemType ?>-meta">Total Suka: <strong><?= escape_html($item['total_likes']) ?></strong></p>

            <div class="item-actions-bottom">
                <form action="<?= $basePath ?>/komentar_rating/show/<?= escape_html($item_type) ?>/<?= escape_html($item['id']) ?>" method="POST" style="display:inline-block;">
                    <input type="hidden" name="action" value="toggle_like">
                    <button type="submit" class="btn btn-like">
                        <i class='bx <?= $isLiked ? 'bxs-heart' : 'bx-heart' ?>'></i> <?= $isLiked ? 'Disukai' : 'Suka' ?>
                    </button>
                </form>
            </div>
        </article>

        <section class="comments-section">
            <h2>Ulasan Pengguna (<?= count($reviews) ?>)</h2>

            <?php
            // Determine initial values for the form
            $currentReviewText = $userReview['review_text'] ?? '';
            $currentRating = $userReview['rating'] ?? 0;
            ?>

            <div class="comment-form">
                <h3><?= $hasUserReviewed ? 'Edit Ulasan Anda' : 'Tambahkan Ulasan Anda' ?></h3>
                <form action="<?= $basePath ?>/komentar_rating/show/<?= escape_html($item_type) ?>/<?= escape_html($item['id']) ?>" method="POST">
                    <div class="input-group">
                        <label for="rating">Rating (1-10):</label>
                        <input type="number" id="rating" name="rating" min="1" max="10" value="<?= escape_html($currentRating) ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="review_text">Ulasan:</label>
                        <textarea name="review_text" id="review_text" placeholder="Tulis ulasan Anda di sini..." rows="8" required><?= escape_html($currentReviewText) ?></textarea>
                    </div>
                    <button type="submit" class="btn"><?= $hasUserReviewed ? 'Perbarui Ulasan' : 'Kirim Ulasan' ?></button>
                </form>
            </div>

            <div class="comments-list">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review):
                        // Path lengkap ke foto profil pengulas
                        $reviewerPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($review['user_photo'] ?? 'default.png');
                        // Jika default.png tidak ada di uploads/profile_photos, coba di assets/img
                        if (strpos($reviewerPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
                            $reviewerPhotoUrl = $basePath . '/assets/img/default.png';
                        }
                    ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <img src="<?= $reviewerPhotoUrl ?>" alt="Reviewer Photo" class="commenter-photo-thumb">
                                <p class="comment-author"><strong><?= escape_html($review['username']) ?></strong></p>
                                <p class="comment-date"><?= date('d M Y, H:i', strtotime($review['created_at'])) ?></p>
                                <p class="comment-rating">Rating: <strong><?= escape_html($review['rating']) ?>/10</strong></p>
                            </div>
                            <p class="comment-text"><?= nl2br(escape_html($review['review_text'])) ?></p>
                            <?php
                                $currentUser = Session::get('user');
                                // Only reviewer or admin can delete/edit
                                if (isset($currentUser) && ($currentUser['id'] == $review['user_id'] || $currentUser['is_admin'] == 1)) :
                            ?>
                                <div class="comment-actions">
                                    <?php if ($currentUser['id'] == $review['user_id']) : // Only show edit if it's their own review ?>
                                    <?php endif; ?>
                                    <a href="<?= $basePath ?>/komentar_rating/deleteReview/<?= escape_html($item_type) ?>/<?= escape_html($review['id']) ?>"
                                    onclick="return confirm('Yakin ingin menghapus ulasan ini?')" class="btn-delete-comment">Hapus</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Belum ada ulasan untuk <?= escape_html($item['title']) ?>. Jadilah yang pertama memberikan ulasan!</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<?php
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>