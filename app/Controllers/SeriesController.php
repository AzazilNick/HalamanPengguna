<?php
// niflix_project/app/Controllers/SeriesController.php

require_once APP_ROOT . '/app/Core/Session.php'; //
require_once APP_ROOT . '/app/Core/Functions.php'; //
require_once APP_ROOT . '/app/Models/Series.php'; //

class SeriesController {
    private $pdo; //
    private $seriesModel; //

    public function __construct(PDO $pdo) { //
        $this->pdo = $pdo; //
        $this->seriesModel = new Series($pdo); //
    }

    /**
     * Memastikan hanya admin yang bisa mengakses fungsi tertentu.
     */
    private function checkAdminAccess() {
        if (!Session::has('user') || Session::get('user')['is_admin'] != 1) { //
            redirect('/dashboard?message=' . urlencode('Anda tidak memiliki izin untuk mengakses halaman ini.') . '&type=error'); //
        }
    }

    /**
     * Menampilkan daftar semua series (popular dan semua).
     */
    public function index() {
        // Pastikan pengguna sudah login
        if (!Session::has('user')) { //
            redirect('/auth/login'); //
        }

        $currentUserId = Session::get('user')['id']; // Ambil ID pengguna yang sedang login

        // Ambil series populer (ID 1-8)
        $popularSeries = $this->seriesModel->getSeriesByIdRange($currentUserId); // Teruskan userId
        // Ambil semua series
        $allSeries = $this->seriesModel->getAllSeries($currentUserId); // Teruskan userId

        // Tangani pesan dari parameter URL
        $message = $_GET['message'] ?? null;
        $messageType = $_GET['type'] ?? null;

        view('series/index', [
            'popularSeries' => $popularSeries, // Data untuk bagian slider (populer)
            'allSeries' => $allSeries,       // Data untuk bagian grid (semua)
            'title' => 'Daftar Series',     // Judul utama halaman (diperbarui)
            'message' => $message,
            'message_type' => $messageType
        ]);
    }

    /**
     * Endpoint AJAX untuk toggle like pada series.
     */
    public function toggleLikeAjax() {
        header('Content-Type: application/json'); // Pastikan respons dalam format JSON

        if (!Session::has('user')) { // Pastikan pengguna sudah login
            echo json_encode(['success' => false, 'message' => 'Anda harus login untuk menyukai series.', 'redirect' => '/auth/login']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
            exit();
        }

        $seriesId = filter_var($_POST['series_id'] ?? null, FILTER_VALIDATE_INT);
        $userId = Session::get('user')['id'];

        if (!$seriesId) {
            echo json_encode(['success' => false, 'message' => 'ID series tidak valid.']);
            exit();
        }

        $result = $this->seriesModel->toggleLike($userId, $seriesId);

        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'total_likes' => $result['total_likes'],
                'is_liked_by_user' => $result['is_liked_by_user'],
                'message' => $result['is_liked_by_user'] ? 'Series disukai.' : 'Series tidak disukai.'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status like.']);
        }
        exit();
    }

    /**
     * Endpoint AJAX untuk validasi field pada form edit series.
     */
    public function validateFieldAjax() {
        header('Content-Type: application/json');
        $response = ['valid' => true, 'message' => ''];

        if (!Session::has('user') || Session::get('user')['is_admin'] != 1) {
            $response = ['valid' => false, 'message' => 'Anda tidak memiliki izin untuk memvalidasi.', 'redirect' => '/auth/login'];
            echo json_encode($response);
            exit();
        }

        $fieldName = $_POST['fieldName'] ?? '';
        $fieldValue = $_POST['fieldValue'] ?? '';
        $seriesId = filter_var($_POST['seriesId'] ?? null, FILTER_VALIDATE_INT); // Diperlukan untuk validasi unik jika ada

        // Fetch current series data to compare with if needed
        $currentSeries = null;
        if ($seriesId) {
            $currentSeries = $this->seriesModel->findById($seriesId);
        }

        switch ($fieldName) {
            case 'title':
                if (empty($fieldValue)) {
                    $response = ['valid' => false, 'message' => 'Judul series tidak boleh kosong.'];
                }
                // Anda bisa menambahkan validasi unik di sini jika diperlukan,
                // tapi untuk series, judul mungkin tidak perlu unik secara ketat.
                break;
            case 'release_year':
                $year = filter_var($fieldValue, FILTER_VALIDATE_INT);
                if ($year === false || $year <= 0 || $year > date('Y') + 5) { // Misalnya, tidak boleh lebih dari 5 tahun di masa depan
                    $response = ['valid' => false, 'message' => 'Tahun rilis tidak valid.'];
                }
                break;
            case 'image_url':
                if (!empty($fieldValue) && !filter_var($fieldValue, FILTER_VALIDATE_URL)) {
                    $response = ['valid' => false, 'message' => 'URL gambar tidak valid.'];
                }
                break;
            // Anda bisa menambahkan case untuk field lain di sini
            default:
                $response = ['valid' => false, 'message' => 'Field tidak dikenal.'];
                break;
        }

        echo json_encode($response);
        exit();
    }


    /**
     * Menampilkan detail series tunggal.
     * @param int $id ID series
     */
    public function show($id) {
        $series = $this->seriesModel->findById($id); //

        if (!$series) { //
            redirect('/daftar_series?message=' . urlencode('Series tidak ditemukan.') . '&type=error'); //
        }

        view('series/show', [ //
            'series' => $series, //
            'title' => $series['title'] //
        ]);
    }

    /**
     * Menampilkan formulir untuk membuat series baru atau memproses submission.
     */
    public function create() {
        $this->checkAdminAccess(); // Hanya admin yang bisa mengakses //

        $message = null; //
        $messageType = null; //
        $title = $_POST['title'] ?? ''; //
        $description = $_POST['description'] ?? ''; //
        $releaseYear = $_POST['release_year'] ?? ''; //
        $imageUrl = $_POST['image_url'] ?? ''; //
        // Initialize $is_popular with a default value for GET requests
        $is_popular = 0; // Default to not popular (0) //

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { //
            $title = trim($_POST['title'] ?? ''); //
            $description = trim($_POST['description'] ?? ''); //
            $releaseYear = filter_var($_POST['release_year'] ?? '', FILTER_VALIDATE_INT); //
            $imageUrl = trim($_POST['image_url'] ?? ''); //
            $is_popular_str = $_POST['is_popular'] ?? 'NO'; // Re-capture for POST //
            $is_popular = ($is_popular_str === 'YES') ? 1 : 0; // Convert to integer (0 or 1) //
            $creatorId = Session::get('user')['id']; // AMBIL ID PENGGUNA DARI SESI //

            if (empty($title) || empty($description) || empty($releaseYear)) { //
                $message = 'Judul, deskripsi, dan tahun rilis tidak boleh kosong.'; //
                $messageType = 'error'; //
            } elseif ($releaseYear === false || $releaseYear <= 0) { //
                $message = 'Tahun rilis harus berupa angka valid.'; //
                $messageType = 'error'; //
            } else {
                // Pass the converted integer value for is_popular and creatorId
                if ($this->seriesModel->create($title, $description, $releaseYear, $imageUrl, $is_popular, $creatorId)) { // TAMBAHKAN $creatorId //
                    $message = 'Series berhasil ditambahkan!'; //
                    $messageType = 'success'; //
                    redirect('/daftar_series?message=' . urlencode($message) . '&type=' . urlencode($messageType)); //
                    exit(); //
                } else {
                    $message = 'Gagal menambahkan series.'; //
                    $messageType = 'error'; //
                }
            }
        }

        view('series/create', [ //
            'title' => 'Tambah Series Baru', //
            'message' => $message, //
            'message_type' => $messageType, //
            'series' => [ //
                'title' => $title, //
                'description' => $description, //
                'release_year' => $releaseYear, //
                'image_url' => $imageUrl, //
                'is_popular' => $is_popular // This will now always be defined //
            ]
        ]);
    }

    /**
     * Menampilkan formulir untuk mengedit series yang sudah ada atau memproses submission.
     * @param int $id ID series
     */
    public function edit($id) {
        $this->checkAdminAccess(); // Hanya admin yang bisa mengakses //

        $series = $this->seriesModel->findById($id); //

        if (!$series) { //
            redirect('/daftar_series?message=' . urlencode('Series tidak ditemukan.') . '&type=error'); //
        }

        $message = null; //
        $messageType = null; //

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { //
            $title = trim($_POST['title'] ?? ''); //
            $description = trim($_POST['description'] ?? ''); //
            $releaseYear = filter_var($_POST['release_year'] ?? '', FILTER_VALIDATE_INT); //
            $imageUrl = trim($_POST['image_url'] ?? ''); //
            $is_popular_str = $_POST['is_popular'] ?? 'NO'; // Capture string from POST //
            $is_popular = ($is_popular_str === 'YES') ? 1 : 0; // Convert to integer //
            $editorId = Session::get('user')['id']; //

            if (empty($title) || empty($description) || empty($releaseYear)) { //
                $message = 'Judul, deskripsi, dan tahun rilis tidak boleh kosong.'; //
                $messageType = 'error'; //
            } elseif ($releaseYear === false || $releaseYear <= 0) { //
                $message = 'Tahun rilis harus berupa angka valid.'; //
                $messageType = 'error'; //
            } else {
                // Pass the converted integer value for is_popular
                if ($this->seriesModel->update($id, $title, $description, $releaseYear, $imageUrl, $is_popular, $editorId)) { //
                    $message = 'Series berhasil diperbarui!'; //
                    $messageType = 'success'; //
                    $series = $this->seriesModel->findById($id); //
                    redirect('/daftar_series/show/' . $id . '?message=' . urlencode($message) . '&type=' . urlencode($messageType)); //
                    exit(); //
                } else {
                    $message = 'Gagal memperbarui series.'; //
                    $messageType = 'error'; //
                }
            }
        }

        if (isset($_GET['message'])) {
            $message = $_GET['message'];
            $messageType = $_GET['type'] ?? 'info';
        }

        view('series/edit', [ //
            'title' => 'Edit Series', //
            'series' => $series, //
            'message' => $message, //
            'message_type' => $messageType, //
        ]);
    }

    /**
     * Menghapus series.
     * @param int $id ID series yang akan dihapus
     */
    public function delete($id) {
        $this->checkAdminAccess(); // Hanya admin yang bisa mengakses //

        $series = $this->seriesModel->findById($id); //

        if (!$series) { //
            redirect('/daftar_series?message=' . urlencode('Series tidak ditemukan.') . '&type=error'); //
        }

        $message = ''; //
        $messageType = ''; //

        if ($this->seriesModel->delete($id)) { //
            $message = 'Series berhasil dihapus!'; //
            $messageType = 'success'; //
        } else {
            $message = 'Gagal menghapus series.'; //
            $messageType = 'error'; //
        }
        redirect('/daftar_series?message=' . urlencode($message) . '&type=' . urlencode($messageType)); //
    }
}