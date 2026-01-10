<?php

namespace App\Model\User;

use App\Core\Model;
use PDO;

class DashboardUser extends Model {
    protected static $tablePresentasi = "presentasi";
    protected static $tableMahasiswa = "mahasiswa";
    protected static $tableBerkas = "berkas_mahasiswa";
    protected static $tableNotifikasi = "notifikasi";
    protected static $tableJurusan = "jurusan";
    protected static $tableKelas = "kelas";
    protected static $tableUser = "user";
    protected static $tableAbsensi = "absensi";

    // ==========================================================
    // BAGIAN 1: FITUR BARU (UPDATE WAKTU DASHBOARD)
    // ==========================================================

    public function updateActivity($id_mahasiswa_custom = null) {
        // LOGIKA PENENTUAN ID TARGET
        if ($id_mahasiswa_custom !== null) {
            // Skenario 1: Dipanggil oleh Admin (ID dikirim manual)
            $target_id = $id_mahasiswa_custom;
        } else {
            // Skenario 2: Dipanggil oleh Mahasiswa sendiri (Ambil dari Session)
            $target_id = $this->getMahasiswaId();
        }

        // DEBUGGING: Pastikan ID tidak kosong
        if (empty($target_id)) {
            // Kita return false saja agar tidak merusak tampilan user jika gagal update
            return 'tidak ada id mahasiswa'; 
        }

        // Query Insert/Update (Upsert) ke tabel dashboard
        $query = "INSERT INTO dashboard (id_mahasiswa, deskripsi, modified) 
                  VALUES (:id, 'Update Aktivitas', NOW()) 
                  ON DUPLICATE KEY UPDATE modified = NOW()";
        
        try {
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':id', $target_id);
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false; // Silent fail agar tidak error fatal di layar user
        }
    }

    public function getLastActivityTime() {
        $query = "SELECT modified FROM dashboard WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['modified'] : false;
    }

    // ==========================================================
    // BAGIAN 2: FUNGSI LAMA ANDA (WAJIB ADA)
    // ==========================================================

    public function getBiodataStatus() {
        $query = "SELECT * FROM " . self::$tableMahasiswa . " WHERE id_user = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $_SESSION['user']['id']);
        $stmt->execute();
        $result = $stmt->fetch();
    
        if (!$result) {
            return false;
        }
        foreach ($result as $key => $value) {
            if (!empty($value)) {
                return true;
            }
        }
        return false;
    }

    public function getBerkasStatus() {
        $query = "SELECT accepted FROM " . self::$tableBerkas . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
    
        if (!$result) {
            return false;
        }
        // Perbaikan logika return agar sesuai fungsi lama
        foreach ($result as $key => $value) {
            return $value;
        }
        return false;
    }
    
    public function getAbsensiTesTertulis() {
        $query = "SELECT absensi_tes_tertulis FROM " . self::$tableAbsensi . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        if(!$result) {
            return false;
        }
        if ($result['absensi_tes_tertulis'] == "Hadir") {
            return true;
        }
        return false;
    }

    public function getAbsensiWawancaraI() {
        $query = "SELECT absensi_wawancara_I FROM " . self::$tableAbsensi . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        if(!$result) {
            return false;
        }
        if ($result['absensi_wawancara_I'] == "Hadir") {
            return true;
        }
        return false; 
    }

    public function getAbsensiWawancaraII() {
        $query = "SELECT absensi_wawancara_II FROM " . self::$tableAbsensi . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        if(!$result) {
            return false;
        }
        if ($result['absensi_wawancara_II'] == "Hadir") {
            return true;
        }
        return false;
    }

    public function getAbsensiWawancaraIII() {
        $query = "SELECT absensi_wawancara_III FROM " . self::$tableAbsensi . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        if(!$result) {
            return false;
        }
        if ($result['absensi_wawancara_III'] == "Hadir") {
            return true;
        }
        return false;
    }

    public function getAbsensiPresentasi() {
        $query = "SELECT absensi_presentasi FROM " . self::$tableAbsensi . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        if(!$result) {
            return false;
        }
        if ($result['absensi_presentasi'] == "Hadir") {
            return true;
        }
        return false;
    }

    public function getStatusPpt() {
        $query = "SELECT is_accepted, is_revisi FROM " . self::$tablePresentasi . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    
        if (!$result) {
            return false;
        }
    
        if (!empty($result['is_revisi'])) {
            return 'revisi'; 
        }
    
        if (!empty($result['is_accepted'])) {
            return 'diterima'; 
        }
    
        return false; 
    }

    public function getPptAccStatus() {
        $query = "SELECT * FROM " . self::$tablePresentasi . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $id = $this->getMahasiswaId();
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    
        if (!$result) {
            return false;
        }
        foreach ($result as $key => $value) {
            if (!empty($value)) {
                return true;
            }
        }
        return false;
    }

    // Helper Private function
    private function getMahasiswaId() {
        // Cek Session dulu
        if (!isset($_SESSION['user']['id'])) {
            return false;
        }

        $query = "SELECT id FROM " . self::$tableMahasiswa . " WHERE id_user = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $_SESSION['user']['id']);
        $stmt->execute();
        $result = $stmt->fetch();
        if(!$result) {
            return false;
        }
        return $result['id'];
    }
    
}