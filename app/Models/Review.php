<?php
// niflix_project/app/Models/Review.php

class Review {
    private $pdo;
    private $table = 'reviews';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Membuat review baru.
     * @param int $userId ID pengguna yang menulis review.
     * @param int $reviewableId ID dari item yang direview (film atau series).
     * @param string $reviewableType Tipe item ('film' atau 'series').
     * @param string $title Judul review.
     * @param string $content Isi dari review.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function create($userId, $reviewableId, $reviewableType, $title, $content) {
        $sql = "INSERT INTO {$this->table} (user_id, reviewable_id, reviewable_type, title, content) VALUES (:user_id, :reviewable_id, :reviewable_type, :title, :content)";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':user_id' => $userId,
            ':reviewable_id' => $reviewableId,
            ':reviewable_type' => $reviewableType,
            ':title' => $title,
            ':content' => $content
        ]);
    }

    /**
     * Menemukan review berdasarkan ID-nya, lengkap dengan data penulis.
     * @param int $id ID review.
     * @return array|false Data review atau false jika tidak ditemukan.
     */
    public function findById($id) {
        $sql = "SELECT r.*, u.username as author_username, u.nama_lengkap as author_fullname
                FROM {$this->table} r
                JOIN user u ON r.user_id = u.id
                WHERE r.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Mengambil semua review untuk sebuah item spesifik (film atau series).
     * @param int $reviewableId ID dari item.
     * @param string $reviewableType Tipe item ('film' atau 'series').
     * @return array Daftar review.
     */

    // ... di dalam class Review ...
        /**
     * Mengambil semua review (film & series) lengkap dengan judul media dan penulis.
     * @return array Daftar semua review.
     */
    public function getAllReviewsWithMediaTitle() {
        $sql = "
            SELECT 
                r.id, 
                r.title, 
                r.content, 
                r.created_at, 
                r.reviewable_type,
                r.reviewable_id, -- Kita butuh ini untuk membuat link
                u.username AS author_username,
                u.nama_lengkap AS author_fullname,
                -- Menggunakan COALESCE untuk mengambil judul dari tabel yang relevan
                COALESCE(f.title, s.title) AS media_title
            FROM 
                {$this->table} r
            JOIN 
                user u ON r.user_id = u.id
            LEFT JOIN 
                films f ON r.reviewable_id = f.id AND r.reviewable_type = 'film'
            LEFT JOIN 
                series s ON r.reviewable_id = s.id AND r.reviewable_type = 'series'
            ORDER BY 
                r.created_at DESC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menghapus sebuah review berdasarkan ID.
     * @param int $id ID review yang akan dihapus.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}