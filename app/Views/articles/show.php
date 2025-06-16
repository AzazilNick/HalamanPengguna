<?php
// niflix_project/app/Views/articles/show.php
// $article, $comments, $title, $message, $message_type akan tersedia dari ArticleController

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php';

// Pastikan base Path tersedia untuk tautan dan gambar
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}

// Path lengkap ke foto profil penulis
$authorPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($article['author_photo'] ?? 'default.png');
// Jika default.png tidak ada di uploads/profile_photos, coba di assets/img
if (strpos($authorPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
    $authorPhotoUrl = $basePath . '/assets/img/default.png';
}

// Function to render comments and replies recursively
function renderArticleComments($entries, $basePath, $article_id, $currentUser, $pdo) {
    echo '<div class="comments-list">';
    if (empty($entries)) {
        // Only show this message if it's the initial call (not a recursive reply call)
        // A simple check like this might suffice, or pass a flag.
        // For now, let's keep it simple.
    } else {
        foreach ($entries as $entry) {
            // Check if commenter photo exists or use default
            $commenterPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($entry['commenter_photo'] ?? 'default.png');
            if (strpos($commenterPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
                $commenterPhotoUrl = $basePath . '/assets/img/default.png';
            }
            $isCommentLiked = false; // Not implemented for article comments yet, but kept for consistency

            echo '<div class="comment-item" id="comment-' . escape_html($entry['id']) . '">';
            echo '<div class="comment-header">';
            echo '<img src="' . $commenterPhotoUrl . '" alt="Commenter Photo" class="commenter-photo-thumb">';
            echo '<p class="comment-author"><strong>' . escape_html($entry['commenter_username']) . '</strong></p>';
            echo '<p class="comment-date">' . date('d M Y, H:i', strtotime($entry['created_at'])) . '</p>';

            echo '</div>'; // .comment-header
            echo '<p class="comment-text">' . nl2br(escape_html($entry['comment_text'])) . '</p>';

            echo '<div class="comment-actions">';
            // Like/Unlike comment button - currently not implemented for article comments, but can be added
            // if you expand CommentRating model's toggleLike to handle item_type 'article'
            /*
            echo '<form action="' . $basePath . '/comment/toggleLikeAjax" method="POST" style="display:inline-block;">';
            echo '<input type="hidden" name="comment_id" value="' . escape_html($entry['id']) . '">';
            echo '<input type="hidden" name="item_type" value="article">';
            echo '<button type="submit" class="btn-like-comment ' . ($isCommentLiked ? 'liked' : '') . '">';
            echo '<i class="bx ' . ($isCommentLiked ? 'bxs-heart' : 'bx-heart') . '"></i>';
            echo '</button>';
            echo '</form>';
            */

            // Reply button
            echo '<button class="btn-reply" data-comment-id="' . escape_html($entry['id']) . '" data-comment-user="' . escape_html($entry['commenter_username']) . '">Balas</button>';


            // Delete entry button (for author or admin or article author)
            if (isset($currentUser) && ($currentUser['id'] == $entry['user_id'] || $currentUser['id'] == $article['user_id'] || $currentUser['is_admin'] == 1)) {
                echo '<a href="' . $basePath . '/comment/delete/' . escape_html($entry['id']) . '" onclick="return confirm(\'Yakin ingin menghapus komentar ini?\')" class="btn-delete-comment">Hapus</a>';
            }
            echo '</div>'; // .comment-actions

            // Render replies recursively
            if (!empty($entry['replies'])) {
                echo '<div class="comment-replies">';
                renderArticleComments($entry['replies'], $basePath, $article_id, $currentUser, $pdo);
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
        <a href="<?= $basePath ?>/articles" class="btn btn-back">← Kembali ke Daftar Artikel</a>

        <?php if (isset($message) && $message): ?>
            <div class="notification <?= escape_html($message_type ?? '') ?>"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <?php if ($article): ?>
            <article class="single-article">
                <h1><?= escape_html($article['title']) ?></h1>
                <p class="article-meta">
                    Oleh: <img src="<?= $authorPhotoUrl ?>" alt="Author Photo" class="author-photo-thumb">
                    <strong><?= escape_html($article['author_fullname'] ?: $article['author_username']) ?></strong>
                    pada <?= date('d F Y H:i', strtotime($article['created_at'])) ?>
                    <?php if ($article['created_at'] != $article['updated_at']): ?>
                        (Terakhir diperbarui: <?= date('d F Y H:i', strtotime($article['updated_at'])) ?>)
                    <?php endif; ?>
                </p>
                <div class="article-content">
                    <?= nl2br(escape_html($article['content'])) ?>
                </div>

                <?php
                    $currentUser = Session::get('user');
                    if (isset($currentUser) && ($currentUser['id'] == $article['user_id'] || $currentUser['is_admin'] == 1)) :
                ?>
                    <div class="article-actions-right">
                        <a href="<?= $basePath ?>/articles/edit/<?= escape_html($article['id']) ?>" class="btn-edit-global">Edit</a>
                        <a href="<?= $basePath ?>/articles/delete/<?= escape_html($article['id']) ?>"
                        onclick="return confirm('Yakin ingin menghapus artikel ini? Semua komentar juga akan terhapus.')" class="btn-delete">Hapus</a>
                    </div>
                <?php endif; ?>
            </article>

            <section class="comments-section">
                <h2>Komentar (<span id="comment-count"><?= count($comments) ?></span>)</h2>

                <?php if (Session::has('user')): ?>
                    <div class="comment-form">
                        <h3>Tambahkan Komentar</h3>
                        <form id="comment-form-ajax" action="<?= $basePath ?>/comment/addCommentAjax" method="POST">
                            <input type="hidden" name="item_id" value="<?= escape_html($article['id']) ?>">
                            <input type="hidden" name="item_type" value="article">
                            <input type="hidden" name="parent_comment_id" id="parent-comment-id-article" value="">

                            <label for="comment_text" id="comment-label-article">Tulis komentar Anda di sini:</label>
                            <textarea name="comment_text" id="comment_text_article" placeholder="Tulis komentar Anda di sini..." rows="5" required></textarea>
                            <button type="submit" class="btn">Kirim Komentar</button>
                            <button type="button" id="cancel-reply-article" class="btn btn-cancel" style="display:none;">Batal Balasan</button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="comments-list-container">
                    <?php
                    // Pastikan $pdo diteruskan ke fungsi rekursif
                    renderArticleComments($comments, $basePath, $article['id'], Session::get('user'), $pdo);
                    ?>
                </div>
                <?php if (empty($comments)): ?>
                    <p id="no-comments-message">Belum ada komentar untuk artikel ini.</p>
                <?php endif; ?>
            </section>

        <?php else: ?>
            <p>Artikel tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</main>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const commentFormAjax = document.getElementById('comment-form-ajax');
    const commentTextInputArticle = document.getElementById('comment_text_article');
    const parentCommentIdInputArticle = document.getElementById('parent-comment-id-article');
    const commentLabelArticle = document.getElementById('comment-label-article');
    const cancelReplyArticleButton = document.getElementById('cancel-reply-article');
    const commentsListContainer = document.querySelector('.comments-list-container');
    const noCommentsMessage = document.getElementById('no-comments-message');
    const commentCountSpan = document.getElementById('comment-count');
    const articleId = commentFormAjax.querySelector('input[name="item_id"]').value;
    const articleAuthorId = <?= json_encode($article['user_id']) ?>; // Get article author ID
    const currentUserId = <?= json_encode(Session::get('user')['id'] ?? null) ?>;
    const currentUserIsAdmin = <?= json_encode(Session::get('user')['is_admin'] ?? 0) ?>;


    // Function to create a new comment HTML element
    const createCommentElement = (comment) => {
        const commentItem = document.createElement('div');
        commentItem.classList.add('comment-item');
        commentItem.id = `comment-${comment.id}`;

        const basePath = BASE_URL; // Assume BASE_URL is defined globally from header.php

        const commenterPhotoUrl = `${basePath}/uploads/profile_photos/${comment.commenter_photo || 'default.png'}`;

        commentItem.innerHTML = `
            <div class="comment-header">
                <img src="${commenterPhotoUrl}" alt="Commenter Photo" class="commenter-photo-thumb">
                <p class="comment-author"><strong>${escapeHTML(comment.commenter_username)}</strong></p>
                <p class="comment-date">${new Date(comment.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
            </div>
            <p class="comment-text">${nl2br(escapeHTML(comment.comment_text))}</p>
            <div class="comment-actions">
                <button class="btn-reply" data-comment-id="${escapeHTML(comment.id)}" data-comment-user="${escapeHTML(comment.commenter_username)}">Balas</button>
                ${(currentUserId == comment.user_id || currentUserId == articleAuthorId || currentUserIsAdmin == 1) ?
                    `<a href="${basePath}/comment/delete/${escapeHTML(comment.id)}" onclick="return confirm('Yakin ingin menghapus komentar ini?')" class="btn-delete-comment">Hapus</a>` : ''
                }
            </div>
        `;

        // Add event listener for reply button on the newly created comment
        commentItem.querySelector('.btn-reply').addEventListener('click', (e) => {
            const replyButton = e.target;
            const commentId = replyButton.dataset.commentId;
            const commentUser = replyButton.dataset.commentUser;

            parentCommentIdInputArticle.value = commentId;
            commentLabelArticle.textContent = `Balas Komentar @${commentUser}:`;
            commentTextInputArticle.placeholder = `Tulis balasan untuk @${commentUser} di sini...`;
            commentTextInputArticle.focus();
            cancelReplyArticleButton.style.display = 'inline-block';
        });

        return commentItem;
    };

    // Helper function for nl2br (from PHP's nl2br)
    function nl2br(str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
    }

    // Helper function for escapeHTML (from PHP's escape_html)
    function escapeHTML(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }


    if (commentFormAjax) {
        commentFormAjax.addEventListener('submit', async (e) => {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(commentFormAjax);
            const url = commentFormAjax.action;
            const submitButton = commentFormAjax.querySelector('button[type="submit"]');

            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';

            // Clear any existing notification
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Mark as AJAX request
                    },
                });

                const result = await response.json();

                if (result.success) {
                    const notificationDiv = document.createElement('div');
                    notificationDiv.classList.add('notification', 'success');
                    notificationDiv.textContent = result.message;
                    commentFormAjax.before(notificationDiv); // Display notification above the form

                    // Add new comment to the UI
                    if (result.comment) {
                        const newCommentElement = createCommentElement(result.comment);
                        if (result.comment.parent_comment_id) {
                            // Append as a reply
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
                            // Append as a top-level comment
                            commentsListContainer.prepend(newCommentElement); // Add to the top
                            if (noCommentsMessage) {
                                noCommentsMessage.style.display = 'none'; // Hide "No comments" message
                            }
                        }
                         // Update comment count
                        if (commentCountSpan) {
                            let currentCount = parseInt(commentCountSpan.textContent);
                            commentCountSpan.textContent = currentCount + 1;
                        }
                    }

                    // Reset form fields
                    commentTextInputArticle.value = '';
                    parentCommentIdInputArticle.value = '';
                    commentLabelArticle.textContent = 'Tulis komentar Anda di sini:';
                    commentTextInputArticle.placeholder = 'Tulis komentar Anda di sini...';
                    cancelReplyArticleButton.style.display = 'none';

                } else {
                    const notificationDiv = document.createElement('div');
                    notificationDiv.classList.add('notification', 'error');
                    notificationDiv.textContent = result.message;
                    commentFormAjax.before(notificationDiv); // Display notification above the form
                    // If user not logged in, redirect them
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                const notificationDiv = document.createElement('div');
                notificationDiv.classList.add('notification', 'error');
                notificationDiv.textContent = 'Terjadi kesalahan jaringan atau server.';
                commentFormAjax.before(notificationDiv);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Kirim Komentar';
            }
        });

        // Event listener for reply buttons
        document.querySelectorAll('.btn-reply').forEach(button => {
            button.addEventListener('click', (e) => {
                const replyButton = e.target;
                const commentId = replyButton.dataset.commentId;
                const commentUser = replyButton.dataset.commentUser;

                parentCommentIdInputArticle.value = commentId;
                commentLabelArticle.textContent = `Balas Komentar @${commentUser}:`;
                commentTextInputArticle.placeholder = `Tulis balasan untuk @${commentUser} di sini...`;
                commentTextInputArticle.focus();
                cancelReplyArticleButton.style.display = 'inline-block';
            });
        });

        // Event listener for cancel reply button
        cancelReplyArticleButton.addEventListener('click', () => {
            parentCommentIdInputArticle.value = '';
            commentLabelArticle.textContent = 'Tulis komentar Anda di sini:';
            commentTextInputArticle.placeholder = 'Tulis komentar Anda di sini...';
            commentTextInputArticle.value = '';
            cancelReplyArticleButton.style.display = 'none';
        });
    }
});
</script>
