<?php
    require_once APP_ROOT . '/app/Views/includes/header.php';
?>

<main>
    <div class="review-container">
        <h2>Tambah Review Series</h2>

        <form action="<?= $basePath ?>/review_series/store" method="POST">
            <label for="series_id">Judul Series:</label>
            <select id="series_id" name="series_id" required>
                <?php foreach ($series as $serie): ?>
                    <option value="<?= $serie['id'] ?>"><?= htmlspecialchars($serie['title']) ?></option>
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
