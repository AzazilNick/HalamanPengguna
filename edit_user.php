<?php
session_start();

// Cek admin
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user']['is_admin'] != 1) {
    header('Location: dashboard.php');
    exit;
}

// Koneksi database
$conn = new mysqli('localhost', 'root', '', 'niflix');

// Ambil data user berdasarkan ID
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_user = null;
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $current_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Proses update
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $current_user) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $nama_lengkap = trim($_POST['nama_lengkap']);

    // Validasi input
    if (empty($username) || empty($email)) {
        $error = "Username dan Email wajib diisi!";
    } else {
        // Update data
        $stmt = $conn->prepare("UPDATE user SET username=?, email=?, nama_lengkap=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $nama_lengkap, $user_id);
        if ($stmt->execute()) {
            $message = "Akun berhasil diperbarui!";
            // Update data current_user untuk form
            $current_user['username'] = $username;
            $current_user['email'] = $email;
            $current_user['nama_lengkap'] = $nama_lengkap;
        } else {
            $error = "Gagal memperbarui akun: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Akun</title>
    <link rel="stylesheet" href="StyleDashboard.css">
    <style>
        .edit-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            color: #ffcc00;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px;
            background: #333;
            border: 1px solid #444;
            color: white;
            border-radius: 4px;
        }
        .notification {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success { background: #4CAF50; }
        .error { background: #f44336; }
    </style>
</head>
<body>
    <?php include_once('header.php'); ?>
    <main>
        <div class="edit-container">
            <h2>Edit Akun Pengguna</h2>
            
            <?php if ($error): ?>
                <div class="notification error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($message): ?>
                <div class="notification success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if ($current_user): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($current_user['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($current_user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($current_user['nama_lengkap']) ?>">
                </div>
                <button type="submit" class="btn">Simpan Perubahan</button>
                <a href="admin.php" class="btn">Kembali</a>
            </form>
            <?php else: ?>
                <div class="notification error">Akun tidak ditemukan!</div>
            <?php endif; ?>
        </div>
    </main>

    <?php include_once('footer.php'); ?>
</body>
</html>
