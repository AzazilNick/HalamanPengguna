<?php
// niflix_project/index.php

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definisikan path absolut ke folder aplikasi
// APP_ROOT sekarang menunjuk ke folder 'app'
define('APP_ROOT', __DIR__ . '/app');
// PUBLIC_PATH sekarang menunjuk ke folder 'public'
define('PUBLIC_PATH', __DIR__ . '/public');

// BASE_URL_ASSETS untuk digunakan di front-end (misalnya di header.php untuk JS/CSS)
// Ini akan menjadi '/nama_subfolder_jika_ada/public' atau '/public'
$basePathRoot = rtrim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL_ASSETS', $basePathRoot . '/public');


// Muat helper functions dan session dari dalam folder APP_ROOT
require_once APP_ROOT . '/Core/Functions.php';
require_once APP_ROOT . '/Core/Session.php';

// Muat konfigurasi database
$dbConfig = require APP_ROOT . '/config/database.php';

// Buat koneksi database
try {
    $pdo = new PDO(
        "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['database'] . ";charset=" . $dbConfig['charset'],
        $dbConfig['username'],
        $dbConfig['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// --- Sistem Routing Sederhana ---
$controllerName = 'DashboardController';
$actionName = 'index';
$params = [];
$routeHandled = false;

// Dapatkan URI yang diminta relatif terhadap root aplikasi (bukan lagi public)
$requestUri = $_SERVER['REQUEST_URI'];
// Base path untuk routing (ini akan menjadi '/niflix_project' atau '/')
// Jika aplikasi diakses langsung di root domain, $basePath akan kosong.
// Jika di subfolder (misal /niflix_project), $basePath akan '/niflix_project'
$basePathForRouting = rtrim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');

if (strpos($requestUri, $basePathForRouting) === 0) {
    $requestUri = substr($requestUri, strlen($basePathForRouting));
}
$uriSegments = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

$controllerFilePath = '';

if (!empty($uriSegments[0])) {

    $controllerCandidate = ucfirst($uriSegments[0]) . 'Controller';
    $tempControllerFilePath = APP_ROOT . '/' . $controllerCandidate . '.php'; // Path controller langsung di APP_ROOT

    if (file_exists($tempControllerFilePath)) {
        $controllerName = $controllerCandidate;
        $controllerFilePath = $tempControllerFilePath;
        array_shift($uriSegments);

    } else {
        // Logika routing spesifik kamu tetap sama, hanya sesuaikan path require_once
        switch ($uriSegments[0]) {
            case 'articles':
                $controllerName = 'ArticleController';
                $controllerFilePath = APP_ROOT . '/ArticleController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);
                break;
            case 'comment':
                $controllerName = 'CommentRatingController';
                $controllerFilePath = APP_ROOT . '/CommentRatingController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);

                if (!empty($uriSegments[0]) && $uriSegments[0] === 'delete' && !empty($uriSegments[1])) {
                    $actionName = 'deleteEntry';
                    array_shift($uriSegments);
                    $entryIdToDelete = array_shift($uriSegments);
                    $params = [$entryIdToDelete];
                    $routeHandled = true;
                } elseif (!empty($uriSegments[0]) && $uriSegments[0] === 'addCommentAjax') {
                    $actionName = 'addCommentAjax';
                    array_shift($uriSegments);
                    $routeHandled = true;
                } else {
                    header("HTTP/1.0 404 Not Found");
                    echo "<h1>404 Not Found</h1><p>URL komentar tidak valid.</p>";
                    exit();
                }
                break;
            case 'daftar_series':
                $controllerName = 'SeriesController';
                $controllerFilePath = APP_ROOT . '/SeriesController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);

                if (!empty($uriSegments[0]) && $uriSegments[0] === 'toggleLikeAjax') {
                    $actionName = 'toggleLikeAjax';
                    array_shift($uriSegments);
                    $routeHandled = true;
                }
                if (!empty($uriSegments[0]) && $uriSegments[0] === 'validateFieldAjax') {
                    $actionName = 'validateFieldAjax';
                    array_shift($uriSegments);
                    $routeHandled = true;
                }
                break;
            case 'daftar_film':
                $controllerName = 'FilmController';
                $controllerFilePath = APP_ROOT . '/FilmController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);

                if (!empty($uriSegments[0]) && $uriSegments[0] === 'toggleLikeAjax') {
                    $actionName = 'toggleLikeAjax';
                    array_shift($uriSegments);
                    $routeHandled = true;
                }
                if (!empty($uriSegments[0]) && $uriSegments[0] === 'validateFieldAjax') {
                    $actionName = 'validateFieldAjax';
                    array_shift($uriSegments);
                    $routeHandled = true;
                }
                break;
            case 'review_films':
                $controllerName = 'ReviewFilmController';
                $controllerFilePath = APP_ROOT . '/ReviewFilmController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);
                break;
            case 'review_series':
                $controllerName = 'ReviewSeriesController';
                $controllerFilePath = APP_ROOT . '/ReviewSeriesController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);
                break;
            case 'komentar_rating':
                $controllerName = 'CommentRatingController';
                $controllerFilePath = APP_ROOT . '/CommentRatingController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);
                break;
            case 'admin':
                $controllerName = 'AdminController';
                $controllerFilePath = APP_ROOT . '/AdminController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);
                break;
            case 'profile':
                $controllerName = 'ProfileController';
                $controllerFilePath = APP_ROOT . '/ProfileController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);
                break;
            case 'auth':
                $controllerName = 'AuthController';
                $controllerFilePath = APP_ROOT . '/AuthController.php'; // Langsung di APP_ROOT
                array_shift($uriSegments);
                break;
            default:
                // Jika tidak ada controller yang cocok, fallback ke DashboardController
                $controllerName = 'DashboardController';
                $controllerFilePath = APP_ROOT . '/DashboardController.php';
                // Jika uriSegments[0] adalah action untuk DashboardController, biarkan
                // Jika tidak, mungkin ini bukan action yang valid, biarkan actionName 'index' dan params kosong
                if (method_exists('DashboardController', $uriSegments[0])) {
                    $actionName = array_shift($uriSegments);
                } else {
                    $actionName = 'index'; // Default ke index jika tidak ada action yang cocok
                }
                break;
        }
    }
}
// Tambahkan .php jika belum ada (pastikan selalu ada)
if (strpos($controllerFilePath, '.php') === false) {
    $controllerFilePath .= '.php';
}

if (!file_exists($controllerFilePath)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1><p>Controller file tidak ditemukan: " . htmlspecialchars($controllerName) . ".php</p>";
    exit();
}

require_once $controllerFilePath;

$controller = new $controllerName($pdo);

if (!$routeHandled) {
    if (empty($uriSegments[0])) {
        $actionName = 'index';
    } else {
        $actionName = $uriSegments[0];
        array_shift($uriSegments);
    }

    $params = $uriSegments;
}


if (method_exists($controller, $actionName)) {
    if ($controllerName === 'CommentRatingController') {
        if ($actionName === 'detail' && count($params) >= 2) {
            $itemType = array_shift($params);
            $itemId = array_shift($params);
            call_user_func_array([$controller, $actionName], [$itemType, $itemId]);
        } elseif ($actionName === 'deleteEntry' && count($params) >= 1) {
            $entryId = array_shift($params);
            call_user_func_array([$controller, $actionName], [$entryId]);
        } elseif ($actionName === 'addCommentAjax') { // Handle the new AJAX action
            call_user_func_array([$controller, $actionName], []); // No explicit params, they come from POST
        }
        else {
            call_user_func_array([$controller, $actionName], $params);
        }
    } else {
        call_user_func_array([$controller, $actionName], $params);
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1><p>Halaman tidak ditemukan. Action '" . htmlspecialchars($actionName) . "' pada Controller '" . htmlspecialchars($controllerName) . "' tidak ditemukan.</p>";
}