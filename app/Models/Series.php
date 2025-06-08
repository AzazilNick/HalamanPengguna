<?php
// niflix_project/app/Models/Series.php

class Series {
    private $pdo;
    private $table = 'series';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil semua series.
     * @return array
     */
    public function getAllSeries() {
        // Modified to include mock counts for demonstration.
        // In a real application, you would join with actual likes, watched, and comment tables.
        $stmt = $this->pdo->prepare("
            SELECT
                s.*,
                COALESCE(rf_count.total_reviews, 0) AS film_reviews_count,
                COALESCE(rs_count.total_reviews, 0) AS series_reviews_count
            FROM
                {$this->table} s
            LEFT JOIN (
                SELECT film_id, COUNT(*) as total_reviews FROM review_films GROUP BY film_id
            ) rf_count ON s.id = rf_count.film_id
            LEFT JOIN (
                SELECT series_id, COUNT(*) as total_reviews FROM review_series GROUP BY series_id
            ) rs_count ON s.id = rs_count.series_id
            ORDER BY s.id ASC
        ");
        $stmt->execute();
        $series = $stmt->fetchAll();

        // Add mock likes and watched counts for demonstration
        foreach ($series as &$s) {
            $s['likes_count'] = rand(100, 1000); // Mock data
            $s['watched_count'] = rand(500, 5000); // Mock data
            // Use existing comment counts from reviews_film or review_series.
            // Adjust this logic based on which table truly represents "comments" for series.
            $s['comments_count'] = $s['series_reviews_count'];
            unset($s['film_reviews_count']); // Remove if not directly relevant to series comments
            unset($s['series_reviews_count']); // Remove if not directly relevant to series comments
        }
        return $series;
    }

    /**
     * Menemukan series berdasarkan ID.
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Membuat series baru.
     * @param string $title
     * @param string $description
     * @param int $releaseYear
     * @param string $imageUrl
     * @param int $is_popular
     * @return bool
     */
    public function create($title, $description, $releaseYear, $imageUrl = null, $is_popular) {
        // Added 'is_popular' to the column list and ':is_popular' to the values list
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (title, description, release_year, image_url, is_popular) VALUES (:title, :description, :release_year, :image_url, :is_popular)");
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':release_year' => $releaseYear,
            ':image_url' => $imageUrl,
            ':is_popular' => $is_popular // Added this binding
        ]);
    }

    /**
     * Memperbarui series.
     * @param int $id
     * @param string $title
     * @param string $description
     * @param int $releaseYear
     * @param string $imageUrl
     * @param int $is_popular
     * @return bool
     */
    public function update($id, $title, $description, $releaseYear, $imageUrl = null, $is_popular) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET title = :title, description = :description, release_year = :release_year, image_url = :image_url, is_popular = :is_popular WHERE id = :id");
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':release_year' => $releaseYear,
            ':image_url' => $imageUrl,
            ':is_popular' => $is_popular,
            ':id' => $id
        ]);
    }

    /**
     * Menghapus series.
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Mengambil series berdasarkan is_popular status.
     * @return array
     */
    public function getSeriesByIdRange() {
        // Modified to include mock counts for demonstration.
        $stmt = $this->pdo->prepare("
            SELECT
                s.*,
                COALESCE(rf_count.total_reviews, 0) AS film_reviews_count,
                COALESCE(rs_count.total_reviews, 0) AS series_reviews_count
            FROM
                {$this->table} s
            LEFT JOIN (
                SELECT film_id, COUNT(*) as total_reviews FROM review_films GROUP BY film_id
            ) rf_count ON s.id = rf_count.film_id
            LEFT JOIN (
                SELECT series_id, COUNT(*) as total_reviews FROM review_series GROUP BY series_id
            ) rs_count ON s.id = rs_count.series_id
            WHERE s.is_popular = 1
            ORDER BY s.id ASC
        ");
        $stmt->execute();
        $popularSeries = $stmt->fetchAll();

        // Add mock likes and watched counts for demonstration
        foreach ($popularSeries as &$s) {
            $s['likes_count'] = rand(100, 1000); // Mock data
            $s['watched_count'] = rand(500, 5000); // Mock data
            $s['comments_count'] = $s['series_reviews_count'];
            unset($s['film_reviews_count']);
            unset($s['series_reviews_count']);
        }
        return $popularSeries;
    }
}
