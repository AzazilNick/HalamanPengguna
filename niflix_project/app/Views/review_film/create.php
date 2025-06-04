<?php
    require_once APP_ROOT . '/app/Views/includes/header.php';
?>

<div class="review-container">
    <h2>Tambah Review Film</h2>

    <form action="/review_film/store" method="POST">
        <label for="film_id">Judul Film:</label>
        <input type="text" id="film_id" name="film_id" required placeholder="Masukkan judul film">

        <label for="review_text">Ulasan:</label>
        <textarea name="review_text" id="review_text" rows="5" required></textarea>

        <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_login'] ?? 1) ?>"><!-- ganti dengan user login -->

        <button type="submit">Kirim Review</button>
    </form>
</div>

<?php
    require_once APP_ROOT . '/app/Views/includes/footer.php';
?>