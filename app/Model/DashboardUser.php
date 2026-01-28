<?php

namespace App\Model;

use App\Core\Model;

class DashboardUser extends Model {
    protected static $tablePresentasi = "presentasi";
    protected static $tableMahasiswa = "mahasiswa";
    protected static $tableBerkas = "berkas_mahasiswa";
    protected static $tableNotifikasi = "notifikasi";
    protected static $tableJurusan = "jurusan";
    protected static $tableKelas = "kelas";
    protected static $tableUser = "user";
    protected static $tableAbsensi = "absensi";

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
        foreach ($result as $key => $value) {
            return $value;
        }
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
    public function getGraduationStatus() {
        $id = $this->getMahasiswaId();
        if (!$id) return 'Pending';

        $query = "SELECT COALESCE(total_nilai, nilai) as final_score FROM nilai_akhir WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) return 'Pending';

        return ($result['final_score'] >= 70) ? 'Lulus' : 'Gagal';
    }

    public function isPengumumanOpen() {
        try {
            // Check deadline_kegiatan table for 'pengumuman'
            $query = "SELECT tanggal FROM deadline_kegiatan WHERE jenis = 'pengumuman'";
            $stmt = self::getDB()->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                $today = date('Y-m-d');
                return $today >= $row['tanggal'];
            }

            // Fallback: If no deadline set, assume closed or check if all stages complete
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function getMahasiswaId() {
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
