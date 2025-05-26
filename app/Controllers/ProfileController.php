<?php
// niflix_project/app/Controllers/ProfileController.php

require_once APP_ROOT . '/app/Core/Session.php';
require_once APP_ROOT . '/app/Core/Functions.php';
require_once APP_ROOT . '/app/Models/User.php';

class ProfileController {
    private $pdo;
    private $userModel;
    private $uploadDir = PUBLIC_PATH . '/uploads/profile_photos/'; // Lokasi penyimpanan foto profil

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);

        // Pastikan pengguna sudah login untuk mengakses halaman profil
        if (!Session::has('user')) {
            redirect('/auth/login');
        }

        // Pastikan direktori upload ada
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true); // Buat direktori dengan izin 0755 (atau 0777 jika 0755 gagal)
        }
    }

    /**
     * Menampilkan halaman profil atau memproses update.
     */
    public function index() {
        $userId = Session::get('user')['id'];
        $currentUser = $this->userModel->findById($userId);

        $message = null;
        $messageType = null;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $updateData = [];

            // Validasi username
            if (empty($username)) {
                $error = "Username tidak boleh kosong.";
            } else if ($username !== $currentUser['username'] && $this->userModel->usernameExists($username)) {
                $error = "Username sudah digunakan!";
            } else {
                $updateData['username'] = $username;
            }

            // Validasi email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Mohon masukkan alamat email yang valid.";
            } else if ($email !== $currentUser['email'] && $this->userModel->emailExists($email, $userId)) {
                $error = "Email sudah digunakan!";
            } else {
                $updateData['email'] = $email;
            }

            // Tambahkan nama lengkap
            $updateData['nama_lengkap'] = $fullname;

            // Periksa perubahan password
            if (!empty($newPassword)) {
                if (empty($currentPassword)) {
                    $error = "Mohon masukkan password saat ini untuk mengubahnya.";
                } elseif (!password_verify($currentPassword, $currentUser['password'])) {
                    $error = "Password saat ini salah.";
                } elseif ($newPassword !== $confirmPassword) {
                    $error = "Password baru tidak cocok.";
                } else {
                    $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                }
            }

            // Handle upload foto profil
            $photoPath = $currentUser['foto_pengguna']; // Default ke foto yang sudah ada
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
                $fileName = $_FILES['profile_photo']['name'];
                $fileSize = $_FILES['profile_photo']['size'];
                $fileType = $_FILES['profile_photo']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedFileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
                if (in_array($fileExtension, $allowedFileExtensions)) {
                    // Bersihkan nama file untuk keamanan
                    $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
                    $destPath = $this->uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // Hapus foto lama jika bukan 'default.png'
                        if ($photoPath !== 'default.png' && file_exists($this->uploadDir . $photoPath)) {
                            unlink($this->uploadDir . $photoPath);
                        }
                        $updateData['foto_pengguna'] = $newFileName; // Simpan hanya nama file
                    } else {
                        $error = "Maaf, ada kesalahan saat mengunggah file Anda.";
                    }
                } else {
                    $error = "Jenis file tidak diizinkan. Hanya JPG, JPEG, PNG, GIF.";
                }
            }

            // Jika tidak ada error, lakukan update
            if (empty($error)) {
                if ($this->userModel->update($userId, $updateData)) {
                    $message = "Profil berhasil diperbarui!";
                    $messageType = 'success';
                    // Perbarui data pengguna di sesi setelah update berhasil
                    $updatedUser = $this->userModel->findById($userId);
                    Session::set('user', [
                        'id' => $updatedUser['id'],
                        'username' => $updatedUser['username'],
                        'is_admin' => $updatedUser['is_admin'],
                        'photo' => $updatedUser['foto_pengguna'] // Update foto di session
                    ]);
                    // Refresh data pengguna yang ditampilkan di form
                    $currentUser = $updatedUser;
                } else {
                    $error = "Terjadi kesalahan saat memperbarui profil: " . ($this->pdo->errorInfo()[2] ?? 'Unknown error'); // Ambil error PDO jika ada
                }
            }
        }
        view('profile', [
            'currentUser' => $currentUser,
            'message' => $message,
            'message_type' => $messageType,
            'error' => $error,
            'title' => 'My Profile'
        ]);
    }
}