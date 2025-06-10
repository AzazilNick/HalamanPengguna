<?php
// niflix_project/app/Controllers/SeriesController.php

require_once APP_ROOT . '/app/Core/Session.php';
require_once APP_ROOT . '/app/Core/Functions.php';
require_once APP_ROOT . '/app/Models/Series.php';

class SeriesController {
    private $pdo;
    private $seriesModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->seriesModel = new Series($pdo);
    }

    /**
     * Memastikan hanya admin yang bisa mengakses fungsi tertentu.
     */
    private function checkAdminAccess() {
        if (!Session::has('user') || Session::get('user')['is_admin'] != 1) {
            redirect('/dashboard?message=' . urlencode('Anda tidak memiliki izin untuk mengakses halaman ini.') . '&type=error');
        }
    }

    /**
     * Menampilkan daftar semua series (popular dan semua).
     */
    public function index() {
        // Pastikan pengguna sudah login
        if (!Session::has('user')) {
            redirect('/auth/login');
        }

        // Ambil series populer (ID 1-8)
        $popularSeries = $this->seriesModel->getSeriesByIdRange();
        // Ambil semua series
        $allSeries = $this->seriesModel->getAllSeries();

        // Tangani pesan dari parameter URL
        $message = $_GET['message'] ?? null;
        $messageType = $_GET['type'] ?? null;

        view('series/index', [
            'popularSeries' => $popularSeries, // Data untuk bagian slider (populer)
            'allSeries' => $allSeries,       // Data untuk bagian grid (semua)
            'title' => 'Series Populer',     // Judul utama halaman
            'message' => $message,
            'message_type' => $messageType
        ]);
    }

    /**
     * Menampilkan detail series tunggal.
     * @param int $id ID series
     */
    public function show($id) {
        $series = $this->seriesModel->findById($id);

        if (!$series) {
            redirect('/daftar_series?message=' . urlencode('Series tidak ditemukan.') . '&type=error');
        }

        view('series/show', [
            'series' => $series,
            'title' => $series['title']
        ]);
    }

    /**
     * Menampilkan formulir untuk membuat series baru atau memproses submission.
     */
    public function create() {
        $this->checkAdminAccess(); // Hanya admin yang bisa mengakses

        $message = null;
        $messageType = null;
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $releaseYear = $_POST['release_year'] ?? '';
        $imageUrl = $_POST['image_url'] ?? '';
        // Initialize $is_popular with a default value for GET requests
        $is_popular = 0; // Default to not popular (0)

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $releaseYear = filter_var($_POST['release_year'] ?? '', FILTER_VALIDATE_INT);
            $imageUrl = trim($_POST['image_url'] ?? '');
            $is_popular_str = $_POST['is_popular'] ?? 'NO'; // Re-capture for POST
            $is_popular = ($is_popular_str === 'YES') ? 1 : 0; // Convert to integer (0 or 1)
            $creatorId = Session::get('user')['id']; // AMBIL ID PENGGUNA DARI SESI

            if (empty($title) || empty($description) || empty($releaseYear)) {
                $message = 'Judul, deskripsi, dan tahun rilis tidak boleh kosong.';
                $messageType = 'error';
            } elseif ($releaseYear === false || $releaseYear <= 0) {
                $message = 'Tahun rilis harus berupa angka valid.';
                $messageType = 'error';
            } else {
                // Pass the converted integer value for is_popular and creatorId
                if ($this->seriesModel->create($title, $description, $releaseYear, $imageUrl, $is_popular, $creatorId)) { // TAMBAHKAN $creatorId
                    $message = 'Series berhasil ditambahkan!';
                    $messageType = 'success';
                    redirect('/daftar_series?message=' . urlencode($message) . '&type=' . urlencode($messageType));
                    exit();
                } else {
                    $message = 'Gagal menambahkan series.';
                    $messageType = 'error';
                }
            }
        }

        view('series/create', [
            'title' => 'Tambah Series Baru',
            'message' => $message,
            'message_type' => $messageType,
            'series' => [
                'title' => $title,
                'description' => $description,
                'release_year' => $releaseYear,
                'image_url' => $imageUrl,
                'is_popular' => $is_popular // This will now always be defined
            ]
        ]);
    }

    /**
     * Menampilkan formulir untuk mengedit series yang sudah ada atau memproses submission.
     * @param int $id ID series
     */
    public function edit($id) {
        $this->checkAdminAccess(); // Hanya admin yang bisa mengakses

        $series = $this->seriesModel->findById($id);

        if (!$series) {
            redirect('/daftar_series?message=' . urlencode('Series tidak ditemukan.') . '&type=error');
        }

        $message = null;
        $messageType = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $releaseYear = filter_var($_POST['release_year'] ?? '', FILTER_VALIDATE_INT);
            $imageUrl = trim($_POST['image_url'] ?? '');
            $is_popular_str = $_POST['is_popular'] ?? 'NO'; // Capture string from POST
            $is_popular = ($is_popular_str === 'YES') ? 1 : 0; // Convert to integer
            $editorId = Session::get('user')['id'];

            if (empty($title) || empty($description) || empty($releaseYear)) {
                $message = 'Judul, deskripsi, dan tahun rilis tidak boleh kosong.';
                $messageType = 'error';
            } elseif ($releaseYear === false || $releaseYear <= 0) {
                $message = 'Tahun rilis harus berupa angka valid.';
                $messageType = 'error';
            } else {
                // Pass the converted integer value for is_popular
                if ($this->seriesModel->update($id, $title, $description, $releaseYear, $imageUrl, $is_popular, $editorId)) {
                    $message = 'Series berhasil diperbarui!';
                    $messageType = 'success';
                    $series = $this->seriesModel->findById($id);
                    redirect('/daftar_series/show/' . $id . '?message=' . urlencode($message) . '&type=' . urlencode($messageType));
                    exit();
                } else {
                    $message = 'Gagal memperbarui series.';
                    $messageType = 'error';
                }
            }
        }

        if (isset($_GET['message'])) {
            $message = $_GET['message'];
            $messageType = $_GET['type'] ?? 'info';
        }

        view('series/edit', [
            'title' => 'Edit Series',
            'series' => $series,
            'message' => $message,
            'message_type' => $messageType,
        ]);
    }

    /**
     * Menghapus series.
     * @param int $id ID series yang akan dihapus
     */
    public function delete($id) {
        $this->checkAdminAccess(); // Hanya admin yang bisa mengakses

        $series = $this->seriesModel->findById($id);

        if (!$series) {
            redirect('/daftar_series?message=' . urlencode('Series tidak ditemukan.') . '&type=error');
        }

        $message = '';
        $messageType = '';

        if ($this->seriesModel->delete($id)) {
            $message = 'Series berhasil dihapus!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menghapus series.';
            $messageType = 'error';
        }
        redirect('/daftar_series?message=' . urlencode($message) . '&type=' . urlencode($messageType));
    }
}