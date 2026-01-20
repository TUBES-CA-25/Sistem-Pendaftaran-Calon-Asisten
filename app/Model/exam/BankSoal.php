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
                    b.deskripsi,
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
     * Get single bank by ID with question counts
     */
    public function getBankById($id) {
        $query = "SELECT 
                    b.id,
                    b.nama,
                    b.deskripsi,
                    b.created_at,
                    b.updated_at,
                    b.token,
                    b.is_active,
                    COUNT(s.id) as jumlah_soal,
                    SUM(CASE WHEN s.status_soal = 'pilihan_ganda' THEN 1 ELSE 0 END) as jumlah_pg,
                    SUM(CASE WHEN s.status_soal != 'pilihan_ganda' AND s.id IS NOT NULL THEN 1 ELSE 0 END) as jumlah_essay
                  FROM " . self::$table . " b
                  LEFT JOIN soal s ON b.id = s.bank_soal_id
                  WHERE b.id = :id
                  GROUP BY b.id, b.nama, b.deskripsi, b.created_at, b.updated_at, b.token, b.is_active";
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
        try {
            self::getDB()->beginTransaction();
            
            // Delete associated questions first (Manual Cascade)
            $sqlSoal = "DELETE FROM soal WHERE bank_soal_id = ?";
            $stmtSoal = self::getDB()->prepare($sqlSoal);
            $stmtSoal->bindParam(1, $id, PDO::PARAM_INT);
            $stmtSoal->execute();
            
            // Delete the bank
            $sql = "DELETE FROM " . self::$table . " WHERE id = ?";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $success = $stmt->execute();
            
            if ($success) {
                self::getDB()->commit();
                return true;
            } else {
                self::getDB()->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            self::getDB()->rollBack();
            return false;
        }
    }

    /**
     * Get exam statistics (bank count, total questions, PG count, essay count)
     * Business logic for counting should be in Model, not View
     */
    public function getExamStatistics(): array {
        $sql = "SELECT 
            (SELECT COUNT(*) FROM " . self::$table . ") as bank_count,
            (SELECT COUNT(*) FROM soal) as total_soal,
            (SELECT COUNT(*) FROM soal WHERE status_soal = 'pilihan_ganda') as pg_count,
            (SELECT COUNT(*) FROM soal WHERE status_soal != 'pilihan_ganda' OR status_soal IS NULL) as essay_count";
        
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: [
            'bank_count' => 0, 
            'total_soal' => 0, 
            'pg_count' => 0, 
            'essay_count' => 0
        ];
    }
}
