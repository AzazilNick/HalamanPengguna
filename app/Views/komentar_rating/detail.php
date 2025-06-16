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

<script>
// Define BASE_URL globally if not already done in header or another script
const BASE_URL = '<?= $basePath ?>'; // Make sure this is correctly defined

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
    const ratingValueInput = document.getElementById('rating_value');
    const totalCommentsRatingsDisplay = document.getElementById('total-comments-ratings-display');
    const averageRatingDisplay = document.getElementById('average-rating-display');
    const noCommentsReviewsMessage = document.getElementById('no-comments-reviews-message');
    const itemType = commentReviewForm ? commentReviewForm.querySelector('input[name="item_type"]').value : null;
    const itemId = commentReviewForm ? commentReviewForm.querySelector('input[name="item_id"]').value : null;

    const currentUserId = <?= json_encode(Session::get('user')['id'] ?? null) ?>;
    const currentUserIsAdmin = <?= json_encode(Session::get('user')['is_admin'] ?? 0) ?>;


    // Function to create a new comment/review HTML element for Komentar & Rating page
    const createCommentReviewElement = (comment, item_type, item_id) => {
        const commentItem = document.createElement('div');
        commentItem.classList.add('comment-item');
        commentItem.id = comment-${comment.id};

        const commenterPhotoUrl = ${BASE_URL}/uploads/profile_photos/${comment.commenter_photo || 'default.png'};
        // The isCommentLiked status for a newly added comment (especially a reply)
        // should ideally be fetched or determined by the backend if the user creating it likes it by default.
        // For simplicity here, we'll assume new comments are not "liked" yet visually,
        // and a full page reload or a specific AJAX call would update it if the user liked their own comment.
        // For the 'toggle_comment_like_form' we will set it to false.
        const isCommentLiked = false; // Default for new element

        commentItem.innerHTML = `
            <div class="comment-header">
                <img src="${commenterPhotoUrl}" alt="Commenter Photo" class="commenter-photo-thumb">
                <p class="comment-author"><strong>${escapeHTML(comment.commenter_username)}</strong></p>
                ${comment.rating_value !== null ? <p class="comment-rating">Rating: <strong>${escapeHTML(parseFloat(comment.rating_value).toFixed(1))}/10</strong></p> : ''}
                <p class="comment-likes"><i class="bx bxs-heart"></i> <span id="likes-count-${comment.id}">${comment.total_likes !== undefined ? escapeHTML(comment.total_likes) : 0}</span></p>
                <p class="comment-date">${new Date(comment.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
            </div>
            <p class="comment-text">${nl2br(escapeHTML(comment.comment_text))}</p>
            <div class="comment-actions">
                <form class="toggle-comment-like-form" data-comment-id="${escapeHTML(comment.id)}" action="${BASE_URL}/komentar_rating/detail/${item_type}/${item_id}" method="POST">
                    <input type="hidden" name="action" value="toggle_comment_like">
                    <input type="hidden" name="comment_id" value="${escapeHTML(comment.id)}">
                    <button type="submit" class="btn-like-comment">
                        <i class="bx ${isCommentLiked ? 'bxs-heart' : 'bx-heart'}"></i>
                    </button>
                </form>
                ${comment.rating_value === null ? <button class="btn-reply" data-comment-id="${escapeHTML(comment.id)}" data-comment-user="${escapeHTML(comment.commenter_username)}">Balas</button> : ''}
                ${(currentUserId == comment.user_id || currentUserIsAdmin == 1) ?
                    <a href="${BASE_URL}/komentar_rating/deleteEntry/${escapeHTML(comment.id)}" onclick="return confirm('Yakin ingin menghapus entri ini?')" class="btn-delete-comment">Hapus</a> : ''
                }
            </div>
        `;

        // Create nested replies container if it's a top-level comment (for future replies)
        if (!comment.parent_comment_id) {
            const repliesDiv = document.createElement('div');
            repliesDiv.classList.add('comment-replies');
            commentItem.appendChild(repliesDiv);
        }

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
                        'X-Requested-With': 'XMLHttpRequest', // Important for AJAX detection in PHP
                    },
                });

                // Check if the response is JSON or a redirect
                const contentType = response.headers.get('content-type');
                if (response.ok && contentType && contentType.includes('application/json')) {
                    const result = await response.json();

                    if (result.success) {
                        const notificationDiv = document.createElement('div');
                        notificationDiv.classList.add('notification', 'success');
                        notificationDiv.textContent = result.message;
                        commentReviewForm.before(notificationDiv);

                        if (result.comment) {
                            const newCommentElement = createCommentReviewElement(result.comment, itemType, itemId);
                            if (result.comment.parent_comment_id) {
                                // Append to replies of parent comment
                                const parentCommentItem = document.getElementById(comment-${result.comment.parent_comment_id});
                                if (parentCommentItem) {
                                    let repliesContainer = parentCommentItem.querySelector('.comment-replies');
                                    if (!repliesContainer) { // Create if it doesn't exist
                                        repliesContainer = document.createElement('div');
                                        repliesContainer.classList.add('comment-replies');
                                        parentCommentItem.appendChild(repliesContainer);
                                    }
                                    repliesContainer.appendChild(newCommentElement);
                                }
                            } else {
                                // If it's a new review or update, replace/add at top level
                                if (result.action_type === 'update') {
                                    const oldCommentElement = document.getElementById(comment-${result.comment.id});
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
                        ratingValueInput.value = ''; // Clear rating field
                        ratingValueInput.setAttribute('required', 'required'); // Restore required for top-level reviews if needed (adjust based on your form logic for reviews vs comments)
                        // To clear the current rating for a new review/comment, make sure the PHP initializes it to 0 or '' if no user review exists.
                        ratingValueInput.value = ''; // Clear for new input
                    } else {
                        const notificationDiv = document.createElement('div');
                        notificationDiv.classList.add('notification', 'error');
                        notificationDiv.textContent = result.message;
                        commentReviewForm.before(notificationDiv);
                        if (result.redirect) {
                            setTimeout(() => window.location.href = result.redirect, 1500); // Redirect after a short delay
                        }
                    }
                } else {
                    // If response is not JSON (e.g., a redirect from PHP), let the browser handle it.
                    // Or, if it's an error not returning JSON, handle general error.
                    console.warn("Unexpected response from server. Might be a redirect or non-JSON error.");
                    if (!response.ok) {
                         const notificationDiv = document.createElement('div');
                        notificationDiv.classList.add('notification', 'error');
                        notificationDiv.textContent = Error: ${response.status} ${response.statusText}. Please try again.;
                        commentReviewForm.before(notificationDiv);
                    }
                    // For forms that cause full page reloads, typically you wouldn't use preventDefault()
                    // and then try to handle JSON. If the PHP logic redirects, the browser will follow.
                    // This block is mostly for unexpected non-JSON responses.
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

        // Add event listeners for reply buttons (delegation for dynamically added buttons)
        document.querySelector('.comments-list-container').addEventListener('click', (event) => {
            if (event.target.classList.contains('btn-reply')) {
                const button = event.target;
                const commentId = button.dataset.commentId;
                const commentUser = button.dataset.commentUser;

                parentCommentIdInput.value = commentId;
                commentLabel.textContent = Balas Komentar @${commentUser}:;
                commentTextInput.placeholder = Tulis balasan untuk @${commentUser} di sini...;
                commentTextInput.focus();
                cancelReplyButton.style.display = 'inline-block';
                ratingValueInput.value = ''; // Clear rating when replying
                ratingValueInput.removeAttribute('required'); // Make rating optional for replies
                ratingValueInput.style.display = 'none'; // Hide rating input for replies
                commentReviewForm.querySelector('label[for="rating_value"]').style.display = 'none'; // Hide rating label
            }
        });


        // Add event listener for cancel reply button
        cancelReplyButton.addEventListener('click', () => {
            parentCommentIdInput.value = '';
            commentLabel.textContent = 'Komentar/Ulasan Anda:';
            commentTextInput.placeholder = 'Tulis komentar atau ulasan Anda di sini...';
            commentTextInput.value = '';
            cancelReplyButton.style.display = 'none';
            ratingValueInput.setAttribute('required', 'required'); // Make rating required again for top-level
            ratingValueInput.style.display = 'block'; // Show rating input
            commentReviewForm.querySelector('label[for="rating_value"]').style.display = 'block'; // Show rating label

            // Restore initial rating value if it was an edit form (php variable)
            const initialRating = '<?= escape_html($currentRating) ?>';
            if (initialRating !== '0' && initialRating !== '') {
                 ratingValueInput.value = initialRating;
            } else {
                 ratingValueInput.value = ''; // Ensure it's truly empty if no initial rating
            }
        });

        // Event listener for comment like forms (delegation for dynamically added buttons)
        document.querySelector('.comments-list-container').addEventListener('submit', async (event) => {
            if (event.target.classList.contains('toggle-comment-like-form')) {
                event.preventDefault();
                const form = event.target;
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

                    // For the like/unlike action, the controller might still be doing a redirect.
                    // To truly make this AJAX, your PHP controller's toggle_comment_like action
                    // should return JSON, e.g., { success: true, total_likes: 10, is_liked: true }.
                    // If it redirects, this fetch will just follow the redirect and cause a page reload.
                    // Assuming for a moment it does return JSON for dynamic update:
                    const result = await response.json(); // This line would throw error if it's a redirect

                    if (result.success) {
                        const likeIcon = form.querySelector('i');
                        const likesCountSpan = document.getElementById(likes-count-${commentId});

                        if (result.is_liked) {
                            likeIcon.classList.remove('bx-heart');
                            likeIcon.classList.add('bxs-heart');
                        } else {
                            likeIcon.classList.remove('bxs-heart');
                            likeIcon.classList.add('bx-heart');
                        }
                        if (likesCountSpan) {
                            likesCountSpan.textContent = result.total_likes;
                        }
                    } else {
                        console.error('Error toggling comment like:', result.message);
                        alert('Gagal mengubah status like: ' + result.message);
                    }
                } catch (error) {
                    console.error('Network or server error toggling like:', error);
                    alert('Terjadi kesalahan saat mengubah status like. Silakan coba lagi.');
                    // Optionally, trigger a full page reload if AJAX fails completely,
                    // or revert UI changes.
                }
            }
        });
    }
});
</script>