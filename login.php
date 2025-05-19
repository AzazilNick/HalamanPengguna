<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit();
}

$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Login page">
        <meta name="author" content="Kel 7">
        <title>Login</title>
        <link rel="stylesheet" href="style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
        <form action="login_process.php" method="post">
            <div class="Login-Register-box">
                <div class="login-register-header">
                    <header>Login</header>
                </div>
                <?php if ($error_message): ?>
                    <div class="notification"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <div class="Input-box">
                    <label for="username">
                        Username:
                        <input type="text" class="input-field" name="username" placeholder="Email/Username" autocomplete="off" required>
                        <i class='bx bxs-user'></i>
                    </label>
                </div>
                <div class="Input-box">
                    <label>
                        Password:
                        <input type="password" class="input-field" name="password" placeholder="Password" autocomplete="off" required>
                        <i class='bx bxs-lock-alt'></i>
                    </label>
                </div>
                <div class="forgot">
                    <section>
                        <input type="checkbox" id="check">
                        <label for="check">Remember me</label>
                    </section>
                    <section>
                        <a href="#">Forgot password ?</a>
                    </section>
                </div>
                <div class="Input-submit">
                    <button class="submit-btn" id="submit">Sign in</button>
                </div>
                <div class="sign-up-link">
                    <p>Don't have account?<a href="register.php"> Register</a></p>
                </div>
            </div>
        </form>
    </body>
</html>
