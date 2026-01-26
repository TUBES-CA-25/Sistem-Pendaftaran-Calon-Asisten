<?php
namespace App\Model\Wawancara;
use App\Core\Model;

class Wawancara extends Model
{
    protected static $table = 'wawancara';
    protected $id;
    protected $id_mahasiswa;
    protected $id_ruangan;
    protected $jenis_wawancara;
    protected $waktu;
    protected $tanggal;
    public function __construct(
        $id_ruangan = null,
        $jenis_wawancara = null,
        $waktu = null,
        $tanggal = null,
    ) {
        $this->id_ruangan = $id_ruangan;
        $this->jenis_wawancara = $jenis_wawancara;
        $this->waktu = $waktu;
        $this->tanggal = $tanggal;
    }

    public function getAll()
    {
        $sql = "SELECT w.id,w.id_mahasiswa,m.nama_lengkap, m.stambuk, r.nama as ruangan, w.jenis_wawancara, w.waktu, w.tanggal FROM " . self::$table . " w JOIN mahasiswa m ON w.id_mahasiswa = m.id JOIN ruangan r ON w.id_ruangan = r.id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllFilterByRuangan($id)
    {
        $sql = "SELECT w.id,w.id_mahasiswa,m.nama_lengkap, m.stambuk, r.nama as ruangan, w.jenis_wawancara, w.waktu, w.tanggal FROM " . self::$table . " w JOIN mahasiswa m ON w.id_mahasiswa = m.id JOIN ruangan r ON w.id_ruangan = r.id WHERE w.id_ruangan = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getWawancaraById($id)
    {
        $idMhs = $this->getIdMahasiswa($id);
        if (!$idMhs) {
            error_log("Error: ID mahasiswa tidak ditemukan for user ID $id");
            return [];
        }
    
        $sql = "SELECT 
                    r.nama AS ruangan, 
                    w.jenis_wawancara, 
                    w.waktu, 
                    w.tanggal 
                FROM " . self::$table . " w 
                JOIN ruangan r ON w.id_ruangan = r.id
                WHERE w.id_mahasiswa = :id";
    
        try {
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindParam(':id', $idMhs, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result ?: []; 
        } catch (\PDOException $e) {
            error_log("Error in getWawancaraById: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all activities (Wawancara, Presentasi, and General Activities) with an 'is_mine' flag and attendance status
     */
    public function getJadwalKegiatanById($idUser) {
        $idMhsOfUser = $this->getIdMahasiswa($idUser);
        
        $activities = [];

        // 1. Fetch ALL Wawancara (which also contains Tes Tertulis and Presentasi sometimes)
        // We join with absensi to get the status
        $sqlWawancara = "SELECT r.nama as ruangan, w.jenis_wawancara as judul, w.waktu, w.tanggal, 'Wawancara' as jenis,
                                m.nama_lengkap, (w.id_mahasiswa = :idMhs) as is_mine,
                                a.absensi_tes_tertulis, a.absensi_presentasi, 
                                a.absensi_wawancara_I, a.absensi_wawancara_II, a.absensi_wawancara_III
                         FROM wawancara w 
                         JOIN ruangan r ON w.id_ruangan = r.id 
                         JOIN mahasiswa m ON w.id_mahasiswa = m.id
                         LEFT JOIN absensi a ON w.id_mahasiswa = a.id_mahasiswa";
        $stmt = self::getDB()->prepare($sqlWawancara);
        $stmt->bindValue(':idMhs', $idMhsOfUser, \PDO::PARAM_INT);
        $stmt->execute();
        $rawWawancara = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($rawWawancara as $row) {
            $row['status_kehadiran'] = 'Belum Ada';
            $judul = $row['judul'];
            
            if ($judul === 'Tes Tertulis') {
                $row['status_kehadiran'] = $row['absensi_tes_tertulis'];
            } elseif ($judul === 'Presentasi') {
                $row['status_kehadiran'] = $row['absensi_presentasi'];
            } else {
                // Use regex to match Wawancara types more reliably
                $judulStr = (string)$judul;
                if (preg_match('/Wawancara.*I($|\s)/i', $judulStr) && !preg_match('/Wawancara.*II/i', $judulStr)) {
                     $row['status_kehadiran'] = $row['absensi_wawancara_I'];
                } elseif (preg_match('/Wawancara.*II($|\s)/i', $judulStr) && !preg_match('/Wawancara.*III/i', $judulStr)) {
                     $row['status_kehadiran'] = $row['absensi_wawancara_II'];
                } elseif (preg_match('/Wawancara.*III($|\s)/i', $judulStr)) {
                     $row['status_kehadiran'] = $row['absensi_wawancara_III'];
                }
            }
            
            $activities[] = $row;
        }

        // 2. Fetch ALL Presentasi from jadwal_presentasi
        $sqlPresentasi = "SELECT r.nama as ruangan, p.judul, jp.waktu, jp.tanggal, 'Presentasi' as jenis, 
                                 m.nama_lengkap, (p.id_mahasiswa = :idMhs) as is_mine,
                                 a.absensi_presentasi as status_kehadiran
                          FROM jadwal_presentasi jp 
                          JOIN presentasi p ON jp.id_presentasi = p.id 
                          JOIN ruangan r ON jp.id_ruangan = r.id 
                          JOIN mahasiswa m ON p.id_mahasiswa = m.id
                          LEFT JOIN absensi a ON p.id_mahasiswa = a.id_mahasiswa";
        $stmt = self::getDB()->prepare($sqlPresentasi);
        $stmt->bindValue(':idMhs', $idMhsOfUser, \PDO::PARAM_INT);
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(\PDO::FETCH_ASSOC));

        // 3. Fetch General Activities (kegiatan_admin)
        $sqlGeneral = "SELECT 'Laboratorium' as ruangan, judul, '00:00:00' as waktu, tanggal, 'Kegiatan' as jenis, 
                              'Sistem' as nama_lengkap, 0 as is_mine, 'Belum Ada' as status_kehadiran
                       FROM kegiatan_admin";
        $stmt = self::getDB()->prepare($sqlGeneral);
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(\PDO::FETCH_ASSOC));

        // Sort by date and time
        usort($activities, function($a, $b) {
            $dateA = $a['tanggal'] . ' ' . $a['waktu'];
            $dateB = $b['tanggal'] . ' ' . $b['waktu'];
            return strcmp($dateA, $dateB);
        });

        return $activities;
    }
    

    public function save(Wawancara $wawancara, $id) {
        $sql = "INSERT INTO " . self::$table . " (id_mahasiswa, id_ruangan, jenis_wawancara, waktu, tanggal) VALUES (?, ?, ?, ?, ?)";
        $stmt = self::getDB()->prepare($sql);
        foreach ($id as $idmahasiswa) {
            $stmt->bindValue(1, $idmahasiswa);
            $stmt->bindValue(2, $wawancara->id_ruangan);
            $stmt->bindValue(3, $wawancara->jenis_wawancara);
            $stmt->bindValue(4, $wawancara->waktu);
            $stmt->bindValue(5, $wawancara->tanggal);
            $stmt->execute();
        }
        return true;
    }
    public function updateWawancara($id, Wawancara $wawancara) {
        $sql = "UPDATE " . self::$table . " SET id_ruangan = ?, jenis_wawancara = ?, waktu = ?, tanggal = ? WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindValue(1, $this->id_ruangan);
        $stmt->bindValue(2, $this->jenis_wawancara);
        $stmt->bindValue(3, $this->waktu);
        $stmt->bindValue(4, $this->tanggal);
        $stmt->bindValue(5, $id);
        $stmt->execute();
        return true;
    }
    public function deleteWawancara($id) {
        $sql = "DELETE FROM " . self::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        return true;
    }
    private function getIdMahasiswa($id) {
        $sql = "SELECT id FROM mahasiswa WHERE id_user = ?";
        try {
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindParam(1, $id, \PDO::PARAM_INT); // Pastikan parameter berupa integer
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC); // Ambil hasil sebagai array asosiatif
            
            // Periksa apakah hasil ditemukan
            if ($result && isset($result['id'])) {
                return $result['id']; // Kembalikan nilai ID mahasiswa
            } else {
                error_log("Error: ID mahasiswa tidak ditemukan untuk user ID $id");
                return null; // Kembalikan null jika tidak ada hasil
            }
        } catch (\PDOException $e) {
            error_log("Error in getIdMahasiswa: " . $e->getMessage());
            return null; 
        }
    }
    
}