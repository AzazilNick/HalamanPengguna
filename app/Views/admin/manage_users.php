<?php
// niflix_project/app/Views/admin/manage_users.php
// $users akan tersedia dari AdminController
// $title akan tersedia dari AdminController
// $message dan $message_type akan tersedia dari AdminController (from URL parameters)

// Pastikan APP_ROOT didefinisikan (from index.php)
if (!defined('APP_ROOT')) {
    die('APP_ROOT not defined. Invalid entry point.');
}

// Muat helper functions jika belum ada (misalnya escape_html)
if (!function_exists('escape_html')) {
    require_once APP_ROOT . '/app/Core/Functions.php';
}

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php';

// Pastikan base Path tersedia untuk tautan
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}
?>

<div class="container py-4">
    <div class="bg-dark p-4 rounded shadow-lg text-white">
        <h1 class="text-warning text-center mb-4">Kelola Akun Pengguna</h1>

        <?php if (isset($message) && $message): ?>
            <div class="alert <?= escape_html($message_type ?? '') == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-4" role="alert"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">Admin?</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= escape_html($user['id']) ?></td>
                            <td><?= escape_html($user['username']) ?></td>
                            <td><?= escape_html($user['email']) ?></td>
                            <td><?= escape_html($user['nama_lengkap']) ?></td>
                            <td><?= $user['is_admin'] == 1 ? 'Ya' : 'Tidak' ?></td>
                            <td>
                                <a href="<?= $basePath ?>/admin/edit_user/<?= escape_html($user['id']) ?>" class="btn btn-sm btn-info me-2">Edit</a>
                                <a href="<?= $basePath ?>/admin/delete/<?= escape_html($user['id']) ?>"
                                onclick="return confirm('Yakin hapus akun <?= escape_html($user['username']) ?> ini?')" class="btn btn-sm btn-danger">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-white-50">Tidak ada pengguna ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>