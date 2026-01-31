<?php

namespace App\Model;
use App\Core\Model;

class NotificationUser extends Model {
    protected static $table = 'notifikasi';
    protected $id;
    protected $id_mahasiswa;
    protected $pesan;

    public function __construct(
        $id_mahasiswa = null,
        $pesan = null
    ) {
        $this->id_mahasiswa = $id_mahasiswa;
        $this->pesan = $pesan;
    }

    public function getAll() {
        $query = "SELECT * FROM " . static::$table;
        $stmt = self::getDB()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach ($result as $stmt) {
            $data[] = [
                'id' => $stmt['id'],
                'id_mahasiswa' => $stmt['id_mahasiswa'],
                'pesan' => $stmt['pesan']
            ];
        }

        return $data;
    }

    public function getById(NotificationUser $notification) {
        $query = "SELECT * FROM " . static::$table . " WHERE id_mahasiswa = :idMahasiswa ORDER BY created_at DESC";
        $stmt = self::getDB()->prepare($query);
        $idMahasiswa = $this->getIdMahasiswaByIdUser($notification->id_mahasiswa);
        if (is_array($idMahasiswa)) {
            $id = $idMahasiswa['id'];
        } else {
            $id = 0;
        }
        $stmt->bindParam(':idMahasiswa', $id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (!$result) {
            return false;
        }
        return $result;
    }

    public function insert(NotificationUser $notification) {
        // Validate that mahasiswa exists
        if (!$notification->id_mahasiswa) {
            throw new \Exception('ID mahasiswa tidak valid');
        }

        // Check if mahasiswa exists in database
        $checkQuery = "SELECT id FROM mahasiswa WHERE id = :id";
        $checkStmt = self::getDB()->prepare($checkQuery);
        $checkStmt->bindParam(':id', $notification->id_mahasiswa);
        $checkStmt->execute();
        
        if (!$checkStmt->fetch()) {
            throw new \Exception('Mahasiswa dengan ID ' . $notification->id_mahasiswa . ' tidak ditemukan');
        }

        $query = "INSERT INTO " . static::$table . " (id_mahasiswa, pesan) VALUES (:id_mahasiswa, :pesan)";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id_mahasiswa', $notification->id_mahasiswa);
        $stmt->bindParam(':pesan', $notification->pesan);
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new \Exception('Gagal menyimpan pesan ke database: ' . implode(', ', $stmt->errorInfo()));
        }
        
        return $result;
    }

    public function getUnreadCount(NotificationUser $notification) {
        $query = "SELECT COUNT(*) as count FROM " . static::$table . " WHERE id_mahasiswa = :idMahasiswa AND is_read = 0";
        $stmt = self::getDB()->prepare($query);
        $idMahasiswa = $this->getIdMahasiswaByIdUser($notification->id_mahasiswa);
        if (is_array($idMahasiswa)) {
            $id = $idMahasiswa['id'];
        } else {
            return 0;
        }
        $stmt->bindParam(':idMahasiswa', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['count'] : 0;
    }

    public function markAllAsRead(NotificationUser $notification) {
        $query = "UPDATE " . static::$table . " SET is_read = 1 WHERE id_mahasiswa = :idMahasiswa AND is_read = 0";
        $stmt = self::getDB()->prepare($query);
        $idMahasiswa = $this->getIdMahasiswaByIdUser($notification->id_mahasiswa);
        if (is_array($idMahasiswa)) {
            $id = $idMahasiswa['id'];
        } else {
            return false;
        }
        $stmt->bindParam(':idMahasiswa', $id);
        return $stmt->execute();
    }

    private function getIdMahasiswaByIdUser($id) {
        $query = "SELECT id FROM mahasiswa WHERE id_user = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        if (!$result) {
            return false;
        }
        return $result;
    }
}
