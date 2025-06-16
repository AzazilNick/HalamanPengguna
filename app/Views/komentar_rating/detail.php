<?php
// niflix_project/app/Views/komentar_rating/detail.php

require_once APP_ROOT . '/app/Views/includes/header.php';
// require_once APP_ROOT . '/app/Models/CommentRating.php'; // Not needed here if renderAllEntries is self-contained or uses passed $pdo

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

// Function to render comments and reviews recursively
function renderAllEntries($entries, $basePath, $item_type, $item_id, $currentUser, $pdo) {
    // Instantiate CommentRating Model here if needed, or pass it from controller
    // For this example, we'll assume it's okay to instantiate here for rendering purposes.
    // In a larger application, you might pass the model or pre-fetch isLiked status.
    $commentRatingModel = new CommentRating($pdo);

    echo '<div class="comments-list">';
    if (empty($entries)) {
        // This message is now handled by #no-comments-reviews-message outside this function
    } else {
        foreach ($entries as $entry) {
            $commenterPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($entry['commenter_photo'] ?? 'default.png');
            // Fallback for default.png if it's not in uploads/profile_photos
            if (strpos($commenterPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
                $commenterPhotoUrl = $basePath . '/assets/img/default.png'; // Assuming you have a fallback default image in assets/img
            }
            $isCommentLiked = $currentUser ? $commentRatingModel->hasUserLiked($currentUser['id'], $entry['id']) : false;

            echo '<div class="comment-item" id="comment-' . escape_html($entry['id']) . '">';
            echo '<div class="comment-header">';
            echo '<img src="' . $commenterPhotoUrl . '" alt="Commenter Photo" class="commenter-photo-thumb">';
            echo '<p class="comment-author"><strong>' . escape_html($entry['commenter_username']) . '</strong></p>';
            
            // Display rating if it's a review
            if ($entry['rating_value'] !== null) {
                echo '<p class="comment-rating">Rating: <strong>' . escape_html($entry['rating_value']) . '/10</strong></p>';
            }
            // Display likes for comments (and reviews which can also be liked) - positioned to right in header
            echo '<p class="comment-likes"><i class="bx bxs-heart"></i> <span id="likes-count-' . escape_html($entry['id']) . '">' . escape_html($entry['total_likes']) . '</span></p>';
            echo '<p class="comment-date">' . date('d M Y, H:i', strtotime($entry['created_at'])) . '</p>';

            echo '</div>'; // .comment-header
            echo '<p class="comment-text">' . nl2br(escape_html($entry['comment_text'])) . '</p>';

            echo '<div class="comment-actions">';
            // Like/Unlike comment button
            echo '<form class="toggle-comment-like-form" data-comment-id="' . escape_html($entry['id']) . '" action="' . $basePath . '/komentar_rating/detail/' . escape_html($item_type) . '/' . escape_html($item_id) . '" method="POST">';
            echo '<input type="hidden" name="action" value="toggle_comment_like">';
            echo '<input type="hidden" name="comment_id" value="' . escape_html($entry['id']) . '">';
            echo '<button type="submit" class="btn-like-comment">';
            echo '<i class="bx ' . ($isCommentLiked ? 'bxs-heart' : 'bx-heart') . '"></i>';
            echo '</button>';
            echo '</form>';

            // Reply button (only for pure comments, not reviews, or if you want reviews to be replyable)
            // Adjust condition as needed: $entry['rating_value'] === null
            echo '<button class="btn-reply" data-comment-id="' . escape_html($entry['id']) . '" data-comment-user="' . escape_html($entry['commenter_username']) . '">Balas</button>';
            

            // Delete entry button (for author or admin)
            if (isset($currentUser) && ($currentUser['id'] == $entry['user_id'] || $currentUser['is_admin'] == 1)) {
                echo '<a href="' . $basePath . '/komentar_rating/deleteEntry/' . escape_html($entry['id']) . '" onclick="return confirm(\'Yakin ingin menghapus entri ini?\')" class="btn-delete-comment">Hapus</a>';
            }
            echo '</div>'; // .comment-actions

            // Render replies recursively
            if (!empty($entry['replies'])) {
                echo '<div class="comment-replies">';
                renderAllEntries($entry['replies'], $basePath, $item_type, $item_id, $currentUser, $pdo);
                echo '</div>'; // .comment-replies
            }

            echo '</div>'; // .comment-item
        }
    }
    echo '</div>'; // .comments-list
}

?>
<main>
    <div class="article-detail-container">
        <a href="<?= $basePath ?>/komentar_rating" class="btn btn-back">← Kembali ke Daftar Komentar & Rating</a>

        <?php if (isset($message) && $message): ?>
            <div class="notification <?= escape_html($message_type ?? '') ?>"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <article class="single-article">
            <div class="item-image-container">
                <?php if (!empty($item['image_url'])): ?>
                    <img src="<?= escape_html($item['image_url']) ?>" alt="<?= escape_html($item['title']) ?>" class="item-image">
                <?php endif; ?>
            </div>
            <div class="item-details-content">
                <h1><?= escape_html($item['title']) ?></h1>
                <p class="item-meta">Tahun Rilis: <strong><?= escape_html($item['release_year']) ?></strong></p>
                <p class="item-description">Deskripsi: <?= nl2br(escape_html($item['description'])) ?></p>
                <p class="item-meta">Rating Rata-rata: <strong><span id="average-rating-display"><?= number_format($item['average_rating'], 1) ?></span>/10</strong> (dari <span id="total-comments-ratings-display"><?= escape_html($item['total_comments_ratings']) ?></span> ulasan)</p>
                <p class="item-meta">Total Suka Item: <strong><?= escape_html($item['total_likes']) ?></strong></p>

                <div class="item-actions-bottom">
                    <form action="<?= $basePath ?>/komentar_rating/detail/<?= escape_html($item_type) ?>/<?= escape_html($item['id']) ?>" method="POST">
                        <input type="hidden" name="action" value="toggle_like">
                        <button type="submit" class="btn btn-like">
                            <i class='bx <?= $isLiked ? 'bxs-heart' : 'bx-heart' ?>'></i> <?= $isLiked ? 'Disukai' : 'Suka' ?>
                        </button>
                    </form>
                </div>
            </div>
        </article>

        <section class="comments-section">
            <h2>Komentar & Ulasan</h2>

            <?php
            // Determine initial values for the combined form
            $currentReviewText = $userReviewEntry['comment_text'] ?? '';
            $currentRating = $userReviewEntry['rating_value'] ?? 0;
            $hasUserReviewed = !empty($userReviewEntry);
            ?>

            <div class="comment-form">
                <h3><?= $hasUserReviewed ? 'Edit Ulasan atau Tambah Komentar' : 'Tambahkan Komentar atau Ulasan' ?></h3>
                <?php if (Session::get('user')): ?>
                <form id="comment-review-form" action="<?= $basePath ?>/komentar_rating/addCommentAjax" method="POST">
                    <input type="hidden" name="item_id" value="<?= escape_html($item['id']) ?>">
                    <input type="hidden" name="item_type" value="<?= escape_html($item_type) ?>">
                    <input type="hidden" name="parent_comment_id" id="parent-comment-id" value="">

                    <div class="input-group">
                        <label for="rating_value">Rating (1-10) (Opsional, kosongkan jika hanya komentar):</label>
                        <input type="number" id="rating_value" name="rating_value" min="1" max="10" value="<?= escape_html($currentRating) ?>">
                    </div>

                    <div class="input-group">
                        <label for="comment_text" id="comment-label">Komentar/Ulasan Anda:</label>
                        <textarea name="comment_text" id="comment_text" placeholder="Tulis komentar atau ulasan Anda di sini..." rows="5" required><?= escape_html($currentReviewText) ?></textarea>
                    </div>

                    <button type="submit" class="btn">Kirim</button>
                    <button type="button" id="cancel-reply" class="btn btn-cancel" style="display:none;">Batal Balasan</button>
                </form>
                <?php else: ?>
                    <p class="login-prompt">Anda harus <a href="<?= $basePath ?>/auth/login?message=<?= urlencode('Anda harus login untuk berkomentar atau memberi rating.') ?>&type=info">login</a> untuk berkomentar atau memberi rating.</p>
                <?php endif; ?>
            </div>
            <div class="comments-list-container">
                <?php
                renderAllEntries($allEntries, $basePath, $item_type, $item['id'], Session::get('user'), $pdo);
                ?>
                <?php if (empty($allEntries)): ?>
                    <p id="no-comments-reviews-message">Belum ada komentar atau ulasan untuk item ini.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<?php
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>
