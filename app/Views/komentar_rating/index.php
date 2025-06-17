<?php
// niflix_project/app/Views/komentar_rating/index.php

require_once APP_ROOT . '/app/Views/includes/header.php';

$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}
?>

<main>
    <div class="articles-container">
        <h1><?= escape_html($title) ?></h1>

        <?php if (isset($message) && $message): ?>
            <div class="notification <?= escape_html($message_type ?? '') ?>"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <section class="item-list-section">
            <h2>Film</h2>
            <?php if (!empty($films)): ?>
                <div class="item-grid-container">
                    <?php foreach ($films as $film): ?>
                        <div class="item-card">
                            <a href="<?= $basePath ?>/komentar_rating/detail/film/<?= escape_html($film['id']) ?>">
                                <?php if (!empty($film['image_url'])): ?>
                                    <img src="<?= escape_html($film['image_url']) ?>" alt="<?= escape_html($film['title']) ?>" class="item-thumbnail">
                                <?php else: ?>
                                    <img src="<?= $basePath ?>/assets/img/default_film_thumb.png" alt="No Image" class="item-thumbnail">
                                <?php endif; ?>
                                <div class="item-info">
                                    <h3><?= escape_html($film['title']) ?> (<?= escape_html($film['release_year']) ?>)</h3>
                                    <div class="item-stats">
                                        <span class="stat-item"><i class='bx bxs-star'></i> <?= number_format($film['average_rating'], 1) ?>/10</span>
                                        <span class="stat-item"><i class='bx bxs-message-dots'></i> <?= escape_html($film['total_comments_ratings']) ?></span>
                                        <span class="stat-item"><i class='bx bxs-heart'></i> <?= escape_html($film['total_likes']) ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="info-message">Belum ada film yang ditambahkan.</p>
            <?php endif; ?>
        </section>

        <section class="item-list-section">
            <h2>Series</h2>
            <?php if (!empty($series)): ?>
                <div class="item-grid-container">
                    <?php foreach ($series as $s): ?>
                        <div class="item-card">
                            <a href="<?= $basePath ?>/komentar_rating/detail/series/<?= escape_html($s['id']) ?>">
                                <?php if (!empty($s['image_url'])): ?>
                                    <img src="<?= escape_html($s['image_url']) ?>" alt="<?= escape_html($s['title']) ?>" class="item-thumbnail">
                                <?php else: ?>
                                    <img src="<?= $basePath ?>/assets/img/default_series_thumb.png" alt="No Image" class="item-thumbnail">
                                <?php endif; ?>
                                <div class="item-info">
                                    <h3><?= escape_html($s['title']) ?> (<?= escape_html($s['release_year']) ?>)</h3>
                                    <div class="item-stats">
                                        <span class="stat-item"><i class='bx bxs-star'></i> <?= number_format($s['average_rating'], 1) ?>/10</span>
                                        <span class="stat-item"><i class='bx bxs-message-dots'></i> <?= escape_html($s['total_comments_ratings']) ?></span>
                                        <span class="stat-item"><i class='bx bxs-heart'></i> <?= escape_html($s['total_likes']) ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="info-message">Belum ada series yang ditambahkan.</p>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>
