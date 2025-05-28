<?php
// niflix_project/app/Views/articles/index.php
// $articles akan tersedia dari ArticleController
// $title akan tersedia dari ArticleController
// $message dan $message_type akan tersedia dari ArticleController (dari parameter URL)

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php';

// Pastikan base Path tersedia untuk tautan
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}
?>

<div class="container py-4">
    <h1 class="text-warning text-center mb-4"><?= escape_html($title) ?></h1>

    <?php if (isset($message) && $message): ?>
        <div class="alert <?= escape_html($message_type ?? '') == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-3" role="alert"><?= escape_html($message) ?></div>
    <?php endif; ?>

    <?php if (Session::has('user')): // Hanya tampilkan tombol buat artikel jika sudah login ?>
        <p class="text-center mb-4"><a href="<?= $basePath ?>/articles/create" class="btn btn-niflix">Buat Artikel Baru</a></p>
    <?php else: ?>
        <p class="info-message text-center text-muted fst-italic p-3 rounded mb-4">Login untuk membuat artikel baru. <a href="<?= $basePath ?>/auth/login" class="text-warning fw-bold text-decoration-none">Login</a></p>
    <?php endif; ?>


    <?php if (!empty($articles)): ?>
        <div class="row row-cols-1 g-4">
            <?php foreach ($articles as $article): ?>
            <div class="col">
                <div class="card bg-dark text-white border-secondary shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="card-title">
                            <a href="<?= $basePath ?>/articles/show/<?= escape_html($article['id']) ?>"><?= escape_html($article['title']) ?></a>
                        </h2>
                        <p class="card-text text-muted small">Oleh: <?= escape_html($article['author_fullname'] ?: $article['author_username']) ?> pada <?= date('d F Y', strtotime($article['created_at'])) ?></p>
                        <p class="card-text"><?= escape_html(substr($article['content'], 0, 200)) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= $basePath ?>/articles/show/<?= escape_html($article['id']) ?>" class="btn btn-sm btn-outline-warning">Baca Selengkapnya</a>
                            <?php
                                $currentUser = Session::get('user');
                                if (isset($currentUser) && ($currentUser['id'] == $article['user_id'] || $currentUser['is_admin'] == 1)) :
                            ?>
                                <div class="btn-group" role="group" aria-label="Article actions">
                                    <a href="<?= $basePath ?>/articles/edit/<?= escape_html($article['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="<?= $basePath ?>/articles/delete/<?= escape_html($article['id']) ?>"
                                    onclick="return confirm('Yakin ingin menghapus artikel ini?')" class="btn btn-sm btn-danger">Hapus</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-white-50">Belum ada artikel yang dipublikasikan.</p>
    <?php endif; ?>
</div>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>