<?php
// niflix_project/app/Views/komentar_rating/detail.php

require_once APP_ROOT . '/app/Views/includes/header.php';
require_once APP_ROOT . '/app/Models/CommentRating.php'; // Needed for the rendering function

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

// Function to render comments recursively
// CHANGE PARAMETER NAMES TO $item_type AND $item_id (snake_case)
function renderComments($comments, $basePath, $item_type, $item_id, $currentUser, $pdo) { // <--- CHANGED
    // INSTANTIATE THE MODEL INSIDE THE FUNCTION, PASSING $pdo
    $commentRatingModel = new CommentRating($pdo);
    echo '<div class="comments-list">';
    if (empty($comments)) {
        echo '<p>Belum ada komentar untuk item ini.</p>';
    } else {
        foreach ($comments as $comment) {
            $commenterPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($comment['commenter_photo'] ?? 'default.png');
            if (strpos($commenterPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
                $commenterPhotoUrl = $basePath . '/assets/img/default.png';
            }
            // Use the instantiated model here:
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
            // Like/Unlike comment button
            // USE $item_type AND $item_id HERE:
            echo '<form action="' . $basePath . '/komentar_rating/detail/' . escape_html($item_type) . '/' . escape_html($item_id) . '" method="POST" style="display:inline-block;">'; // <--- CHANGED
            echo '<input type="hidden" name="action" value="toggle_comment_like">';
            echo '<input type="hidden" name="comment_id" value="' . escape_html($comment['id']) . '">';
            echo '<button type="submit" class="btn-like-comment ' . ($isCommentLiked ? 'liked' : '') . '">';
            echo '<i class="bx ' . ($isCommentLiked ? 'bxs-heart' : 'bx-heart') . '"></i>';
            echo '</button>';
            echo '</form>';

            // Reply button
            echo '<button class="btn-reply" data-comment-id="' . escape_html($comment['id']) . '" data-comment-user="' . escape_html($comment['commenter_username']) . '">Balas</button>';

            // Delete comment button (only for author or admin)
            if (isset($currentUser) && ($currentUser['id'] == $comment['user_id'] || $currentUser['is_admin'] == 1)) {
                echo '<a href="' . $basePath . '/komentar_rating/deleteComment/' . escape_html($comment['id']) . '" onclick="return confirm(\'Yakin ingin menghapus komentar ini?\')" class="btn-delete-comment">Hapus</a>';
            }
            echo '</div>'; // .comment-actions

            // Render replies recursively
            if (!empty($comment['replies'])) {
                echo '<div class="comment-replies">';
                // Pass $item_type and $item_id to the recursive call:
                renderComments($comment['replies'], $basePath, $item_type, $item_id, $currentUser, $pdo); // <--- CHANGED
                echo '</div>'; // .comment-replies
            }

            echo '</div>'; // .comment-item
        }
    }
    echo '</div>'; // .comments-list
}

// REMOVE THIS LINE:
// $commentRatingModelForView = new CommentRating($pdo); // Assuming $pdo is available from the controller
// This line caused the error because $pdo wasn't in scope yet.

?>
<main>
    <div class="article-detail-container">
        <section class="comments-section" style="margin-top: 30px;">
            <h2>Komentar (<?= escape_html($item['total_comments']) ?>)</h2>

            <div class="comment-form">
                <h3>Tambahkan Komentar</h3>
                <form id="comment-form" action="<?= $basePath ?>/komentar_rating/detail/<?= escape_html($item_type) ?>/<?= escape_html($item['id']) ?>" method="POST">
                    <input type="hidden" name="action" value="submit_comment">
                    <input type="hidden" name="parent_comment_id" id="parent-comment-id" value="">
                    <label for="comment_text" id="comment-label">Komentar Anda:</label>
                    <textarea name="comment_text" id="comment_text" placeholder="Tulis komentar Anda di sini..." rows="5" required></textarea>
                    <button type="submit" class="btn">Kirim Komentar</button>
                    <button type="button" id="cancel-reply" class="btn btn-cancel" style="display:none;">Batal Balasan</button>
                </form>
            </div>

            <?php
            // Call renderComments and pass $item_type and $item_id (snake_case)
            renderComments($comments, $basePath, $item_type, $item_id, Session::get('user'), $pdo); // <--- CHANGED
            ?>
        </section>
    </div>
</main>