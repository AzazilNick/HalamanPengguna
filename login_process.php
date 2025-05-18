<?php
session_start();
$file = 'users.txt';
$username_or_email = $_POST['username'];
$password = $_POST['password'];
$login_success = false;

if (file_exists($file)) {
    $lines = file($file);
    foreach ($lines as $line) {
        list($file_username, $file_email, $file_password) = explode(',', trim($line));
        if (($file_username === $username_or_email || $file_email === $username_or_email) && $file_password === $password) {
            $login_success = true;
            break;
        }
    }
}

if ($login_success) {
    $_SESSION['user'] = $file_username;
    header('Location: dashboard.php');
} else {
    $_SESSION['error'] = 'Login gagal! Username atau password salah.';
    header('Location: login.php');
}
?>