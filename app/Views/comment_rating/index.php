<?php
// niflix_project/app/Views/komentar_rating/index.php
require_once APP_ROOT . '/app/Views/includes/header.php';
$basePath = dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>

<main>
    <div class="articles-container">
        <h1><?= escape_html($title) ?></h1>
        <p class="info-message" style="margin-bottom: 30px;">
            Lihat semua ulasan terbaru dari para pengguna untuk berbagai film dan series.
        </p>

        <?php if (empty($allReviews)): ?>
            <p class="info-message">Belum ada ulasan yang dibuat.</p>
        <?php else: ?>
            <div class="article-list">
                <?php foreach ($allReviews as $review): ?>
                    <div class="article-item">
                        
                        <h2><a href="#"><?= escape_html($review['title']) ?></a></h2>
                        
                        <p class="article-meta">
                            Oleh: <strong><?= escape_html($review['author_fullname'] ?: $review['author_username']) ?></strong> | 
                            Ulasan untuk <?= escape_html(ucfirst($review['reviewable_type'])) ?>: 
                            <strong style="color: #ffcc00;"><?= escape_html($review['media_title']) ?></strong> | 
                            <span style="color: #aaa;"><?= date('d F Y', strtotime($review['created_at'])) ?></span>
                        </p>
                        
                        <p><?= nl2br(escape_html(substr($review['content'], 0, 250))) ?>...</p>
                        
                        <?php
                            // Tentukan path yang benar untuk link detail
                            $detailPath = '';
                            if ($review['reviewable_type'] === 'film') {
                                $detailPath = "/daftar_film/show/" . $review['reviewable_id'];
                            } elseif ($review['reviewable_type'] === 'series') {
                                $detailPath = "/daftar_series/show/" . $review['reviewable_id'];
                            }
                        ?>

                        <a href="<?= $basePath . $detailPath ?>" class="btn">Baca Selengkapnya & Lihat Komentar</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>