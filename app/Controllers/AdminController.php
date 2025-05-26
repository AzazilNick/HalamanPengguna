<?php
// niflix_project/app/Controllers/AdminController.php

require_once APP_ROOT . '/app/Core/Session.php';
require_once APP_ROOT . '/app/Core/Functions.php';
require_once APP_ROOT . '/app/Models/User.php';

class AdminController {
    private $pdo;
    private $userModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->checkAdminAccess();
    }

    private function checkAdminAccess() {
        if (!Session::has('user') || Session::get('user')['is_admin'] != 1) {
            redirect('/dashboard');
        }
    }

    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index() {
        $users = $this->userModel->getAllUsers();

        // Tangani pesan dari parameter URL
        $message = $_GET['message'] ?? null;
        $messageType = $_GET['type'] ?? null;

        view('admin/manage_users', [
            'users' => $users,
            'title' => 'Kelola Akun',
            'message' => $message,       // Lewatkan pesan ke view
            'message_type' => $messageType // Lewatkan tipe pesan ke view
        ]);
    }

    /**
     * Menangani penghapusan pengguna.
     * @param int $id ID pengguna yang akan dihapus
     */
    public function delete($id) {
        $message = '';
        $messageType = '';

        if ($this->userModel->delete($id)) {
            $message = 'Akun berhasil dihapus!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menghapus akun.';
            $messageType = 'error';
        }
        // Arahkan kembali dengan pesan di parameter URL
        redirect('/admin?message=' . urlencode($message) . '&type=' . urlencode($messageType));
    }

    // Metode edit_user akan ditambahkan kemudian
    public function edit_user($id) {
        // Ini akan diimplementasikan setelah manage_users berfungsi
        // Untuk saat ini, bisa redirect atau tampilkan placeholder
        redirect('/admin?message=' . urlencode('Fitur edit belum diimplementasikan.') . '&type=info');
    }
}