<?php
session_start();
// Cek admin
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

// Koneksi database
$conn = new mysqli('localhost', 'root', '', 'niflix');

// Ambil data user
$user = [];
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $user = $conn->query("SELECT * FROM user WHERE id = $id")->fetch_assoc();
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nama_lengkap = $_POST['nama_lengkap'];
    
    $conn->query("UPDATE user SET 
        username = '$username',
        email = '$email',
        nama_lengkap = '$nama_lengkap'
        WHERE id = $id
    ");
    
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User</h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        
        <label>Username:
            <input type="text" name="username" value="<?= $user['username'] ?>" required>
        </label><br>
        
        <label>Email:
            <input type="email" name="email" value="<?= $user['email'] ?>" required>
        </label><br>
        
        <label>Nama Lengkap:
            <input type="text" name="nama_lengkap" value="<?= $user['nama_lengkap'] ?>" required>
        </label><br>
        
        <button type="submit">Simpan Perubahan</button>
    </form>
</body>
</html>
