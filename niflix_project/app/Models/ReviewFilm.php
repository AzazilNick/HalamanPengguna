<?php
// niflix_project/app/Models/ReviewFilm.php

class ReviewFilm {
    private $pdo; // Tambahkan properti pdo
    protected $table = 'reviews_film'; // Ubah static menjadi non-static jika model akan diinstansiasi

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo; // Inisialisasi pdo di konstruktor
    }

    public function create($data) {
        // Gunakan $this->pdo, bukan static::getDB()
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table} (film_id, user_id, rating, review_text)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['film_id'],
            $data['user_id'],
            $data['rating'], // Pastikan rating disertakan
            $data['review_text']
        ]);
    }

    public function all() {
        // Gunakan $this->pdo, bukan static::getDB()
        $stmt = $this->pdo->query("
            SELECT fr.*, f.title AS film_title, u.username 
            FROM {$this->table} fr
            JOIN films f ON fr.film_id = f.id
            JOIN user u ON fr.user_id = u.id
            ORDER BY fr.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function find($id) {
        // Gunakan $this->pdo, bukan static::getDB()
        $stmt = $this->pdo->prepare("
            SELECT fr.*, f.title AS film_title, u.username 
            FROM {$this->table} fr
            JOIN films f ON fr.film_id = f.id
            JOIN user u ON fr.user_id = u.id
            WHERE fr.id = ?
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