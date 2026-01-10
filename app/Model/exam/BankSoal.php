<?php
namespace App\Model\Exam;

use App\Core\Model;
use PDO;

class BankSoal extends Model {
    protected static $table = 'bank_soal';
    
    /**
     * Get all question banks with question count
     */
    public function getAllBanks() {
        $query = "SELECT 
                    b.id,
                    b.nama,
                    b.created_at,
                    b.updated_at,
                    b.token,
                    b.is_active,
                    COUNT(s.id) as jumlah_soal,
                    SUM(CASE WHEN s.status_soal = 'pilihan_ganda' THEN 1 ELSE 0 END) as pg_count,
                    SUM(CASE WHEN s.status_soal != 'pilihan_ganda' AND s.id IS NOT NULL THEN 1 ELSE 0 END) as essay_count
                  FROM " . self::$table . " b
                  LEFT JOIN soal s ON b.id = s.bank_soal_id
                  GROUP BY b.id, b.nama, b.deskripsi, b.created_at, b.updated_at, b.token, b.is_active
                  ORDER BY b.created_at DESC";
        
        $stmt = self::getDB()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get last inserted ID
     */
    public function getLastInsertId() {
        return self::getDB()->lastInsertId();
    }

    /**
     * Get single bank by ID
     */
    public function getBankById($id) {
        $query = "SELECT * FROM " . self::$table . " WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new question bank
     */
    public function save($nama, $deskripsi, $token) {
        $sql = "INSERT INTO " . self::$table . " (nama, deskripsi, token) VALUES (?, ?, ?)";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $nama);
        $stmt->bindParam(2, $deskripsi);
        $stmt->bindParam(3, $token);
        return $stmt->execute();
    }

    /**
     * Update question bank
     */
    public function updateBank($id, $nama, $deskripsi, $token) {
        $sql = "UPDATE " . self::$table . " SET nama = ?, deskripsi = ?, token = ? WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $nama);
        $stmt->bindParam(2, $deskripsi);
        $stmt->bindParam(3, $token);
        $stmt->bindParam(4, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Set a specific bank as active and deactivate others
     */
    public function setActiveBank($id) {
        try {
            self::getDB()->beginTransaction();

            // Deactivate all
            $resetSql = "UPDATE " . self::$table . " SET is_active = 0";
            self::getDB()->exec($resetSql);

            // Activate specific one
            $setSql = "UPDATE " . self::$table . " SET is_active = 1 WHERE id = ?";
            $stmt = self::getDB()->prepare($setSql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            return self::getDB()->commit();
        } catch (\Exception $e) {
            self::getDB()->rollBack();
            return false;
        }
    }

    /**
     * Get the currently active bank
     */
    public function getActiveBank() {
        $sql = "SELECT * FROM " . self::$table . " WHERE is_active = 1 LIMIT 1";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete question bank
     */
    public function deleteBank($id) {
        $sql = "DELETE FROM " . self::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
