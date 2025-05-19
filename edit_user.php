<?php
session_start();

// Cek admin
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

// Koneksi database dengan error handling
$conn = new mysqli('localhost', 'root', '', 'niflix');
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$user = [];
$error = '';

try {
    // Ambil data user dengan prepared statement
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id']; // Sanitasi input sebagai integer
        
        $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('User tidak ditemukan');
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
    }

    // Proses update dengan prepared statement
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = (int)$_POST['id'];
        $username = htmlspecialchars($_POST['username'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $nama_lengkap = htmlspecialchars($_POST['nama_lengkap'] ?? '');

        // Validasi input
        if (empty($username) || empty($email) || empty($nama_lengkap)) {
            throw new Exception('Semua field harus diisi');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid');
        }

        $stmt = $conn->prepare("UPDATE user SET 
            username = ?,
            email = ?,
            nama_lengkap = ?
            WHERE id = ?");
            
        $stmt->bind_param("sssi", 
            $username,
            $email,
            $nama_lengkap,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception('Gagal memperbarui data: ' . $stmt->error);
        }
        
        $stmt->close();
        header('Location: admin.php');
        exit;
    }
} catch (Exception $e) {
    $error = $e->getMessage();
} finally {
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .error { color: red; margin-bottom: 10px; }
        label { display: block; margin: 10px 0; }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">
            
            <label>Username:
                <input type="text" name="username" 
                    value="<?= htmlspecialchars($user['username'] ?? '') ?>" 
                    required>
            </label>
            
            <label>Email:
                <input type="email" name="email" 
                    value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                    required>
            </label>
            
            <label>Nama Lengkap:
                <input type="text" name="nama_lengkap" 
                    value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>" 
                    required>
            </label>
            
            <button type="submit">Simpan Perubahan</button>
            <a href="admin.php" style="margin-left: 10px;">Kembali ke Admin</a>
        </form>
    </div>
</body>
</html>
