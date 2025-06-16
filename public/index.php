<?php
// niflix_project/public/index.php

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definisikan path absolut ke folder aplikasi
define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

// Muat helper functions
require_once APP_ROOT . '/app/Core/Functions.php';
require_once APP_ROOT . '/app/Core/Session.php';

// Muat konfigurasi database
$dbConfig = require APP_ROOT . '/app/config/database.php';

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

$requestUri = $_SERVER['REQUEST_URI'];
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}

if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}
$uriSegments = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

$controllerFilePath = '';

if (!empty($uriSegments[0])) {

    $controllerCandidate = ucfirst($uriSegments[0]) . 'Controller';
    $tempControllerFilePath = APP_ROOT . '/app/Controllers/' . $controllerCandidate . '.php';

    if (file_exists($tempControllerFilePath)) {
        $controllerName = $controllerCandidate;
        $controllerFilePath = $tempControllerFilePath;
        array_shift($uriSegments);

    } else {
        switch ($uriSegments[0]) {
            case 'articles':
                $controllerName = 'ArticleController';
                $controllerFilePath = APP_ROOT . '/app/Controllers/ArticleController.php';
                array_shift($uriSegments);
                break;
            case 'comment':
                $controllerName = 'CommentRatingController';
                $controllerFilePath = APP_ROOT . '/app/Controllers/CommentRatingController.php';
                array_shift($uriSegments);

                if (!empty($uriSegments[0]) && $uriSegments[0] === 'delete' && !empty($uriSegments[1])) {
                    $actionName = 'deleteEntry';
                    array_shift($uriSegments);
                    $entryIdToDelete = array_shift($uriSegments);
                    $params = [$entryIdToDelete];
                    $routeHandled = true;
                } elseif (!empty($uriSegments[0]) && $uriSegments[0] === 'addCommentAjax') { // NEW AJAX ROUTE
                    $actionName = 'addCommentAjax';
                    array_shift($uriSegments);
                    $routeHandled = true;
                }
                // Add the new toggleCommentLikeAjax route here
                elseif (!empty($uriSegments[0]) && $uriSegments[0] === 'toggleCommentLikeAjax') { // NEW AJAX ROUTE FOR COMMENT LIKES
                    $actionName = 'toggleCommentLikeAjax';
                    array_shift($uriSegments);
                    $routeHandled = true;
                }
                else {
                    header("HTTP/1.0 404 Not Found");
                    echo "<h1>404 Not Found</h1><p>URL komentar tidak valid.</p>";
                    exit();
                }
                break;
            case 'daftar_series':
                $controllerName = 'SeriesController';
                $controllerFilePath = APP_ROOT . '/app/Controllers/SeriesController.php';
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
                $controllerFilePath = APP_ROOT . '/app/Controllers/FilmController.php';
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
                $controllerFilePath = APP_ROOT . '/app/Controllers/ReviewFilmController.php';
                array_shift($uriSegments);
                break;
            case 'review_series':
                $controllerName = 'ReviewSeriesController';
                $controllerFilePath = APP_ROOT . '/app/Controllers/ReviewSeriesController.php';
                array_shift($uriSegments);
                break;
            case 'komentar_rating':
                $controllerName = 'CommentRatingController';
                $controllerFilePath = APP_ROOT . '/app/Controllers/CommentRatingController.php';
                array_shift($uriSegments);
                break;
            default:
                break;
        }
    }
}

if (empty($controllerFilePath)) {
    $controllerFilePath = APP_ROOT . '/app/Controllers/' . $controllerName . '.php';
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
        } elseif ($actionName === 'addCommentAjax' || $actionName === 'toggleCommentLikeAjax') { // Handle the new AJAX actions
            call_user_func_array([$controller, $actionName], []); // No explicit params for AJAX, they come from POST
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