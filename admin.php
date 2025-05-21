<?php
session_start();

// dicek biar tidak dapat mengakses halaman admin dari awal.
// Cek admin
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// kalo data dari is_admin tidak sama dengan 1
// maka akan ke dashboard dan tidak dapat mengakses halaman admin
if ($_SESSION['user']['is_admin'] != 1) {
    header('Location: dashboard.php');
    exit;
}

// kalo admin lanjut ke kode ini
// Koneksi database
$conn = new mysqli('localhost', 'root', '', 'niflix');

// Hapus user
// kalo method get dengan variable action telah di set 
// dan variable action bernilai delete
// dan variable id juga telah diset
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // escape special character didalam string untuk mencegah sql injection
    // dengan membersihkan string dari karakter-karakter berbahaya.
    $id = $conn->real_escape_string($_GET['id']);
    // menjalankan query sql untuk menghapus user berdasarkan id
    $conn->query("DELETE FROM user WHERE id = $id");
}

// Ambil semua user
// simpan ke variable users untuk menampilkan user nantinya
$users = $conn->query("SELECT * FROM user");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Akun</title>
    <link rel="stylesheet" href="style_admin.css">

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

                <!-- looping untuk mengambil setiap 1 baris di variable users-->
                 <!-- menyimpan setiap baris yg di loop ke variable user -->
                <?php while($user = $users->fetch_assoc()): ?>
                <tr>
                    <!-- menampilkan data id, dll dari variable user -->
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td><?= $user['nama_lengkap'] ?></td>
                    <td class="action-links">
                        <!-- kode php untuk mengisi nilai id di url nya -->
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