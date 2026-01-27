<?php

namespace App\Model\presentasi;

use App\Core\Model;

class Ruangan extends Model {


    static protected $table = 'ruangan';

    public function getAll() {
        $sql = "SELECT * FROM " . static::$table;
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function insertRuangan($nama) {
        $sql = "INSERT INTO " . static::$table . " (nama) VALUES (?) ";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $nama);
        $stmt->execute();
    }
    public function deleteRuangan($id) {
        $sql = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
    }
    public function updateRuangan($id,$nama) {
        $sql = "UPDATE " . static::$table . " SET nama = ?, modified = ? WHERE id = ?";
        $date = date('Y-m-d H:i:s');
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $nama);
        $stmt->bindParam(2, $date);
        $stmt->bindParam(3, $id);
        $stmt->execute();
    }

    // User Assignment Methods (Refactored to pull from schedules)
    public function getUsersByRoom($roomId, $type) {
        $db = self::getDB();
        
        if($type === 'tes_tulis') {
            // Join with wawancara where jenis_wawancara is Tes Tertulis
            // also join with nilai_akhir to check finished status
            $sql = "SELECT m.id_user as id, m.nama_lengkap as name, m.stambuk, 
                           CASE WHEN na.id IS NOT NULL THEN 1 ELSE 0 END as is_finished
                    FROM wawancara w
                    JOIN mahasiswa m ON w.id_mahasiswa = m.id
                    LEFT JOIN nilai_akhir na ON na.id_mahasiswa = m.id
                    WHERE w.id_ruangan = ? AND w.jenis_wawancara LIKE 'Tes Tertulis%'";
        } elseif($type === 'presentasi') {
            // Join with jadwal_presentasi
            $sql = "SELECT m.id_user as id, m.nama_lengkap as name, m.stambuk, 0 as is_finished
                    FROM jadwal_presentasi jp
                    JOIN presentasi p ON jp.id_presentasi = p.id
                    JOIN mahasiswa m ON p.id_mahasiswa = m.id
                    WHERE jp.id_ruangan = ?";
        } elseif($type === 'wawancara') {
            // Join with wawancara where jenis_wawancara is NOT Tes Tertulis
            $sql = "SELECT m.id_user as id, m.nama_lengkap as name, m.stambuk, 0 as is_finished
                    FROM wawancara w
                    JOIN mahasiswa m ON w.id_mahasiswa = m.id
                    WHERE w.id_ruangan = ? AND w.jenis_wawancara NOT LIKE 'Tes Tertulis%'";
        } else {
            return [];
        }

        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $roomId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Deprecated: No longer used as it's read-only based on schedules
    public function getAvailableUsers($type) {
        return [];
    }

    // Deprecated: No longer used as it's read-only based on schedules
    public function assignUserToRoom($userId, $roomId, $type) {
        return false;
    }

    // Deprecated: No longer used as it's read-only based on schedules
    public function removeUserFromRoom($userId, $type) {
        return false;
    }

    public function getAllRoomOccupants($roomId) {
        // Fetch all users who have this room ID in ANY of the 3 columns
        $sql = "SELECT id, username as name, stambuk, 
                CASE 
                    WHEN id_ruang_presentasi = ? THEN 'Presentasi'
                    WHEN id_ruang_tes_tulis = ? THEN 'Tes Tulis'
                    WHEN id_ruang_wawancara = ? THEN 'Wawancara'
                END as activity
                FROM user 
                WHERE id_ruang_presentasi = ? 
                   OR id_ruang_tes_tulis = ? 
                   OR id_ruang_wawancara = ?
                ORDER BY activity, username";
        
        $stmt = self::getDB()->prepare($sql);
        // Bind parameters: 3 for CASE, 3 for WHERE = 6 total
        for($i=1; $i<=6; $i++) {
            $stmt->bindParam($i, $roomId);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getColumnByType($type) {
        switch($type) {
            case 'presentasi': return 'id_ruang_presentasi';
            case 'tes_tulis': return 'id_ruang_tes_tulis';
            case 'wawancara': return 'id_ruang_wawancara';
            default: return null;
        }
    }
}