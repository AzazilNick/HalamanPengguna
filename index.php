<?php
// niflix_project/index.php

// Dapatkan path dasar URL (misalnya, /niflix_project)
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/' || $basePath === '\\') { // Tangani kasus jika di root domain
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/\\'); // Hapus trailing slash/backslash
}

// Bangun URL lengkap untuk redirect ke public/auth/login
$redirectUrl = $basePath . '/public/auth/login';

// Lakukan redirect
header('Location: ' . $redirectUrl);
exit();
?>
