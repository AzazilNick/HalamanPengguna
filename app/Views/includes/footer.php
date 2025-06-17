<?php
// niflix_project/app/Views/includes/footer.php

// Pastikan base Path tersedia
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}
// Gunakan $assetsBasePath yang sama seperti di header.php
$assetsBasePath = defined('BASE_URL_ASSETS') ? BASE_URL_ASSETS : $basePath . '/public';
?>
    </main>
    <footer>
        <p>&copy; 2025 Movie & Series Review</p>
    </footer>

    <script src="<?= $assetsBasePath ?>/assets/js/script.js"></script>

</body>
</html>