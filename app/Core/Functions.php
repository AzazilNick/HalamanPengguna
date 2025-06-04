<?php
// niflix_project/app/Core/Functions.php

/**
 * Memuat file view dan meneruskan data.
 * @param string $path Path ke file view (relatif dari app/Views/)
 * @param array $data Data yang akan dilewatkan ke view
 */
function view($path, $data = []) {
    // Ekstrak data array menjadi variabel-variabel terpisah
    // Misalnya, $data = ['title' => 'Home'] akan menjadi $title = 'Home' di view
    extract($data);

    // Path lengkap ke file view
    $fullPath = APP_ROOT . '/app/Views/' . $path . '.php';

    if (file_exists($fullPath)) {
        require $fullPath;
    } else {
        // Error handling jika view tidak ditemukan
        // Di produksi, bisa diarahkan ke halaman error 404
        die("View not found: " . htmlspecialchars($path));
    }
}

/**
 * Mengarahkan (redirect) ke URL lain.
 * @param string $path Path URL relatif
 */
function redirect($path) {
    // Dapatkan base path dari index.php yang sudah dihitung
    // SCRIPT_NAME akan menjadi "/niflix_project/public/index.php"
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath === '/') {
        $basePath = ''; // Jika di root domain, base path kosong
    }

    // Gabungkan base path dengan path yang diminta
    $fullPath = rtrim($basePath, '/') . '/' . ltrim($path, '/');

    header("Location: " . $fullPath);
    exit();
}

/**
 * Mengamankan string dari Cross-Site Scripting (XSS).
 * @param string $string String yang akan diamankan
 * @return string String yang sudah diamankan
 */
function escape_html($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}