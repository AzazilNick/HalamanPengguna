<?php
// niflix_project/app/Models/Comment.php

class Comment {
    private $pdo;
    private $table = 'comments';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Membuat komentar baru pada sebuah review.
     * @param int $reviewId ID dari review yang dikomentari.
     * @param int $userId ID pengguna yang berkomentar.
     * @param string $content Isi komentar.
     * @param int|null $parentId ID dari komentar induk (jika ini adalah balasan).
     * @return bool True jika berhasil, false jika gagal.
     */
    public function create($reviewId, $userId, $content, $parentId = null) {
        $sql = "INSERT INTO {$this->table} (review_id, user_id, content, parent_id) VALUES (:review_id, :user_id, :content, :parent_id)";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':review_id' => $reviewId,
            ':user_id' => $userId,
            ':content' => $content,
            ':parent_id' => $parentId
        ]);
    }

    /**
     * Mengambil semua komentar untuk sebuah review.
     * @param int $reviewId ID dari review.
     * @return array Daftar komentar.
     */
    public function getCommentsForReview($reviewId) {
        $sql = "SELECT c.*, u.username as commenter_username, u.foto_pengguna as commenter_photo
                FROM {$this->table} c
                JOIN user u ON c.user_id = u.id
                WHERE c.review_id = :review_id
                ORDER BY c.created_at ASC"; // Diurutkan dari yang paling lama agar urutan diskusi benar
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':review_id' => $reviewId]);
        
        // Mengelompokkan komentar berdasarkan parent_id untuk memudahkan pembuatan thread
        $comments = [];
        while ($row = $stmt->fetch()) {
            $comments[$row['parent_id'] ?? 0][] = $row;
        }
        return $comments;
    }

    /**
     * Menghapus sebuah komentar berdasarkan ID.
     * @param int $id ID komentar.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}