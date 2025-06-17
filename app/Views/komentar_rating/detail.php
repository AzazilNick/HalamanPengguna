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
// Make sure this function is either in a helper, or self-contained.
// For simplicity, defining it here again for this specific view.
function renderAllEntries($entries, $basePath, $item_type, $item_id, $currentUser, $pdo) {
    // Instantiate CommentRating Model here if needed, or pass it from controller
    // For this example, we'll assume it's okay to instantiate here for rendering purposes.
    // In a larger application, you might pass the model or pre-fetch isLiked status.
    $commentRatingModel = new CommentRating($pdo);

    echo '<div class="comments-list">';
    if (empty($entries)) {
        // This message should ideally be outside the recursive function
    } else {
        foreach ($entries as $entry) {
            $commenterPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($entry['commenter_photo'] ?? 'default.png');
            if (strpos($commenterPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
                $commenterPhotoUrl = $basePath . '/assets/img/default.png';
            }
            $isCommentLiked = $commentRatingModel->hasUserLiked($currentUser['id'], $entry['id']);

            echo '<div class="comment-item ' . ($entry['parent_comment_id'] ? 'is-reply' : '') . '" id="comment-' . escape_html($entry['id']) . '">';
            echo '<div class="comment-header">';
            echo '<img src="' . $commenterPhotoUrl . '" alt="Commenter Photo" class="commenter-photo-thumb">';
            echo '<p class="comment-author"><strong>' . escape_html($entry['commenter_username']) . '</strong></p>';
            echo '<p class="comment-date">' . date('d M Y, H:i', strtotime($entry['created_at'])) . '</p>';

            // Display rating if it's a review
            if ($entry['rating_value'] !== null) {
                echo '<p class="comment-rating">Rating: <strong>' . escape_html($entry['rating_value']) . '/10</strong></p>';
            }
            // Display likes for comments (and reviews which can also be liked)
            echo '<p class="comment-likes"><i class="bx bxs-heart"></i> <span id="likes-count-' . escape_html($entry['id']) . '">' . escape_html($entry['total_likes']) . '</span></p>';


            echo '</div>'; // .comment-header
            echo '<p class="comment-text">' . nl2br(escape_html($entry['comment_text'])) . '</p>';

            echo '<div class="comment-actions">';
            // Like/Unlike comment button
            echo '<form class="toggle-comment-like-form" data-comment-id="' . escape_html($entry['id']) . '" action="' . $basePath . '/komentar_rating/detail/' . escape_html($item_type) . '/' . escape_html($item_id) . '" method="POST" style="display:inline-block;">';
            echo '<input type="hidden" name="action" value="toggle_comment_like">';
            echo '<input type="hidden" name="comment_id" value="' . escape_html($entry['id']) . '">';
            echo '<button type="submit" class="btn-like-comment ' . ($isCommentLiked ? 'liked' : '') . '">';
            echo '<i class="bx ' . ($isCommentLiked ? 'bxs-heart' : 'bx-heart') . '"></i>';
            echo '</button>';
            echo '</form>';

            // Reply button (only for pure comments, not reviews)
            if ($entry['rating_value'] === null) {
                echo '<button class="btn-reply" data-comment-id="' . escape_html($entry['id']) . '" data-comment-user="' . escape_html($entry['commenter_username']) . '">Balas</button>';
            }

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
            <h1><?= escape_html($item['title']) ?></h1>
            <?php if (!empty($item['image_url'])): ?>
                <img src="<?= escape_html($item['image_url']) ?>" alt="<?= escape_html($item['title']) ?>" class="<?= $itemType ?>-full-image">
            <?php else: ?>
                <img src="<?= $basePath ?>/assets/img/default_film_series_thumb.png" alt="No Image" class="<?= $itemType ?>-full-image">
            <?php endif; ?>
            <p class="<?= $itemType ?>-meta">Tahun Rilis: <?= escape_html($item['release_year']) ?></p>
            <p class="<?= $itemType ?>-meta">Deskripsi: <?= nl2br(escape_html($item['description'])) ?></p>
            <p class="<?= $itemType ?>-meta">Rating Rata-rata: <strong><span id="average-rating-display"><?= number_format($item['average_rating'], 1) ?></span>/10</strong> (dari <span id="total-comments-ratings-display"><?= escape_html($item['total_comments_ratings']) ?></span> ulasan)</p>
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
            <h2>Komentar & Ulasan</h2>

            <?php
            // Determine initial values for the combined form
            $currentReviewText = $userReviewEntry['comment_text'] ?? '';
            $currentRating = $userReviewEntry['rating_value'] ?? 0;
            $hasUserReviewed = !empty($userReviewEntry);
            ?>

            <div class="comment-form">
                <h3><?= $hasUserReviewed ? 'Edit Ulasan atau Tambah Komentar' : 'Tambahkan Komentar atau Ulasan' ?></h3>
                <form id="comment-review-form" action="<?= $basePath ?>/komentar_rating/addCommentAjax" method="POST">
                    <input type="hidden" name="item_id" value="<?= escape_html($item['id']) ?>">
                    <input type="hidden" name="item_type" value="<?= escape_html($item_type) ?>">
                    <input type="hidden" name="parent_comment_id" id="parent-comment-id" value="">

                    <div class="input-group">
                        <label for="rating_value" id="rating-label">Rating (1-10) (Opsional, kosongkan jika hanya komentar):</label>
                        <div class="star-rating">
                            <?php for ($i = 10; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="rating_value" value="<?= $i ?>" <?= $currentRating == $i ? 'checked' : '' ?> />
                                <label for="star<?= $i ?>" title="<?= $i ?> stars"></label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="comment_text" id="comment-label">Komentar/Ulasan Anda:</label>
                        <textarea name="comment_text" id="comment_text" placeholder="Tulis komentar atau ulasan Anda di sini..." rows="5" required><?= escape_html($currentReviewText) ?></textarea>
                    </div>

                    <button type="submit" class="btn">Kirim</button>
                    <button type="button" id="cancel-reply" class="btn btn-cancel" style="display:none;">Batal Balasan</button>
                </form>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Common helper functions
    function nl2br(str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
    }

    function escapeHTML(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // --- For Komentar & Rating Detail Page (film/series) ---
    const commentReviewForm = document.getElementById('comment-review-form');
    const parentCommentIdInput = document.getElementById('parent-comment-id');
    const commentTextInput = document.getElementById('comment_text');
    const commentLabel = document.getElementById('comment-label');
    const cancelReplyButton = document.getElementById('cancel-reply');
    const ratingValueInputs = document.querySelectorAll('input[name="rating_value"]'); // Changed to select all radio buttons
    const ratingLabel = document.getElementById('rating-label'); // Added for rating label
    const totalCommentsRatingsDisplay = document.getElementById('total-comments-ratings-display');
    const averageRatingDisplay = document.getElementById('average-rating-display');
    const noCommentsReviewsMessage = document.getElementById('no-comments-reviews-message');
    const itemType = commentReviewForm ? commentReviewForm.querySelector('input[name="item_type"]').value : null;
    const itemId = commentReviewForm ? commentReviewForm.querySelector('input[name="item_id"]').value : null;

    const currentUserId = <?= json_encode(Session::get('user')['id'] ?? null) ?>;
    const currentUserIsAdmin = <?= json_encode(Session::get('user')['is_admin'] ?? 0) ?>;

    const initialRating = <?= json_encode($currentRating) ?>; // Store initial rating for reset


    // Function to create a new comment/review HTML element for Komentar & Rating page
    const createCommentReviewElement = (comment, item_type, item_id) => {
        const commentItem = document.createElement('div');
        commentItem.classList.add('comment-item');
        if (comment.parent_comment_id) {
            commentItem.classList.add('is-reply');
        }
        commentItem.id = `comment-${comment.id}`;

        const basePath = BASE_URL;

        const commenterPhotoUrl = `${basePath}/uploads/profile_photos/${comment.commenter_photo || 'default.png'}`;
        const isCommentLiked = false; // For newly added comments, assume not liked yet by anyone, or fetch dynamically

        commentItem.innerHTML = `
            <div class="comment-header">
                <img src="${commenterPhotoUrl}" alt="Commenter Photo" class="commenter-photo-thumb">
                <p class="comment-author"><strong>${escapeHTML(comment.commenter_username)}</strong></p>
                <p class="comment-date">${new Date(comment.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                ${comment.rating_value !== null ? `<p class="comment-rating">Rating: <strong>${escapeHTML(comment.rating_value)}/10</strong></p>` : ''}
                <p class="comment-likes"><i class="bx bxs-heart"></i> <span id="likes-count-${comment.id}">0</span></p>
            </div>
            <p class="comment-text">${nl2br(escapeHTML(comment.comment_text))}</p>
            <div class="comment-actions">
                <form class="toggle-comment-like-form" data-comment-id="${escapeHTML(comment.id)}" action="${basePath}/komentar_rating/detail/${item_type}/${item_id}" method="POST" style="display:inline-block;">
                    <input type="hidden" name="action" value="toggle_comment_like">
                    <input type="hidden" name="comment_id" value="${escapeHTML(comment.id)}">
                    <button type="submit" class="btn-like-comment ${isCommentLiked ? 'liked' : ''}">
                        <i class="bx ${isCommentLiked ? 'bxs-heart' : 'bx-heart'}"></i>
                    </button>
                </form>
                ${comment.rating_value === null ? `<button class="btn-reply" data-comment-id="${escapeHTML(comment.id)}" data-comment-user="${escapeHTML(comment.commenter_username)}">Balas</button>` : ''}
                ${(currentUserId == comment.user_id || currentUserIsAdmin == 1) ?
                    `<a href="${basePath}/komentar_rating/deleteEntry/${escapeHTML(comment.id)}" onclick="return confirm('Yakin ingin menghapus entri ini?')" class="btn-delete-comment">Hapus</a>` : ''
                }
            </div>
        `;
        return commentItem;
    };


    if (commentReviewForm) {
        commentReviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(commentReviewForm);
            const url = commentReviewForm.action;
            const submitButton = commentReviewForm.querySelector('button[type="submit"]');

            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';

            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const result = await response.json();

                if (result.success) {
                    const notificationDiv = document.createElement('div');
                    notificationDiv.classList.add('notification', 'success');
                    notificationDiv.textContent = result.message;
                    commentReviewForm.before(notificationDiv);

                    if (result.comment) {
                        const newCommentElement = createCommentReviewElement(result.comment, itemType, itemId);
                        if (result.comment.parent_comment_id) {
                            const parentCommentItem = document.getElementById(`comment-${result.comment.parent_comment_id}`);
                            if (parentCommentItem) {
                                let repliesContainer = parentCommentItem.querySelector('.comment-replies');
                                if (!repliesContainer) {
                                    repliesContainer = document.createElement('div');
                                    repliesContainer.classList.add('comment-replies');
                                    parentCommentItem.appendChild(repliesContainer);
                                }
                                repliesContainer.appendChild(newCommentElement);
                            }
                        } else {
                             // If it's a new review or update, replace/add at top level
                            if (result.action_type === 'update') {
                                const oldCommentElement = document.getElementById(`comment-${result.comment.id}`);
                                if (oldCommentElement) {
                                    oldCommentElement.replaceWith(newCommentElement);
                                }
                            } else {
                                const commentsList = document.querySelector('.comments-list');
                                if (commentsList) {
                                    commentsList.prepend(newCommentElement);
                                }
                                if (noCommentsReviewsMessage) {
                                    noCommentsReviewsMessage.style.display = 'none';
                                }
                            }
                        }
                    }

                    // Update total comments/ratings and average rating if provided
                    if (result.new_total_comments_ratings !== undefined && totalCommentsRatingsDisplay) {
                        totalCommentsRatingsDisplay.textContent = result.new_total_comments_ratings;
                    }
                    if (result.new_average_rating !== undefined && averageRatingDisplay) {
                        averageRatingDisplay.textContent = parseFloat(result.new_average_rating).toFixed(1);
                    }

                    // Reset form fields
                    commentTextInput.value = '';
                    parentCommentIdInput.value = '';
                    commentLabel.textContent = 'Komentar/Ulasan Anda:';
                    commentTextInput.placeholder = 'Tulis komentar atau ulasan Anda di sini...';
                    cancelReplyButton.style.display = 'none';
                    
                    // Reset star rating
                    ratingValueInputs.forEach(radio => {
                        radio.checked = false;
                    });
                    // Restore original required attribute for rating or clear it if it was a reply
                    ratingLabel.textContent = 'Rating (1-10) (Opsional, kosongkan jika hanya komentar):';


                } else {
                    const notificationDiv = document.createElement('div');
                    notificationDiv.classList.add('notification', 'error');
                    notificationDiv.textContent = result.message;
                    commentReviewForm.before(notificationDiv);
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                const notificationDiv = document.createElement('div');
                notificationDiv.classList.add('notification', 'error');
                notificationDiv.textContent = 'Terjadi kesalahan jaringan atau server.';
                commentReviewForm.before(notificationDiv);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Kirim';
            }
        });

        // Add event listeners for reply buttons
        document.querySelectorAll('.btn-reply').forEach(button => {
            button.addEventListener('click', () => {
                const commentId = button.dataset.commentId;
                const commentUser = button.dataset.commentUser;

                parentCommentIdInput.value = commentId;
                commentLabel.textContent = `Balas Komentar @${commentUser}:`;
                commentTextInput.placeholder = `Tulis balasan untuk @${commentUser} di sini...`;
                commentTextInput.focus();
                cancelReplyButton.style.display = 'inline-block';
                
                // Clear and hide rating for replies
                ratingValueInputs.forEach(radio => {
                    radio.checked = false;
                });
                ratingLabel.textContent = 'Rating (Tidak berlaku untuk balasan):'; // Change label
            });
        });

        // Add event listener for cancel reply button
        cancelReplyButton.addEventListener('click', () => {
            parentCommentIdInput.value = '';
            commentLabel.textContent = 'Komentar/Ulasan Anda:';
            commentTextInput.placeholder = 'Tulis komentar atau ulasan Anda di sini...';
            commentTextInput.value = '';
            cancelReplyButton.style.display = 'none';
            
            // Restore initial rating and label
            ratingLabel.textContent = 'Rating (1-10) (Opsional, kosongkan jika hanya komentar):';
            if (initialRating > 0) {
                document.getElementById(`star${initialRating}`).checked = true;
            } else {
                 ratingValueInputs.forEach(radio => {
                    radio.checked = false;
                });
            }
        });

         // Event listener for comment like forms
        document.querySelectorAll('.toggle-comment-like-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const commentId = form.dataset.commentId;
                const url = form.action;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    // Note: The controller currently redirects for this action, not returns JSON.
                    // If you want full AJAX, you'd modify the controller to return JSON here.
                    // For now, this will simply trigger a full page reload if the controller redirects.
                    // To truly be AJAX, CommentRatingController's toggleLike would need to return JSON.

                    // Since the current PHP `toggleLike` in CommentRatingController does NOT return JSON,
                    // but redirects, the browser will follow the redirect.
                    // To make this fully AJAX, you'd change CommentRatingController's toggleLike
                    // or toggle_comment_like action to return JSON like the addCommentAjax method.

                    // If it was modified to return JSON:
                    // const result = await response.json();
                    // if (result.success) {
                    //     const likeIcon = form.querySelector('i');
                    //     const likesCountSpan = document.getElementById(`likes-count-${commentId}`);
                    //     if (result.is_liked) {
                    //         likeIcon.classList.remove('bx-heart');
                    //         likeIcon.classList.add('bxs-heart');
                    //     } else {
                    //         likeIcon.classList.remove('bxs-heart');
                    //         likeIcon.classList.add('bx-heart');
                    //     }
                    //     if (likesCountSpan) {
                    //         likesCountSpan.textContent = result.total_likes;
                    //     }
                    // } else {
                    //     console.error('Error toggling comment like:', result.message);
                    // }
                    // As is, this script will just let the form submit and cause a reload.
                    // If the intention is to use AJAX here, the backend needs to change.
                } catch (error) {
                    console.error('Network or server error:', error);
                }
            });
        });
    }
});
</script>
