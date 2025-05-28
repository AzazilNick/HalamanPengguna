<?php
// niflix_project/app/Views/articles/create.php
// $title, $message, $message_type akan tersedia dari ArticleController

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
        <h1 class="text-warning text-center mb-4"><?= escape_html($title) ?></h1>

        <?php if (isset($message) && $message): ?>
            <div class="alert <?= escape_html($message_type ?? '') == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-4" role="alert"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <form action="<?= $basePath ?>/articles/create" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label text-warning">Judul Artikel:</label>
                <input type="text" id="title" name="title" class="form-control bg-secondary text-white border-dark" required value="<?= escape_html($_POST['title'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="content" class="form-label text-warning">Konten Artikel:</label>
                <textarea id="content" name="content" class="form-control bg-secondary text-white border-dark" rows="15" required><?= escape_html($_POST['content'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-niflix me-2">Publikasikan Artikel</button>
                <a href="<?= $basePath ?>/articles" class="btn btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>