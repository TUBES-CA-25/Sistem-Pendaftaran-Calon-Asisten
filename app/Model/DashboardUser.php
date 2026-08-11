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

    /**
     * Timeline seleksi untuk peserta.
     *
     * Tanggalnya diambil dari tahapan yang sama persis dengan yang diatur admin
     * (DashboardAdmin::TAHAPAN_SELEKSI), jadi begitu admin mengubah satu
     * tanggal, peserta langsung melihat perubahannya - tidak ada daftar tahap
     * kedua yang harus ikut disunting.
     *
     * Status tiap tahap dinilai dari DUA sisi:
     *   - kemajuan peserta itu sendiri ($tahapanSelesai) -> "Selesai"
     *   - tanggal hari ini vs tanggal tahap              -> "Berlangsung"/"Akan Datang"/"Lewat"
     * Peserta yang tertinggal karena itu tidak ditandai selesai hanya karena
     * tanggalnya sudah lewat.
     *
     * @param int $tahapanSelesai jumlah tahap yang sudah dituntaskan peserta (0-5)
     * @return array<int, array{kunci: string, label: string, tanggal: string, status: string, urut: int}>
     */
    public static function getTimelineSeleksi(int $tahapanSelesai): array
    {
        $tanggalTahap = DashboardAdmin::getDeadlines();
        $hariIni = date('Y-m-d');

        $timeline = [];
        $urut = 0;
        foreach (DashboardAdmin::TAHAPAN_SELEKSI as $kunci => $def) {
            $urut++;
            $tanggal = $tanggalTahap[$kunci] ?? null;

            if ($tahapanSelesai >= $urut) {
                $status = 'Selesai';
            } elseif ($tahapanSelesai + 1 === $urut) {
                // HANYA tahap yang sedang dijalani yang dinilai terhadap tanggal.
                //
                // Sempat semua tahap belum selesai ikut dibandingkan dengan hari
                // ini; akibatnya peserta yang belum mengerjakan apa pun melihat
                // kelima tahap bertanda "Terlewat" sekaligus - merah semua dan
                // tidak memberi tahu mana yang harus dikerjakan. Sekarang tepat
                // satu tahap yang menjadi sorotan.
                if ($tanggal && $hariIni > $tanggal) {
                    $status = 'Terlewat';
                } elseif ($tanggal === $hariIni) {
                    $status = 'Hari Ini';
                } else {
                    $status = 'Berlangsung';
                }
            } else {
                $status = 'Akan Datang';
            }

            $timeline[] = [
                'kunci'   => $kunci,
                'label'   => $def['label'],
                'tanggal' => $tanggal,
                'status'  => $status,
                'urut'    => $urut,
            ];
        }

        return $timeline;
    }

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
        $id = $this->getMahasiswaId();
        if (!$id) return false;

        try {
            // Check if score exists in nilai_akhir
            // User requested: "jika sudah mengerjakan ujian bagian ini akan terisi"
            $queryNilai = "SELECT COUNT(*) FROM nilai_akhir WHERE id_mahasiswa = :id";
            $stmtNilai = self::getDB()->prepare($queryNilai);
            
            if ($stmtNilai) {
                $stmtNilai->bindParam(':id', $id);
                $stmtNilai->execute();
                if ($stmtNilai->fetchColumn() > 0) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            error_log("Error check nilai in DashboardUser: " . $e->getMessage());
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

        $query = "SELECT status_akhir FROM " . self::$tableMahasiswa . " WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result || empty($result['status_akhir'])) return 'Pending';

        return $result['status_akhir'];
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
