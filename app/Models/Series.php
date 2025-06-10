<?php
// niflix_project/app/Models/Series.php

class Series {
    private $pdo;
    private $table = 'series';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil semua series dengan rata-rata rating (dari comments_rating), jumlah komentar, dan jumlah suka.
     * @return array
     */
    public function getAllSeries() {
        $stmt = $this->pdo->prepare("
            SELECT
                s.*,
                COALESCE(AVG(cr.rating_value), 0) AS average_rating,
                SUM(CASE WHEN cr.item_type = 'series' AND cr.rating_value IS NOT NULL THEN 1 ELSE 0 END) AS total_comments_ratings,
                COALESCE(SUM(CASE WHEN l.item_type = 'series' THEN 1 ELSE 0 END), 0) AS total_likes
            FROM
                {$this->table} s
            LEFT JOIN
                comments_rating cr ON s.id = cr.item_id AND cr.item_type = 'series'
            LEFT JOIN
                likes l ON s.id = l.item_id AND l.item_type = 'series'
            GROUP BY
                s.id
            ORDER BY s.id ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menemukan series berdasarkan ID dengan rata-rata rating (dari comments_rating), jumlah komentar, dan jumlah suka.
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("
            SELECT
                s.*,
                COALESCE(AVG(cr.rating_value), 0) AS average_rating,
                SUM(CASE WHEN cr.item_type = 'series' AND cr.rating_value IS NOT NULL THEN 1 ELSE 0 END) AS total_comments_ratings,
                COALESCE(SUM(CASE WHEN l.item_type = 'series' THEN 1 ELSE 0 END), 0) AS total_likes
            FROM
                {$this->table} s
            LEFT JOIN
                comments_rating cr ON s.id = cr.item_id AND cr.item_type = 'series'
            LEFT JOIN
                likes l ON s.id = l.item_id AND l.item_type = 'series'
            WHERE
                s.id = :id
            GROUP BY
                s.id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Menambahkan/menghapus like pada series.
     * @param int $userId
     * @param int $seriesId
     * @return bool True if successful, false otherwise.
     */
    public function toggleLike($userId, $seriesId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = :user_id AND item_id = :item_id AND item_type = 'series'");
        $stmt->execute([':user_id' => $userId, ':item_id' => $seriesId]);
        $isLiked = $stmt->fetchColumn();

        if ($isLiked) {
            // Unlike
            $stmt = $this->pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND item_id = :item_id AND item_type = 'series'");
        } else {
            // Like
            $stmt = $this->pdo->prepare("INSERT INTO likes (user_id, item_id, item_type) VALUES (:user_id, :item_id, 'series')");
        }
        return $stmt->execute([':user_id' => $userId, ':item_id' => $seriesId]);
    }

    /**
     * Mengecek apakah pengguna sudah menyukai series tertentu.
     * @param int $userId
     * @param int $seriesId
     * @return bool
     */
    public function hasUserLiked($userId, $seriesId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = :user_id AND item_id = :item_id AND item_type = 'series'");
        $stmt->execute([':user_id' => $userId, ':item_id' => $seriesId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Mengambil series berdasarkan is_popular status.
     * @return array
     */
    public function getSeriesByIdRange() {
        $stmt = $this->pdo->prepare("
            SELECT
                s.*,
                COALESCE(AVG(cr.rating_value), 0) AS average_rating,
                SUM(CASE WHEN cr.item_type = 'series' AND cr.rating_value IS NOT NULL THEN 1 ELSE 0 END) AS total_comments_ratings,
                COALESCE(SUM(CASE WHEN l.item_type = 'series' THEN 1 ELSE 0 END), 0) AS total_likes
            FROM
                {$this->table} s
            LEFT JOIN
                comments_rating cr ON s.id = cr.item_id AND cr.item_type = 'series'
            LEFT JOIN
                likes l ON s.id = l.item_id AND l.item_type = 'series'
            WHERE s.is_popular = 1
            GROUP BY
                s.id
            ORDER BY s.id ASC
        ");
        $stmt->execute();
        $popularSeries = $stmt->fetchAll();
        return $popularSeries;
    }
}