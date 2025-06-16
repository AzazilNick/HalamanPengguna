<?php
// niflix_project/app/Controllers/CommentRatingController.php

require_once APP_ROOT . '/app/Core/Session.php';
require_once APP_ROOT . '/app/Core/Functions.php';
require_once APP_ROOT . '/app/Models/Film.php';
require_once APP_ROOT . '/app/Models/Series.php';
require_once APP_ROOT . '/app/Models/CommentRating.php';
require_once APP_ROOT . '/app/Models/User.php';

class CommentRatingController {
    private $pdo;
    private $filmModel;
    private $seriesModel;
    private $commentRatingModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->filmModel = new Film($pdo);
        $this->seriesModel = new Series($pdo);
        $this->commentRatingModel = new CommentRating($pdo);

        // Hanya terapkan check login jika bukan AJAX request dan bukan action untuk AJAX like/delete
        // Ini adalah perubahan penting untuk menghindari redirect otomatis saat AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        $currentAction = debug_backtrace()[1]['function'] ?? ''; // Get the calling function name

        // Periksa apakah action saat ini adalah detail atau deleteEntry (yang mungkin dipanggil dari article controller untuk delete)
        // atau toggleLike, yang sudah menangani redirect sendiri jika tidak login
        if (!$isAjax && !in_array($currentAction, ['detail', 'deleteEntry', 'toggleLikeAjax'])) {
             if (!Session::has('user')) {
                redirect('/auth/login?message=' . urlencode('Anda harus login untuk mengakses halaman Komentar & Rating.') . '&type=error');
            }
        }
    }

    public function index() {
        // Pastikan pengguna sudah login
        if (!Session::has('user')) {
            redirect('/auth/login');
        }

        $films = $this->filmModel->getAllFilms();
        $series = $this->seriesModel->getAllSeries();

        $message = $_GET['message'] ?? Session::getFlash('message');
        $messageType = $_GET['type'] ?? Session::getFlash('message_type');

        view('komentar_rating/index', [
            'films' => $films,
            'series' => $series,
            'title' => 'Komentar & Rating',
            'message' => $message,
            'message_type' => $messageType
        ]);
    }

    /**
     * Menampilkan detail item tunggal dan komentar-komentarnya, atau memproses submission.
     * @param string $itemType Tipe item ('film', 'series')
     * @param int $itemId ID item
     */
    public function detail($itemType, $itemId) {
        if (!Session::has('user')) {
            redirect('/auth/login');
        }

        $item = null;
        $allEntries = [];
        $userReviewEntry = null;
        $isLiked = false;
        $currentUserId = Session::get('user')['id'];

        if ($itemType === 'film') {
            $item = $this->filmModel->findById($itemId);
            if ($item) {
                $allEntries = $this->commentRatingModel->getAllEntriesByItem($itemId, 'film');
                $userReviewEntry = $this->commentRatingModel->findUserRating($currentUserId, $itemId, 'film');
                $isLiked = $this->filmModel->hasUserLiked($currentUserId, $itemId);
            }
        } elseif ($itemType === 'series') {
            $item = $this->seriesModel->findById($itemId);
            if ($item) {
                $allEntries = $this->commentRatingModel->getAllEntriesByItem($itemId, 'series');
                $userReviewEntry = $this->commentRatingModel->findUserRating($currentUserId, $itemId, 'series');
                $isLiked = $this->seriesModel->hasUserLiked($currentUserId, $itemId);
            }
        } else {
            redirect('/dashboard?message=' . urlencode('Tipe item tidak didukung di sini.') . '&type=error');
        }

        if (!$item) {
            redirect('/komentar_rating?message=' . urlencode('Film/Series tidak ditemukan.') . '&type=error');
        }

        $message = null;
        $messageType = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'submit_comment_review') {
                $commentText = trim($_POST['comment_text'] ?? '');
                $ratingValue = filter_var($_POST['rating_value'] ?? null, FILTER_VALIDATE_INT);
                $userId = Session::get('user')['id'];
                $parentCommentId = filter_var($_POST['parent_comment_id'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

                if (empty($commentText) && ($ratingValue === false || $ratingValue < 1 || $ratingValue > 10)) {
                    $message = 'Komentar atau rating harus diisi, dan rating harus antara 1-10!';
                    $messageType = 'error';
                } else if (!empty($commentText) && ($ratingValue !== false && ($ratingValue < 1 || $ratingValue > 10))) {
                     $message = 'Rating harus antara 1-10!';
                     $messageType = 'error';
                } else {
                    if ($ratingValue !== null && $parentCommentId === null) {
                         $existingUserRating = $this->commentRatingModel->findUserRating($userId, $itemId, $itemType);
                        if ($existingUserRating) {
                            if ($this->commentRatingModel->updateEntry($existingUserRating['id'], $commentText, $ratingValue)) {
                                $message = 'Ulasan dan rating berhasil diperbarui!';
                                $messageType = 'success';
                            } else {
                                $message = 'Gagal memperbarui ulasan dan rating.';
                                $messageType = 'error';
                            }
                        } else {
                            if ($this->commentRatingModel->addEntry($itemId, $itemType, $userId, $commentText, null, $ratingValue)) {
                                $message = 'Ulasan dan rating berhasil ditambahkan!';
                                $messageType = 'success';
                            } else {
                                $message = 'Gagal menambahkan ulasan dan rating.';
                                $messageType = 'error';
                            }
                        }
                    } else {
                        if ($this->commentRatingModel->addEntry($itemId, $itemType, $userId, $commentText, $parentCommentId, null)) {
                            $message = 'Komentar berhasil ditambahkan.';
                            $messageType = 'success';
                        } else {
                            $message = 'Gagal menambahkan komentar.';
                            $messageType = 'error';
                        }
                    }
                }
                redirect('/komentar_rating/detail/' . $itemType . '/' . $itemId . '?message=' . urlencode($message) . '&type=' . urlencode($messageType));
                exit();

            } elseif ($action === 'toggle_like') {
                if ($itemType === 'film') {
                    $this->filmModel->toggleLike($currentUserId, $itemId);
                } elseif ($itemType === 'series') {
                    $this->seriesModel->toggleLike($currentUserId, $itemId);
                }
                redirect('/komentar_rating/detail/' . $itemType . '/' . $itemId);
                exit();
            } elseif ($action === 'toggle_comment_like') {
                $commentId = filter_var($_POST['comment_id'] ?? null, FILTER_VALIDATE_INT);
                if ($commentId) {
                    if ($this->commentRatingModel->toggleLike($currentUserId, $commentId)) {
                        // Success, no specific message needed, just refresh
                    } else {
                        // Error toggling like, handle as needed
                    }
                }
                redirect('/komentar_rating/detail/' . $itemType . '/' . $itemId);
                exit();
            }
        }

        $allEntries = $this->commentRatingModel->getAllEntriesByItem($itemId, $itemType);
        $userReviewEntry = $this->commentRatingModel->findUserRating($currentUserId, $itemId, $itemType);

        if (isset($_GET['message'])) {
            $message = $_GET['message'];
            $messageType = $_GET['type'] ?? 'info';
        }


        view('komentar_rating/detail', [
            'item' => $item,
            'item_type' => $itemType,
            'allEntries' => $allEntries,
            'title' => $item['title'],
            'message' => $message,
            'message_type' => $messageType,
            'userReviewEntry' => $userReviewEntry,
            'isLiked' => $isLiked,
            'pdo' => $this->pdo
        ]);
    }

    /**
     * Menambahkan komentar/ulasan baru melalui AJAX.
     * Digunakan oleh ArticleController dan KomentarRatingController.
     */
    public function addCommentAjax() {
        header('Content-Type: application/json');

        if (!Session::has('user')) {
            echo json_encode(['success' => false, 'message' => 'Anda harus login untuk menambahkan komentar.', 'redirect' => BASE_URL . '/auth/login']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
            exit();
        }

        $itemId = filter_var($_POST['item_id'] ?? null, FILTER_VALIDATE_INT);
        $itemType = $_POST['item_type'] ?? '';
        $commentText = trim($_POST['comment_text'] ?? '');
        $ratingValue = filter_var($_POST['rating_value'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $parentCommentId = filter_var($_POST['parent_comment_id'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $userId = Session::get('user')['id'];

        if (empty($itemId) || empty($itemType) || (empty($commentText) && $ratingValue === null)) {
            echo json_encode(['success' => false, 'message' => 'Data komentar tidak lengkap.']);
            exit();
        }

        // Validate item_type (ensure it's one of 'film', 'series', 'article')
        $allowedItemTypes = ['film', 'series', 'article'];
        if (!in_array($itemType, $allowedItemTypes)) {
            echo json_encode(['success' => false, 'message' => 'Tipe item tidak valid.']);
            exit();
        }

        // Check if a review (rating + optional comment) already exists for this user and item.
        // This logic applies only for top-level entries (parent_comment_id is null)
        if ($ratingValue !== null && $parentCommentId === null) {
            $existingReview = $this->commentRatingModel->findUserRating($userId, $itemId, $itemType);
            if ($existingReview) {
                // Update existing review
                if ($this->commentRatingModel->updateEntry($existingReview['id'], $commentText, $ratingValue)) {
                    $updatedComment = $this->commentRatingModel->findById($existingReview['id']);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Ulasan berhasil diperbarui.',
                        'comment' => $updatedComment, // Return updated comment data
                        'action_type' => 'update',
                        'is_new_review' => false,
                        'new_total_comments_ratings' => $this->commentRatingModel->getTotalCommentsRatings($itemId, $itemType),
                        'new_average_rating' => $this->commentRatingModel->getAverageRating($itemId, $itemType)
                    ]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui ulasan.']);
                    exit();
                }
            }
        }

        // Add new comment or review
        if ($this->commentRatingModel->addEntry($itemId, $itemType, $userId, $commentText, $parentCommentId, $ratingValue)) {
            $newCommentId = $this->pdo->lastInsertId();
            $newComment = $this->commentRatingModel->findById($newCommentId);
            if ($newComment) {
                 // Fetch user photo for the new comment
                $userModel = new User($this->pdo); // Instantiate User model
                $commenterUser = $userModel->findById($newComment['user_id']);
                $newComment['commenter_photo'] = $commenterUser['foto_pengguna'] ?? 'default.png';
                $newComment['commenter_username'] = $commenterUser['username'];

                echo json_encode([
                    'success' => true,
                    'message' => 'Komentar berhasil ditambahkan.',
                    'comment' => $newComment,
                    'action_type' => 'add',
                    'is_new_review' => ($ratingValue !== null),
                    'new_total_comments_ratings' => $this->commentRatingModel->getTotalCommentsRatings($itemId, $itemType),
                    'new_average_rating' => $this->commentRatingModel->getAverageRating($itemId, $itemType)
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Komentar berhasil ditambahkan, tetapi gagal mengambil detailnya.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan komentar.']);
        }
        exit();
    }

    public function deleteEntry($entryId) {
        if (!Session::has('user')) {
            redirect('/auth/login?message=' . urlencode('Anda harus login untuk menghapus entri ini.') . '&type=error');
        }

        $entry = $this->commentRatingModel->findById($entryId);

        if (!$entry) {
            redirect('/komentar_rating?message=' . urlencode('Entri tidak ditemukan.') . '&type=error');
        }

        $currentUser = Session::get('user');
        if ($currentUser['id'] != $entry['user_id'] && $currentUser['is_admin'] != 1) {
            redirect('/komentar_rating/detail/' . $entry['item_type'] . '/' . $entry['item_id'] . '?message=' . urlencode('Anda tidak memiliki izin untuk menghapus entri ini.') . '&type=error');
        }

        $message = '';
        $messageType = '';

        $redirectItemType = $entry['item_type'];
        $redirectItemId = $entry['item_id'];

        if ($this->commentRatingModel->delete($entryId)) {
            $message = 'Entri berhasil dihapus!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menghapus entri.';
            $messageType = 'error';
        }

        if ($redirectItemType === 'article') {
            redirect('/articles/show/' . $redirectItemId . '?message=' . urlencode($message) . '&type=' . urlencode($messageType));
        } else {
            redirect('/komentar_rating/detail/' . $redirectItemType . '/' . $redirectItemId . '?message=' . urlencode($message) . '&type=' . urlencode($messageType));
        }
    }
}
