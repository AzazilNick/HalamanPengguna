<?php
// niflix_project/app/Models/ReviewSeries.php

class ReviewSeries {
    private $pdo;
    protected $table = 'review_series'; // Pastikan ini nama tabel yang benar di database

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table} (series_id, user_id, review_text)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([
            $data['series_id'],
            $data['user_id'],
            $data['review_text']
        ]);
    }

    public function all() {
        $stmt = $this->pdo->query("
            SELECT rs.*, s.title AS series_title, u.username 
            FROM {$this->table} rs
            JOIN series s ON rs.series_id = s.id
            JOIN user u ON rs.user_id = u.id
            ORDER BY rs.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("
            SELECT rs.*, s.title AS series_title, u.username 
            FROM {$this->table} rs
            JOIN series s ON rs.series_id = s.id
            JOIN user u ON rs.user_id = u.id
            WHERE rs.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $rating, $reviewText) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET rating = ?, review_text = ? WHERE id = ?");
        return $stmt->execute([$rating, $reviewText, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}