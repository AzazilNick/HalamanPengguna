<?php
// niflix_project/app/Views/includes/footer.php

// Pastikan base Path tersedia
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}
?>
    </main>
    <footer class="mt-auto py-3 bg-dark text-white text-center">
        <p>&copy; 2025 Movie & Series Review</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= $basePath ?>/assets/js/script.js"></script>

</body>
</html>