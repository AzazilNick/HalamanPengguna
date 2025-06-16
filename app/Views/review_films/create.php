<?php
    require_once APP_ROOT . '/app/Views/includes/header.php';
?>

<main>
    <div class="review-container">
        <h2>Tambah Review Film</h2>

        <form action="<?= $basePath ?>/review_films/store" method="POST">
            <label for="film_id">Judul Film:</label>
            <select id="film_id" name="film_id" required>
                <?php foreach ($films as $film): ?>
                    <option value="<?= $film['id'] ?>"><?= htmlspecialchars($film['title']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="review_text">Ulasan:</label>
            <textarea name="review_text" id="review_text" rows="5" required></textarea>

            <!-- Tambahan: Rating 1–10 tanpa keterangan -->
            <label for="rating">Rating (1 - 10):</label>
            <select id="rating" name="rating" required>
                <option value="">-- Pilih Rating --</option>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit">Kirim Review</button>
        </form>
    </div>
</main>

<?php
    require_once APP_ROOT . '/app/Views/includes/footer.php';
?>
