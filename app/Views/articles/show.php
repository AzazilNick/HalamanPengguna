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
?>

<div class="container py-4">
    <a href="<?= $basePath ?>/articles" class="btn btn-outline-light mb-4">← Kembali ke Daftar Artikel</a>

    <?php if (isset($message) && $message): ?>
        <div class="alert <?= escape_html($message_type ?? '') == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-4" role="alert"><?= escape_html($message) ?></div>
    <?php endif; ?>

    <?php if ($article): ?>
        <article class="card bg-dark text-white border-secondary shadow-lg mb-5">
            <div class="card-body p-4">
                <h1 class="card-title text-warning mb-3"><?= escape_html($article['title']) ?></h1>
                <p class="card-text text-muted mb-4 d-flex align-items-center">
                    Oleh: <img src="<?= $authorPhotoUrl ?>" alt="Author Photo" class="author-photo-thumb">
                    <strong class="text-white"><?= escape_html($article['author_fullname'] ?: $article['author_username']) ?></strong>
                    pada <?= date('d F Y H:i', strtotime($article['created_at'])) ?>
                    <?php if ($article['created_at'] != $article['updated_at']): ?>
                        (Terakhir diperbarui: <?= date('d F Y H:i', strtotime($article['updated_at'])) ?>)
                    <?php endif; ?>
                </p>
                <div class="card-text article-content">
                    <?= nl2br(escape_html($article['content'])) ?>
                </div>

                <?php
                    $currentUser = Session::get('user');
                    if (isset($currentUser) && ($currentUser['id'] == $article['user_id'] || $currentUser['is_admin'] == 1)) :
                ?>
                    <hr class="border-secondary my-4">
                    <div class="d-flex justify-content-end mt-4">
                        <a href="<?= $basePath ?>/articles/edit/<?= escape_html($article['id']) ?>" class="btn btn-info me-2">Edit Artikel</a>
                        <a href="<?= $basePath ?>/articles/delete/<?= escape_html($article['id']) ?>"
                           onclick="return confirm('Yakin ingin menghapus artikel ini? Semua komentar juga akan terhapus.')" class="btn btn-danger">Hapus Artikel</a>
                    </div>
                <?php endif; ?>
            </article>

            <section class="comments-section bg-dark p-4 rounded shadow-sm">
                <h2 class="text-warning text-center mb-4">Komentar (<?= count($comments) ?>)</h2>

                <?php if (Session::has('user')): ?>
                    <div class="comment-form mb-4">
                        <h3 class="text-white mb-3">Tambahkan Komentar</h3>
                        <form action="<?= $basePath ?>/articles/show/<?= escape_html($article['id']) ?>" method="POST">
                            <div class="mb-3">
                                <textarea name="comment_text" class="form-control bg-secondary text-white border-dark" placeholder="Tulis komentar Anda di sini..." rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-niflix d-block mx-auto">Kirim Komentar</button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="comments-list">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment):
                            // Path lengkap ke foto profil pengomentar
                            $commenterPhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($comment['commenter_photo'] ?? 'default.png');
                            // Jika default.png tidak ada di uploads/profile_photos, coba di assets/img
                            if (strpos($commenterPhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
                                $commenterPhotoUrl = $basePath . '/assets/img/default.png';
                            }
                        ?>
                            <div class="card bg-secondary text-white border-dark mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2 border-bottom border-dark pb-2">
                                        <img src="<?= $commenterPhotoUrl ?>" alt="Commenter Photo" class="commenter-photo-thumb">
                                        <p class="card-text fw-bold mb-0 me-auto"><?= escape_html($comment['commenter_username']) ?></p>
                                        <p class="card-text text-muted small mb-0"><?= date('d M Y, H:i', strtotime($comment['created_at'])) ?></p>
                                    </div>
                                    <p class="card-text mt-3"><?= nl2br(escape_html($comment['comment_text'])) ?></p>
                                    <?php
                                        // Izinkan hapus komentar jika:
                                        // 1. Pengguna saat ini adalah penulis komentar
                                        // 2. Pengguna saat ini adalah penulis artikel
                                        // 3. Pengguna saat ini adalah admin
                                        if (isset($currentUser) && ($currentUser['id'] == $comment['user_id'] || $currentUser['id'] == $article['user_id'] || $currentUser['is_admin'] == 1)) :
                                    ?>
                                        <div class="text-end mt-2">
                                            <a href="<?= $basePath ?>/comment/delete/<?= escape_html($comment['id']) ?>"
                                            onclick="return confirm('Yakin ingin menghapus komentar ini?')" class="btn btn-sm btn-danger">Hapus</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-white-50">Belum ada komentar untuk artikel ini.</p>
                    <?php endif; ?>
                </div>
            </section>

        <?php else: ?>
            <p class="text-center text-white-50">Artikel tidak ditemukan.</p>
        <?php endif; ?>
    </div>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>