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
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} ORDER BY title ASC");
        $stmt->execute();
        return $stmt->fetchAll();
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
     * @return bool
     */
    public function create($title, $description, $releaseYear, $imageUrl = null) {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (title, description, release_year, image_url) VALUES (:title, :description, :release_year, :image_url)");
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':release_year' => $releaseYear,
            ':image_url' => $imageUrl
        ]);
    }

    /**
     * Memperbarui series.
     * @param int $id
     * @param string $title
     * @param string $description
     * @param int $releaseYear
     * @param string $imageUrl
     * @return bool
     */
    public function update($id, $title, $description, $releaseYear, $imageUrl = null) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET title = :title, description = :description, release_year = :release_year, image_url = :image_url WHERE id = :id");
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':release_year' => $releaseYear,
            ':image_url' => $imageUrl,
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
     * Mengambil series berdasarkan rentang ID.
     * @param int $startId
     * @param int $endId
     * @return array
     */
    public function getSeriesByIdRange($startId, $endId) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id BETWEEN :start_id AND :end_id ORDER BY id ASC");
        $stmt->bindParam(':start_id', $startId, PDO::PARAM_INT);
        $stmt->bindParam(':end_id', $endId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}