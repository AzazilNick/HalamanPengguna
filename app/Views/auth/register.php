<?php
// niflix_project/app/Views/auth/register.php
// $message dan $message_type akan tersedia dari AuthController

// Pastikan escape_html() tersedia
if (!function_exists('escape_html')) {
    require_once APP_ROOT . '/app/Core/Functions.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Halaman Pendaftaran">
    <meta name="author" content="Kel 7">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/niflix_project/public/assets/css/auth_override.css">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-container">
        <div class="auth-header text-center mb-4">
            <h2>Daftar</h2>
        </div>
        <?php if (isset($message) && $message): ?>
            <div class="alert <?= escape_html($message_type) == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-3" role="alert"><?= escape_html($message) ?></div>
        <?php endif; ?>
        <form action="/niflix_project/public/auth/register" method="post">
            <div class="mb-3 input-group-icon">
                <label for="fullname" class="form-label">Nama Lengkap:</label>
                <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Nama Lengkap" required>
                <i class='bx bx-user'></i>
            </div>
            <div class="mb-3 input-group-icon">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                <i class='bx bx-envelope'></i>
            </div>
            <div class="mb-3 input-group-icon">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
                <i class='bx bx-user-circle'></i>
            </div>
            <div class="mb-3 input-group-icon">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <i class='bx bx-lock'></i>
            </div>
            <div class="mb-3 input-group-icon">
                <label for="confirm-password" class="form-label">Konfirmasi Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" class="form-control" placeholder="Konfirmasi Password" required>
                <i class='bx bx-lock'></i>
            </div>
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-niflix-submit">Daftar</button>
            </div>
        </form>
        <div class="text-center signup-link">
            <p>Sudah punya akun? <a href="/niflix_project/public/auth/login">Login di sini</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>