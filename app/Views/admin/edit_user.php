<?php
// niflix_project/app/Views/admin/edit_user.php
// $user, $message, $message_type, $error, $title akan tersedia dari AdminController

// Memuat header
require_once APP_ROOT . '/app/Views/includes/header.php';

// Pastikan base Path tersedia untuk tautan dan gambar
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}

// Path lengkap ke foto profil pengguna yang diedit
$profilePhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($user['foto_pengguna'] ?? 'default.png');
// Jika default.png tidak ada di uploads/profile_photos, coba di assets/img
if (strpos($profilePhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
    $profilePhotoUrl = $basePath . '/assets/img/default.png';
}

?>

<div class="container py-4">
    <div class="card bg-dark text-white border-secondary shadow-lg p-4 admin-edit-user-container">
        <h1 class="text-warning text-center mb-4"><?= escape_html($title) ?>: <?= escape_html($user['username']) ?></h1>

        <?php if (isset($message) && $message): ?>
            <div class="alert <?= escape_html($message_type ?? '') == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-4" role="alert"><?= escape_html($message) ?></div>
        <?php endif; ?>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger text-center mb-4" role="alert"><?= escape_html($error) ?></div>
        <?php endif; ?>

        <?php if ($user): ?>
            <form action="<?= $basePath ?>/admin/edit_user/<?= escape_html($user['id']) ?>" method="POST" enctype="multipart/form-data">
                <div class="row align-items-start g-4">
                    <div class="col-md-4 text-center border-end border-secondary pb-md-0 pb-4">
                        <img src="<?= $profilePhotoUrl ?>" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <div class="mb-3">
                            <label for="profile_photo" class="btn btn-secondary btn-sm">Ubah Foto Profil</label>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="d-none">
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="mb-3 row">
                            <label for="username" class="col-sm-4 col-form-label text-warning text-md-end">Username:</label>
                            <div class="col-sm-8">
                                <input type="text" id="username" name="username" class="form-control bg-secondary text-white border-dark" value="<?= escape_html($user['username']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="fullname" class="col-sm-4 col-form-label text-warning text-md-end">Nama Lengkap:</label>
                            <div class="col-sm-8">
                                <input type="text" id="fullname" name="fullname" class="form-control bg-secondary text-white border-dark" value="<?= escape_html($user['nama_lengkap']) ?>">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="email" class="col-sm-4 col-form-label text-warning text-md-end">Email:</label>
                            <div class="col-sm-8">
                                <input type="email" id="email" name="email" class="form-control bg-secondary text-white border-dark" value="<?= escape_html($user['email']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="is_admin" class="col-sm-4 col-form-label text-warning text-md-end">Status Admin:</label>
                            <div class="col-sm-8">
                                <select id="is_admin" name="is_admin" class="form-select bg-secondary text-white border-dark">
                                    <option value="0" <?= $user['is_admin'] == 0 ? 'selected' : '' ?>>Tidak</option>
                                    <option value="1" <?= $user['is_admin'] == 1 ? 'selected' : '' ?>>Ya</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top border-secondary">
                            <h3 class="text-warning text-center mb-4">Ubah Password (kosongkan jika tidak ingin diubah)</h3>
                            <div class="mb-3 row">
                                <label for="new_password" class="col-sm-4 col-form-label text-warning text-md-end">Password Baru:</label>
                                <div class="col-sm-8">
                                    <input type="password" id="new_password" name="new_password" class="form-control bg-secondary text-white border-dark">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="confirm_password" class="col-sm-4 col-form-label text-warning text-md-end">Konfirmasi Password Baru:</label>
                                <div class="col-sm-8">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control bg-secondary text-white border-dark">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-niflix me-2">Perbarui Pengguna</button>
                            <a href="<?= $basePath ?>/admin" class="btn btn-cancel">Batal</a>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <p class="text-center text-white-50">Pengguna tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Memuat footer
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>