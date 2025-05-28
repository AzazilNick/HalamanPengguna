<?php
// niflix_project/app/Views/profile.php
// $currentUser, $message, $message_type, $error, $title akan tersedia dari ProfileController

require_once APP_ROOT . '/app/Views/includes/header.php';

// Pastikan base Path tersedia untuk gambar dan tautan
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/') {
    $basePath = '';
} else {
    $basePath = rtrim($basePath, '/');
}

// Path lengkap ke foto profil
$profilePhotoUrl = $basePath . '/uploads/profile_photos/' . escape_html($currentUser['foto_pengguna'] ?? 'default.png');
// Jika default.png ada di folder public/assets/img/
if (strpos($profilePhotoUrl, 'default.png') !== false && !file_exists(PUBLIC_PATH . '/uploads/profile_photos/default.png')) {
    $profilePhotoUrl = $basePath . '/assets/img/default.png'; // Asumsi default.png ada di assets/img
}

?>

<div class="container py-4">
    <div class="card bg-dark text-white border-secondary shadow-lg p-4 profile-container">
        <div class="card-header bg-dark text-center border-0 pb-0">
            <h1>My Profile</h1>
        </div>
        <div class="card-body">
            <div id="profile-notification">
                <?php if (isset($message) && $message): ?>
                    <div class="alert <?= escape_html($message_type) == 'success' ? 'alert-success' : 'alert-danger' ?> text-center mb-4" role="alert"><?= escape_html($message) ?></div>
                <?php endif; ?>
                <?php if (isset($error) && $error): // Error dari validasi PHP awal (sebelum AJAX) ?>
                    <div class="alert alert-danger text-center mb-4" role="alert"><?= escape_html($error) ?></div>
                <?php endif; ?>
            </div>

            <form class="row g-4 justify-content-center" method="POST" enctype="multipart/form-data">
                <div class="col-md-4 text-center border-end border-secondary pb-md-0 pb-4">
                    <img src="<?= $profilePhotoUrl ?>"
                        alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">

                    <div class="mb-3">
                        <label for="profile_photo" class="btn btn-secondary btn-sm">
                            Change Profile Photo
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="d-none">
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mb-3 row">
                        <label for="username" class="col-sm-4 col-form-label text-warning text-md-end">Username:</label>
                        <div class="col-sm-8">
                            <input type="text" id="username" name="username"
                                class="form-control bg-secondary text-white border-dark" value="<?= escape_html($currentUser['username']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="fullname" class="col-sm-4 col-form-label text-warning text-md-end">Full Name:</label>
                        <div class="col-sm-8">
                            <input type="text" id="fullname" name="fullname"
                                class="form-control bg-secondary text-white border-dark" value="<?= escape_html($currentUser['nama_lengkap']) ?>">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="email" class="col-sm-4 col-form-label text-warning text-md-end">Email:</label>
                        <div class="col-sm-8">
                            <input type="email" id="email" name="email"
                                class="form-control bg-secondary text-white border-dark" value="<?= escape_html($currentUser['email']) ?>" required>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top border-secondary">
                        <h3 class="text-warning text-center mb-4">Change Password</h3>

                        <div class="mb-3 row">
                            <label for="current_password" class="col-sm-4 col-form-label text-warning text-md-end">Current Password:</label>
                            <div class="col-sm-8">
                                <input type="password" id="current_password" name="current_password" class="form-control bg-secondary text-white border-dark">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="new_password" class="col-sm-4 col-form-label text-warning text-md-end">New Password:</label>
                            <div class="col-sm-8">
                                <input type="password" id="new_password" name="new_password" class="form-control bg-secondary text-white border-dark">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="confirm_password" class="col-sm-4 col-form-label text-warning text-md-end">Confirm New Password:</label>
                            <div class="col-sm-8">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control bg-secondary text-white border-dark">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid d-md-block text-md-start mt-4">
                        <button type="submit" class="btn btn-niflix">Update Profile</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once APP_ROOT . '/app/Views/includes/footer.php';
?>