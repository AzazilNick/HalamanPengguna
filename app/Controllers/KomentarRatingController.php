<?php
// niflix_project/app/Controllers/KomentarRatingController.php

require_once APP_ROOT . '/app/Core/Session.php';
require_once APP_ROOT . '/app/Core/Functions.php';
require_once APP_ROOT . '/app/Models/Film.php';
require_once APP_ROOT . '/app/Models/Series.php';
require_once APP_ROOT . '/app/Models/CommentRating.php';

class KomentarRatingController {
    private $pdo;
    private $filmModel;
    private $seriesModel;
    private $commentRatingModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->filmModel = new Film($pdo);
        $this->seriesModel = new Series($pdo);
        $this->commentRatingModel = new CommentRating($pdo);

        if (!Session::has('user')) {
            redirect('/auth/login?message=' . urlencode('Anda harus login untuk mengakses halaman Komentar & Rating.') . '&type=error');
        }
    }

    public function index() {
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

    public function detail($itemType, $itemId) {
        $item = null;
        $ratings = [];
        $comments = [];
        $userRating = null;
        $isLiked = false;
        $currentUserId = Session::get('user')['id'];

        if ($itemType === 'film') {
            $item = $this->filmModel->findById($itemId);
            if ($item) {
                $ratings = $this->commentRatingModel->getRatingsByItem($itemId, 'film');
                $userRating = $this->commentRatingModel->findUserRating($currentUserId, $itemId, 'film');
                $isLiked = $this->filmModel->hasUserLiked($currentUserId, $itemId);
            }
        } elseif ($itemType === 'series') {
            $item = $this->seriesModel->findById($itemId);
            if ($item) {
                $ratings = $this->commentRatingModel->getRatingsByItem($itemId, 'series');
                $userRating = $this->commentRatingModel->findUserRating($currentUserId, $itemId, 'series');
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

            if ($action === 'submit_comment') { // For general comments
                $commentText = trim($_POST['comment_text'] ?? '');
                $userId = Session::get('user')['id'];
                $parentCommentId = filter_var($_POST['parent_comment_id'] ?? null, FILTER_VALIDATE_INT);
                if ($parentCommentId === false) {
                    $parentCommentId = null;
                }

                if (empty($commentText)) {
                    $message = 'Komentar tidak boleh kosong!';
                    $messageType = 'error';
                } else {
                    if ($this->commentRatingModel->addEntry($itemId, $itemType, $userId, $commentText, $parentCommentId, null)) { // null for rating_value
                        $message = 'Komentar berhasil ditambahkan.';
                        $messageType = 'success';
                    } else {
                        $message = 'Gagal menambahkan komentar.';
                        $messageType = 'error';
                    }
                }
                redirect('/komentar_rating/detail/' . $itemType . '/' . $itemId . '?message=' . urlencode($message) . '&type=' . urlencode($messageType));
                exit();
            } elseif ($action === 'submit_review_rating') { // Handle new review/rating submission
                $commentText = trim($_POST['comment_text'] ?? '');
                $ratingValue = filter_var($_POST['rating_value'] ?? null, FILTER_VALIDATE_INT);
                $userId = Session::get('user')['id'];

                if ($ratingValue === false || $ratingValue < 1 || $ratingValue > 10) {
                    $message = 'Ulasan dan rating tidak boleh kosong dan rating harus antara 1-10!';
                    $messageType = 'error';
                } else {
                    if ($userRating) { // If user has already reviewed, update it
                        if ($this->commentRatingModel->updateEntry($userRating['id'], $commentText, $ratingValue)) {
                            $message = 'Ulasan dan rating berhasil diperbarui!';
                            $messageType = 'success';
                        } else {
                            $message = 'Gagal memperbarui ulasan dan rating.';
                            $messageType = 'error';
                        }
                    } else { // Otherwise, create a new entry
                        if ($this->commentRatingModel->addEntry($itemId, $itemType, $userId, $commentText, null, $ratingValue)) { // null for parent_comment_id
                            $message = 'Ulasan dan rating berhasil ditambahkan!';
                            $messageType = 'success';
                        } else {
                            $message = 'Gagal menambahkan ulasan dan rating.';
                            $messageType = 'error';
                        }
                    }
                }
                redirect('/komentar_rating/detail/' . $itemType . '/' . $itemId . '?message=' . urlencode($message) . '&type=' . urlencode($messageType));
                exit();
            } elseif ($action === 'toggle_like') {
                // This logic is already present and seems correct for item likes
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

        // Re-fetch comments and ratings after potential submission to display the new ones
        $comments = $this->commentRatingModel->getCommentsByItem($itemId, $itemType, true); // Only get pure comments
        $ratings = $this->commentRatingModel->getRatingsByItem($itemId, $itemType);
        $userRating = $this->commentRatingModel->findUserRating($currentUserId, $itemId, $itemType); // Re-fetch user's rating

        if (isset($_GET['message'])) {
            $message = $_GET['message'];
            $messageType = $_GET['type'] ?? 'info';
        }

        view('komentar_rating/detail', [
            'item' => $item,
            'item_type' => $itemType,
            'ratings' => $ratings,
            'comments' => $comments,
            'title' => $item['title'],
            'message' => $message,
            'message_type' => $messageType,
            'userRating' => $userRating,
            'isLiked' => $isLiked,
            'pdo' => $this->pdo
        ]);
    }

    public function deleteRating($itemType, $ratingId) {
        if (!Session::has('user')) {
            redirect('/auth/login?message=' . urlencode('Anda harus login untuk menghapus rating.') . '&type=error');
        }

        $rating = $this->commentRatingModel->findById($ratingId);

        if (!$rating || $rating['rating_value'] === null) { // Ensure it's a rating, not a comment
            redirect('/komentar_rating?message=' . urlencode('Rating tidak ditemukan.') . '&type=error');
        }

        $currentUser = Session::get('user');
        if ($currentUser['id'] != $rating['user_id'] && $currentUser['is_admin'] != 1) {
            redirect('/komentar_rating/detail/' . $itemType . '/' . $rating['item_id'] . '?message=' . urlencode('Anda tidak memiliki izin untuk menghapus rating ini.') . '&type=error');
        }

        $message = '';
        $messageType = '';
        $success = false;

        $success = $this->commentRatingModel->delete($ratingId);

        if ($success) {
            $message = 'Rating berhasil dihapus!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menghapus rating.';
            $messageType = 'error';
        }

        redirect('/komentar_rating/detail/' . $itemType . '/' . $rating['item_id'] . '?message=' . urlencode($message) . '&type=' . urlencode($messageType));
    }

    /**
     * Deletes a comment.
     * @param int $commentId ID of the comment to delete
     */
    public function deleteComment($commentId) {
        if (!Session::has('user')) {
            redirect('/auth/login?message=' . urlencode('Anda harus login untuk menghapus komentar.') . '&type=error');
        }

        $comment = $this->commentRatingModel->findById($commentId);

        if (!$comment || $comment['rating_value'] !== null) { // Ensure it's a comment, not a rating
            redirect('/komentar_rating?message=' . urlencode('Komentar tidak ditemukan.') . '&type=error');
        }

        $currentUser = Session::get('user');
        // Only comment author, or admin can delete
        if ($currentUser['id'] != $comment['user_id'] && $currentUser['is_admin'] != 1) {
            redirect('/komentar_rating/detail/' . $comment['item_type'] . '/' . $comment['item_id'] . '?message=' . urlencode('Anda tidak memiliki izin untuk menghapus komentar ini.') . '&type=error');
        }

        $message = '';
        $messageType = '';

        if ($this->commentRatingModel->delete($commentId)) {
            $message = 'Komentar berhasil dihapus!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menghapus komentar.';
            $messageType = 'error';
        }

        redirect('/komentar_rating/detail/' . $comment['item_type'] . '/' . $comment['item_id'] . '?message=' . urlencode($message) . '&type=' . urlencode($messageType));
    }
}