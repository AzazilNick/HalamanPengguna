<?php
// niflix_project/app/Models/Rating.php

class Rating {
    private $pdo;
    private $table = 'ratings';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Membuat atau memperbarui rating dari seorang user untuk sebuah item.
     * Menggunakan ON DUPLICATE KEY UPDATE agar user bisa mengubah ratingnya.
     * Catatan: Ini memerlukan UNIQUE KEY pada (user_id, rateable_id, rateable_type) di tabel Anda.
     *
     * @param int $userId ID pengguna.
     * @param int $rateableId ID item (film/series).
     * @param string $rateableType Tipe item ('film' atau 'series').
     * @param int $value Nilai rating.
     * @return bool
     */
    public function createOrUpdate($userId, $rateableId, $rateableType, $value) {
        $sql = "
            INSERT INTO {$this->table} (user_id, rateable_id, rateable_type, value)
            VALUES (:user_id, :rateable_id, :rateable_type, :value)
            ON DUPLICATE KEY UPDATE value = VALUES(value)
        ";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':user_id' => $userId,
            ':rateable_id' => $rateableId,
            ':rateable_type' => $rateableType,
            ':value' => $value
        ]);
    }

    /**
     * Mendapatkan rata-rata rating untuk sebuah item.
     * @param int $rateableId ID item.
     * @param string $rateableType Tipe item.
     * @return float Rata-rata rating.
     */
    public function getAverageRatingFor($rateableId, $rateableType) {
        $sql = "SELECT AVG(value) as average_rating FROM {$this->table} WHERE rateable_id = ? AND rateable_type = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$rateableId, $rateableType]);
        $result = $stmt->fetch();
        return $result ? round($result['average_rating'] ?? 0, 1) : 0;
    }
}