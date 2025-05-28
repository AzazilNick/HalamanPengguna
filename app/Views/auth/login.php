<?php
// niflix_project/app/Views/auth/login.php
// $error akan tersedia dari AuthController

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
    <meta name="description" content="Login page">
    <meta name="author" content="Kel 7">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/niflix_project/public/assets/css/auth_override.css">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-container">
        <div class="auth-header text-center mb-4">
            <h2>Login</h2>
        </div>
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger text-center mb-3" role="alert"><?= escape_html($error) ?></div>
        <?php endif; ?>
        <form action="/niflix_project/public/auth/login" method="post">
            <div class="mb-3 input-group-icon">
                <label for="username" class="form-label">Username/Email:</label>
                <input type="text" id="username" class="form-control" name="username" placeholder="Email/Username" autocomplete="off" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="mb-3 input-group-icon">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" class="form-control" name="password" placeholder="Password" autocomplete="off" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember_me">
                    <label class="form-check-label" for="remember_me">Remember me</label>
                </div>
                <div>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>
            </div>
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-niflix-submit">Sign in</button>
            </div>
        </form>
        <div class="text-center signup-link">
            <p>Don't have account? <a href="/niflix_project/public/auth/register">Register</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>