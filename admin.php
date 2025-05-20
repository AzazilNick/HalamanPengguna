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
    $id = $conn->real_escape_string($_GET['id']);
    $conn->query("DELETE FROM user WHERE id = $id");
}

// Ambil semua user
$users = $conn->query("SELECT * FROM user");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Akun</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .action-links a { margin-right: 10px; }

        h1 {
            color: #ffcc00;
            text-align: center;
            margin: 20px 0;
        }
        table {
            background: rgba(0, 0, 0, 0.8);
        }
        th {
            background: #222;
            color: #ffcc00;
        }
        td {
            background: #333;
        }
        .action-links a {
            color: #ffcc00;
            text-decoration: none;
        }
        .action-links a:hover {
            color: #ff9100;
        }
    </style>
</head>
<body>
    <?php include_once('header.php'); ?>
    <main>
        <div class="admin" >
            <h1>Kelola Akun Pengguna</h1>
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
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td><?= $user['nama_lengkap'] ?></td>
                    <td class="action-links">
                        <a href="edit_user.php?id=<?= $user['id'] ?>">Edit</a>
                        <a href="?action=delete&id=<?= $user['id'] ?>" 
                        onclick="return confirm('Yakin hapus akun ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </main>
    <?php include_once('footer.php'); ?>