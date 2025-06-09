<?php
// niflix_project/app/Controllers/KomentarRatingController.php

require_once APP_ROOT . '/app/Core/Session.php';
require_once APP_ROOT . '/app/Core/Functions.php';
require_once APP_ROOT . '/app/Models/Film.php';
require_once APP_ROOT . '/app/Models/Series.php';
require_once APP_ROOT . '/app/Models/CommentRating.php'; // Updated/Renamed model

class KomentarRatingController {
    private $pdo;
    private $filmModel;
    private $seriesModel;
    private $commentRatingModel; // Renamed property

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->filmModel = new Film($pdo);
        $this->seriesModel = new Series($pdo);
        $this->commentRatingModel = new CommentRating($pdo); // Instantiate new model

        // Ensure user is logged in for most actions on this page
        if (!Session::has('user')) {
            redirect('/auth/login?message=' . urlencode('Anda harus login untuk mengakses halaman Komentar & Rating.') . '&type=error');
        }
    }

    /**
     * Displays a list of films and series with their ratings and comments.
     */
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

    /**
     * Displays the detail page for a specific film or series, including all ratings and comments.
     * @param string $itemType 'film' or 'series'
     * @param int $itemId
     */
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
            // Invalid item type for this controller's detail view
            redirect('/dashboard?message=' . urlencode('Tipe item tidak didukung di sini.') . '&type=error');
        }

        if (!$item) {
            redirect('/komentar_rating?message=' . urlencode('Film/Series tidak ditemukan.') . '&type=error');
        }

        // Get comments for the item (film or series)
        $comments = $this->commentRatingModel->getCommentsByItem($itemId, $itemType);

        $message = null;
        $messageType = null;

        // ... (rest of your POST handling logic)

        // Get messages from URL parameters for initial load
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
            'pdo' => $this->pdo // <--- ADD THIS LINE
        ]);
    }

    /**
     * Deletes a rating.
     * @param string $itemType 'film' or 'series'
     * @param int $ratingId ID of the rating to delete
     */
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