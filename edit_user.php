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

// Hapus user
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Sanitasi input sebagai integer
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Ambil semua user
$users = $conn->query("SELECT * FROM user");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Akun</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <h1>Kelola Akun Pengguna</h1>
    <div class="manage-account">
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Nama Lengkap</th>
                <th>Aksi</th>
            </tr>
            <?php while($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                <td class="action-links">
                    <a href="edit_user.php?id=<?= $user['id'] ?>">Edit</a>
                    <a href="?action=delete&id=<?= $user['id'] ?>" 
                    onclick="return confirm('Yakin hapus akun ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
