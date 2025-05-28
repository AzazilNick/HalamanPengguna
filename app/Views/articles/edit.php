<?php
// niflix_project/app/Views/articles/edit.php
// $article, $title, $message, $message_type akan tersedia dari ArticleController

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
    <div class="card bg-dark text-white border-secondary shadow-lg p-4 form-container">
        <h1 class="text-warning text-center mb-4"><?= escape_html($title) ?>: <?= escape_html($article['title']) ?></h1>

        <?php if (isset($message) && $message): ?>
            <div class="alert <?= escape_html($message_type ?? '') == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-4" role="alert"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <?php if ($article): ?>
            <form action="<?= $basePath ?>/articles/edit/<?= escape_html($article['id']) ?>" method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label text-warning">Judul Artikel:</label>
                    <input type="text" id="title" name="title" class="form-control bg-secondary text-white border-dark" required value="<?= escape_html($article['title']) ?>">
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label text-warning">Konten Artikel:</label>
                    <textarea id="content" name="content" class="form-control bg-secondary text-white border-dark" rows="15" required><?= escape_html($article['content']) ?></textarea>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-niflix me-2">Perbarui Artikel</button>
                    <a href="<?= $basePath ?>/articles/show/<?= escape_html($article['id']) ?>" class="btn btn-cancel">Batal</a>
                </div>
            </form>
        <?php else: ?>
            <p class="text-center text-white-50">Artikel tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>