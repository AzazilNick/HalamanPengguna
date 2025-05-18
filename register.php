<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        $message = 'Password dan konfirmasi password tidak cocok!';
    } else {
        $file = 'users.txt';
        $data = "$username,$email,$password\n";
        $user_exists = false;

        if (file_exists($file)) {
            $lines = file($file);
            foreach ($lines as $line) {
                list($file_username, $file_email, $file_password) = explode(',', trim($line));
                if ($file_username === $username) {
                    $user_exists = true;
                    $message = 'Username sudah terdaftar!';
                    break;
                }
                if ($file_email === $email) {
                    $user_exists = true;
                    $message = 'Email sudah terdaftar!';
                    break;
                }
            }
        }

        if (!$user_exists) {
            file_put_contents($file, $data, FILE_APPEND);
            $message = 'Registrasi berhasil! Silakan login.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Halaman Pendaftaran">
    <meta name="author" content="Kel 7">
    <title>REGISTER</title>
    <link rel="stylesheet" href="Style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="Login-Register-box">
        <div class="login-register-header">
            <header>Daftar</header>
        </div>
        <?php if ($message): ?>
            <div class="notification"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form class="register-form" action="" method="post">
            <div class="Input-box">
                <input type="text" id="fullname" name="fullname" class="input-field" placeholder="Nama Lengkap" required>
                <i class='bx bx-user'></i>
            </div>

            <div class="Input-box">
                <input type="email" id="email" name="email" class="input-field" placeholder="Email" required>
                <i class='bx bx-envelope'></i>
            </div>

            <div class="Input-box">
                <input type="text" id="username" name="username" class="input-field" placeholder="Username" required>
                <i class='bx bx-user-circle'></i>
            </div>

            <div class="Input-box">
                <input type="password" id="password" name="password" class="input-field" placeholder="Password" required>
                <i class='bx bx-lock'></i>
            </div>

            <div class="Input-box">
                <input type="password" id="confirm-password" name="confirm-password" class="input-field" placeholder="Konfirmasi Password" required>
                <i class='bx bx-lock'></i>
            </div>

            <div class="Input-submit">
                <button type="submit" class="submit-btn">Daftar</button>
            </div>
        </form>
        <div class="sign-up-link">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>