<?php
    require_once APP_ROOT . '/app/Views/includes/header.php';

    // Pastikan base Path tersedia untuk tautan
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath === '/') {
        $basePath = '';
    } else {
        $basePath = rtrim($basePath, '/');
    }
?>

<main>
    <div class="review-container">
        <h2>Daftar Review Film</h2>

        <a href="<?= $basePath ?>/review_films/create" class="add-review-link">+ Tambah Review Baru</a>

        <?php if (empty($reviews)): ?>
            <p>Belum ada review film. Jadilah yang pertama memberikan review!</p>
        <?php else: ?>
            <ul>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <?php
                            $photo = isset($review['user_photo']) ? $review['user_photo'] : 'default.png';
                            $photoUrl = $basePath . '/uploads/profile_photos/' . htmlspecialchars($photo);
                            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $photoUrl)) {
                                $photoUrl = $basePath . '/assets/img/default.png';
                            }
                        ?>
                        <img src="<?= $photoUrl ?>" alt="Foto <?= htmlspecialchars($review['username'] ?? 'User') ?>" class="user-photo-thumb">

                        <div class="review-content">
                            <div class="review-header">
                                <strong style="color: gold; font-size: 18px;"><?= htmlspecialchars($review['film_title'] ?? 'Judul Tidak Tersedia') ?></strong>
                                <span class="review-meta">Rating: <?= (int)$review['rating'] ?>/10</span>
                            </div>
                            <div class="review-meta">Oleh <?= htmlspecialchars($review['username'] ?? 'Anonim') ?></div>
                            <div class="review-text"><?= nl2br(htmlspecialchars($review['review_text'] ?? '')) ?></div>
                            <div class="review-actions">
                                <!-- View Button -->
                                <a href="<?= $basePath ?>/review_films/show/<?= htmlspecialchars($review['id'] ?? '') ?>" class="btn-action">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                                
                                <?php
                                // Check if user is logged in
                                $userLoggedIn = isset($currentUser);
                                
                                // Check if current user is admin
                                $isAdmin = $userLoggedIn && ($currentUser['is_admin'] == 1);
                                
                                // Check if current user is the review owner
                                $isOwner = $userLoggedIn && isset($review['user_id']) && 
                                        ($currentUser['id'] == $review['user_id']);
                                
                                // Show delete button only for admin or owner
                                if ($isAdmin || $isOwner): ?>
                                    | <a href="<?= $basePath ?>/review_films/delete/<?= htmlspecialchars($review['id'] ?? '') ?>" 
                                    class="btn-action btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus review ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

<?php
    require_once APP_ROOT . '/app/Views/includes/footer.php';
?>
