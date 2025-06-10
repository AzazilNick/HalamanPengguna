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
            <p class="<?= $itemType ?>-meta">Rating Rata-rata: <strong><?= number_format($item['average_rating'], 1) ?>/10</strong> (dari <?= escape_html($item['total_comments_ratings']) ?> rating)</p>
            <p class="<?= $itemType ?>-meta">Total Suka: <strong><?= escape_html($item['total_likes']) ?></strong></p>

            <div class="item-actions-bottom">
                <form action="<?= $basePath ?>/komentar_rating/detail/<?= escape_html($item_type) ?>/<?= escape_html($item['id']) ?>" method="POST" style="display:inline-block;">
                    <input type="hidden" name="action" value="toggle_like">
                    <button type="submit" class="btn btn-like">
                        <i class='bx <?= $isLiked ? 'bxs-heart' : 'bx-heart' ?>'></i> <?= $isLiked ? 'Disukai' : 'Suka' ?>
                    </button>
                </form>
            </div>
        </article>

        <section class="comments-section">
            <h2>Ulasan Pengguna (<?= escape_html($item['total_comments_ratings']) ?>)</h2>

            <?php
            // Determine initial values for the form
            $currentReviewText = $userRating['comment_text'] ?? '';
            $currentRating = $userRating['rating_value'] ?? 0;
            $hasUserReviewed = !empty($userRating);
            ?>

            <div class="comment-form">
                <h3><?= $hasUserReviewed ? 'Edit Ulasan Anda' : 'Tambahkan Ulasan Anda' ?></h3>
                <form action="<?= $basePath ?>/komentar_rating/detail/<?= escape_html($item_type) ?>/<?= escape_html($item['id']) ?>" method="POST">
                    <input type="hidden" name="action" value="submit_review_rating">
                    <div class="input-group">
                        <label for="rating">Rating (1-10):</label>
                        <input type="number" id="rating" name="rating" min="1" max="10" value="<?= escape_html($currentRating) ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="review_text">Ulasan:</label>
                        <textarea name="review_text" id="review_text" placeholder="Tulis ulasan Anda di sini..." rows="8" required><?= escape_html($currentReviewText) ?></textarea>
                    </div>
                    <button type="submit" class="btn">Perbarui Ulasan</button>
                </form>
            </div>

            <div class="comments-list">
                <?php if (!empty($ratings)): ?>
                    <?php foreach ($ratings as $review):
                        $reviewerPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($review['reviewer_photo'] ?? 'default.png');
                        if (strpos($reviewerPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
                            $reviewerPhotoUrl = $basePath . '/assets/img/default.png';
                        }
                    ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <img src="<?= $reviewerPhotoUrl ?>" alt="Reviewer Photo" class="commenter-photo-thumb">
                                <p class="comment-author"><strong><?= escape_html($review['reviewer_username']) ?></strong></p>
                                <p class="comment-date"><?= date('d M Y, H:i', strtotime($review['created_at'])) ?></p>
                                <p class="comment-rating">Rating: <strong><?= escape_html($review['rating_value']) ?>/10</strong></p>
                            </div>
                            <p class="comment-text"><?= nl2br(escape_html($review['comment_text'])) ?></p>
                            <?php
                                $currentUser = Session::get('user');
                                if (isset($currentUser) && ($currentUser['id'] == $review['user_id'] || $currentUser['is_admin'] == 1)) :
                            ?>
                                <div class="comment-actions">
                                    <a href="<?= $basePath ?>/komentar_rating/deleteRating/<?= escape_html($item_type) ?>/<?= escape_html($review['id']) ?>"
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

        <section class="comments-section" style="margin-top: 30px;">
            <h2>Komentar Umum (<?= escape_html($item['total_comments_ratings']) ?>)</h2>

            <div class="comment-form">
                <h3>Tambahkan Komentar</h3>
                <form id="comment-form-general" action="<?= $basePath ?>/komentar_rating/detail/<?= escape_html($item_type) ?>/<?= escape_html($item['id']) ?>" method="POST">
                    <input type="hidden" name="action" value="submit_comment_general">
                    <input type="hidden" name="parent_comment_id" id="parent-comment-id-general" value="">
                    <label for="comment_text_general" id="comment-label-general">Komentar Anda:</label>
                    <textarea name="comment_text" id="comment_text_general" placeholder="Tulis komentar Anda di sini..." rows="5" required></textarea>
                    <button type="submit" class="btn">Kirim Komentar</button>
                    <button type="button" id="cancel-reply-general" class="btn btn-cancel" style="display:none;">Batal Balasan</button>
                </form>
            </div>

            <?php
            // The renderComments function itself needs to be defined once, perhaps in Core/Functions.php
            // For now, we keep it here for simplicity, but acknowledge it's not ideal.
            // Ensure the CommentRating model is available for the renderComments function.
            // No need to instantiate again here, as it's passed from the controller via $pdo.
            require_once APP_ROOT . '/app/Models/CommentRating.php';
            $commentRatingModelForView = new CommentRating($pdo); // Use the passed $pdo connection

            function renderComments($comments, $basePath, $item_type, $item_id, $currentUser, $commentRatingModel) {
                echo '<div class="comments-list">';
                if (empty($comments)) {
                    echo '<p>Belum ada komentar untuk item ini.</p>';
                } else {
                    foreach ($comments as $comment) {
                        $commenterPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($comment['commenter_photo'] ?? 'default.png');
                        if (strpos($commenterPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
                            $commenterPhotoUrl = $basePath . '/assets/img/default.png';
                        }
                        $isCommentLiked = $commentRatingModel->hasUserLiked($currentUser['id'], $comment['id']);

                        echo '<div class="comment-item">';
                        echo '<div class="comment-header">';
                        echo '<img src="' . $commenterPhotoUrl . '" alt="Commenter Photo" class="commenter-photo-thumb">';
                        echo '<p class="comment-author"><strong>' . escape_html($comment['commenter_username']) . '</strong></p>';
                        echo '<p class="comment-date">' . date('d M Y, H:i', strtotime($comment['created_at'])) . '</p>';
                        echo '<p class="comment-likes"><i class="bx bxs-heart"></i> ' . escape_html($comment['total_likes']) . '</p>';
                        echo '</div>'; // .comment-header
                        echo '<p class="comment-text">' . nl2br(escape_html($comment['comment_text'])) . '</p>';

                        echo '<div class="comment-actions">';
                        echo '<form action="' . $basePath . '/komentar_rating/detail/' . escape_html($item_type) . '/' . escape_html($item_id) . '" method="POST" style="display:inline-block;">';
                        echo '<input type="hidden" name="action" value="toggle_comment_like">';
                        echo '<input type="hidden" name="comment_id" value="' . escape_html($comment['id']) . '">';
                        echo '<button type="submit" class="btn-like-comment ' . ($isCommentLiked ? 'liked' : '') . '">';
                        echo '<i class="bx ' . ($isCommentLiked ? 'bxs-heart' : 'bx-heart') . '"></i>';
                        echo '</button>';
                        echo '</form>';

                        echo '<button class="btn-reply" data-comment-id="' . escape_html($comment['id']) . '" data-comment-user="' . escape_html($comment['commenter_username']) . '">Balas</button>';

                        if (isset($currentUser) && ($currentUser['id'] == $comment['user_id'] || $currentUser['is_admin'] == 1)) {
                            echo '<a href="' . $basePath . '/komentar_rating/deleteComment/' . escape_html($comment['id']) . '" onclick="return confirm(\'Yakin ingin menghapus komentar ini?\')" class="btn-delete-comment">Hapus</a>';
                        }
                        echo '</div>'; // .comment-actions

                        if (!empty($comment['replies'])) {
                            echo '<div class="comment-replies">';
                            renderComments($comment['replies'], $basePath, $item_type, $item_id, $currentUser, $commentRatingModel);
                            echo '</div>'; // .comment-replies
                        }

                        echo '</div>'; // .comment-item
                    }
                }
                echo '</div>'; // .comments-list
            }

            // This line caused the error. $generalComments is already passed from the controller.
            // REMOVED: $generalComments = $this->commentRatingModel->getCommentsByItem($itemId, $itemType, true);
            renderComments($generalComments, $basePath, $itemType, $item['id'], Session::get('user'), $commentRatingModelForView);
            ?>
        </section>
    </div>
</main>

<?php
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>