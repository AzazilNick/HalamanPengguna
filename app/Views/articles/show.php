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