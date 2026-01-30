<?php

namespace App\Model;

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

    // User Assignment Methods (Restored from backup/original state)
    public function getUsersByRoom($roomId, $type) {
        $column = $this->getColumnByType($type);
        if (!$column) return [];

        $sql = "SELECT id, username as name, stambuk, 
                CASE WHEN id_ruang_tes_tulis IS NOT NULL AND $column = ? THEN 1 ELSE 0 END as is_finished
                FROM user 
                WHERE $column = ?
                ORDER BY username ASC";
        
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $roomId);
        $stmt->bindParam(2, $roomId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAvailableUsers($type) {
        $column = $this->getColumnByType($type);
        if (!$column) return [];

        // Get users who are NOT assigned to any room for THIS type
        // and only for students (where role != 'Admin')
        $sql = "SELECT u.id, u.username as name, u.stambuk 
                FROM user u
                LEFT JOIN mahasiswa m ON u.id = m.id_user
                WHERE u.$column IS NULL 
                AND (u.role != 'Admin' OR u.role IS NULL)
                ORDER BY u.username ASC";
        
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function assignUserToRoom($userId, $roomId, $type) {
        $column = $this->getColumnByType($type);
        if (!$column) return false;

        $sql = "UPDATE user SET $column = ? WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $roomId);
        $stmt->bindParam(2, $userId);
        return $stmt->execute();
    }

    public function removeUserFromRoom($userId, $type) {
        $column = $this->getColumnByType($type);
        if (!$column) return false;

        $sql = "UPDATE user SET $column = NULL WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $userId);
        return $stmt->execute();
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
