<?php
// niflix_project/app/Controllers/KomentarRatingController.php

require_once APP_ROOT . '/app/Core/Session.php';
require_once APP_ROOT . '/app/Core/Functions.php';
require_once APP_ROOT . '/app/Models/Review.php';
require_once APP_ROOT . '/app/Models/Rating.php'; // Tambahkan ini
require_once APP_ROOT . '/app/Models/Comment.php'; // Tambahkan ini

class CommentRatingController {
    private $pdo;
    private $reviewModel;
    private $ratingModel; // Aktifkan ini
    private $commentModel; // Aktifkan ini


    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->reviewModel = new Review($pdo);
        $this->ratingModel = new Rating($pdo); // Aktifkan ini
        $this->commentModel = new Comment($pdo); // Aktifkan ini

        // Pastikan pengguna sudah login untuk mengakses halaman ini
        if (!Session::has('user')) {
            redirect('/auth/login');
        }
    }

    /**
     * Menampilkan halaman utama Komentar & Rating.
     */
    public function index() {
        // Panggil metode baru yang efisien untuk mengambil semua data
        $allReviews = $this->reviewModel->getAllReviewsWithMediaTitle();

        // Kirim data ke view
        view('comment_rating/index', [
            'title' => 'Komentar & Rating',
            'allReviews' => $allReviews
        ]);
    }

    public function back() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // Fallback jika halaman sebelumnya tidak tersedia
            redirect('/dashboard');
        }
    }
    
    /**
     * Menyimpan rating baru atau memperbarui rating yang sudah ada.
     */
    public function store_rating() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/dashboard'); // Hanya izinkan POST
        }

        $userId = Session::get('user')['id'];
        $rateableId = $_POST['rateable_id'] ?? null;
        $rateableType = $_POST['rateable_type'] ?? null;
        $value = $_POST['value'] ?? null;

        // Validasi sederhana
        if (empty($rateableId) || empty($rateableType) || empty($value)) {
            // Sebaiknya redirect kembali dengan pesan error
            $this->back();
            return;
        }

        // Simpan ke database
        $this->ratingModel->createOrUpdate($userId, $rateableId, $rateableType, $value);

        // Redirect kembali ke halaman sebelumnya
        $this->back();
    }

    /**
     * Menyimpan komentar baru pada sebuah review.
     */
    public function store_comment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/dashboard'); // Hanya izinkan POST
        }

        $userId = Session::get('user')['id'];
        $reviewId = $_POST['review_id'] ?? null;
        $content = trim($_POST['content'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;

        // Validasi sederhana
        if (empty($reviewId) || empty($content)) {
            $this->back();
            return;
        }
        
        // Simpan ke database
        $this->commentModel->create($reviewId, $userId, $content, $parentId);
        
        // Redirect kembali ke halaman sebelumnya
        $this->back();
    }
}