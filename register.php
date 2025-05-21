<?php
$message = '';

// jika request method server adalah post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // variable fullname dengan array post disimpan ke fullname
    // dan selanjutnya
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // kalo password tidak cocok dengan variable confirm_password (untuk saat register)
    if ($password !== $confirm_password) {
        $message = 'Password dan konfirmasi password tidak cocok!';
    } else {
        // variable untuk menyimpan nilai host, user db, password db, dan nama db
        $host = 'localhost';
        $dbuser = 'root';
        $dbpass = '';
        $dbname = 'niflix';

        // mencoba kumpulan statement/perintah didalam try,
        // apabila statement yang ada didalamnya terdapat error
        // maka akan ditangkap oleh blok catch dan akan ditampilkan
        // sebuah message error
        try {
            // untuk membuat atau menyambungkan koneksi ke database
            $conn = new mysqli($host, $dbuser, $dbpass, $dbname);
            
            // kalo terdapat error maka akan menampilkan message koneksi gagal.
            if ($conn->connect_error) {
                throw new Exception("Koneksi gagal: " . $conn->connect_error);
            }

            // Cek duplikat username/email
            // menyimpan suatu query sql ke variable
            $check_query = "SELECT username, email FROM user WHERE username = ? OR email = ?";
            // menyiapkan query sql sebelumnya menggunakan method prepare pada objek conn
            // dan menyimpan nya ke variable stmt
            $stmt = $conn->prepare($check_query);
            // bind_param untuk mengganti tanda "?" dengan nilai dari variable username dan email
            $stmt->bind_param("ss", $username, $email);
            // setelah itu baru kemudian di execute
            $stmt->execute();
            // hasil execute disimpan ke result
            $result = $stmt->get_result();
            
            $duplicate_username = false;
            $duplicate_email = false;

            // me-looping seluruh baris di result
            while ($row = $result->fetch_assoc()) {
                // kalo data username yang ada di row sama dengan variable username (nilai yang baru diregister)
                // maka variable duplikat true
                if ($row['username'] === $username) {
                    $duplicate_username = true;
                }
                if ($row['email'] === $email) {
                    $duplicate_email = true;
                }
            }
            
            // menutup prepared statement
            $stmt->close();

            if ($duplicate_username) {
                throw new Exception('Username sudah terdaftar!');
            }
            if ($duplicate_email) {
                throw new Exception('Email sudah terdaftar!');
            }

            // Hash password
            // meng encrypt password yang baru di input menggunakan password hash
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // variable yang menyimpan string nama untuk default foto akun
            $default_photo = 'default.png';

            // Insert data baru
            $insert_stmt = $conn->prepare("INSERT INTO user 
                (username, email, password, nama_lengkap, foto_pengguna) 
                VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssss", 
                $username, 
                $email, 
                $hashed_password, 
                $fullname, 
                $default_photo);

            if ($insert_stmt->execute()) {
                $message = 'Registrasi berhasil! Silakan login.';
            } else {
                throw new Exception("Terjadi kesalahan saat registrasi");
            }
            
            $insert_stmt->close();
            // menutup koneksi database
            $conn->close();

        } catch (Exception $e) {
            $message = $e->getMessage();
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
        <!-- menampilkan notif/message nya apabila terjadi error -->
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
