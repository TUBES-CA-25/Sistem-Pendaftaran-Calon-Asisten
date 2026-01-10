<?php

namespace App\Model\User;

use App\Core\Model;

class PengumumanUser extends Model {
    // Definisi tabel static seperti di NotificationUser
    protected static $table = 'pengumuman';

    public function getAll() {
        // Query Standard
        $query = "SELECT * FROM " . static::$table . " ORDER BY created_at DESC";
        
        // Panggil DB menggunakan style Notification (self::getDB)
        $stmt = self::getDB()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();

        // Kita kembalikan hasilnya langsung
        // Jika kosong, dia akan return array kosong []
        return $result;
    }
}