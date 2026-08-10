<?php
namespace App\Model;
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
        $sql = "SELECT w.id, w.id_mahasiswa, m.nama_lengkap, m.stambuk, r.nama as ruangan, w.jenis_wawancara, w.waktu, w.tanggal,
                       (SELECT foto FROM berkas_mahasiswa WHERE id_mahasiswa = m.id ORDER BY id DESC LIMIT 1) as foto
                FROM " . self::$table . " w 
                JOIN mahasiswa m ON w.id_mahasiswa = m.id 
                JOIN ruangan r ON w.id_ruangan = r.id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllWawancaraOnly()
    {
        // LEFT JOIN ke ruangan, bukan JOIN.
        //
        // Tidak ada foreign key dari wawancara.id_ruangan ke ruangan.id, jadi
        // menghapus sebuah ruangan meninggalkan jadwal yang menunjuk id yang
        // sudah tidak ada. Dengan INNER JOIN baris itu hilang total dari
        // daftar - admin tidak bisa melihat apalagi menghapusnya, padahal
        // jadwalnya tetap terhitung saat pengecekan bentrok slot. Akibatnya
        // ruangan terlihat kosong tetapi menolak dipakai.
        //
        // COALESCE memberi label yang jelas supaya barisnya bisa dikenali dan
        // diperbaiki, bukan tampil sebagai sel kosong.
        $sql = "SELECT w.id, w.id_mahasiswa, m.nama_lengkap, m.stambuk,
                       COALESCE(r.nama, '(ruangan dihapus)') as ruangan,
                       w.jenis_wawancara, w.waktu, w.tanggal, w.id_ruangan,
                       (SELECT foto FROM berkas_mahasiswa WHERE id_mahasiswa = m.id ORDER BY id DESC LIMIT 1) as foto
                FROM " . self::$table . " w
                JOIN mahasiswa m ON w.id_mahasiswa = m.id
                LEFT JOIN ruangan r ON w.id_ruangan = r.id
                WHERE w.jenis_wawancara NOT LIKE 'Tes Tertulis%'
                ORDER BY w.tanggal DESC, w.waktu DESC";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllFilterByRuangan($id)
    {
        $sql = "SELECT w.id, w.id_mahasiswa, m.nama_lengkap, m.stambuk, r.nama as ruangan, w.jenis_wawancara, w.waktu, w.tanggal, w.id_ruangan,
                       (SELECT foto FROM berkas_mahasiswa WHERE id_mahasiswa = m.id ORDER BY id DESC LIMIT 1) as foto
                FROM " . self::$table . " w 
                JOIN mahasiswa m ON w.id_mahasiswa = m.id 
                JOIN ruangan r ON w.id_ruangan = r.id 
                WHERE w.id_ruangan = ? AND w.jenis_wawancara NOT LIKE 'Tes Tertulis%'
                ORDER BY w.tanggal DESC, w.waktu DESC";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getWawancaraById($id)
    {
        $idMhs = $this->getMahasiswaIdValue($id);
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
        $idMhsOfUser = $this->getMahasiswaIdValue($idUser);
        
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
            
            // Set dynamic type based on title
            if ($judul === 'Tes Tertulis') {
                $row['status_kehadiran'] = $row['absensi_tes_tertulis'];
                $row['jenis'] = 'Ujian Tertulis'; 
            } elseif ($judul === 'Presentasi') {
                $row['status_kehadiran'] = $row['absensi_presentasi'];
                $row['jenis'] = 'Presentasi';
            } else {
                // Default to Wawancara for other types in this table
                $row['jenis'] = 'Wawancara';
                
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
        // Determine if this is Tes Tertulis or Wawancara
        $isTertulis = (stripos($wawancara->jenis_wawancara, 'Tes Tertulis') !== false);

        if ($isTertulis) {
            // For Tes Tertulis: Check if mahasiswa already has ANY Tes Tertulis schedule
            $checkSql = "SELECT COUNT(*) as count FROM " . self::$table . "
                         WHERE id_mahasiswa = ? AND jenis_wawancara LIKE 'Tes Tertulis%'";
        } else {
            // Duplikat diperiksa per JENIS wawancara, bukan "wawancara apa pun".
            //
            // Dulu query ini memakai NOT LIKE 'Tes Tertulis%' sehingga peserta
            // yang sudah dijadwalkan Wawancara Kepala Lab I dianggap sudah
            // punya jadwal - akibatnya penjadwalan Lab II untuk peserta yang
            // sama selalu dilewati. Padahal keduanya tahap berbeda yang memang
            // harus dijalani berurutan.
            $checkSql = "SELECT COUNT(*) as count FROM " . self::$table . "
                         WHERE id_mahasiswa = ? AND jenis_wawancara = ?";
        }
        $checkStmt = self::getDB()->prepare($checkSql);

        $sql = "INSERT INTO " . self::$table . " (id_mahasiswa, id_ruangan, jenis_wawancara, waktu, tanggal) VALUES (?, ?, ?, ?, ?)";
        $stmt = self::getDB()->prepare($sql);

        $skippedCount = 0;
        $insertedCount = 0;

        foreach ($id as $idmahasiswa) {
            // Check if this mahasiswa already has schedule
            $checkStmt->bindValue(1, $idmahasiswa);
            // Cabang wawancara membandingkan jenisnya juga (lihat catatan di
            // atas); cabang tes tertulis hanya memakai satu parameter.
            if (!$isTertulis) {
                $checkStmt->bindValue(2, $wawancara->jenis_wawancara);
            }
            $checkStmt->execute();
            $result = $checkStmt->fetch(\PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                // Skip this mahasiswa, already scheduled
                $skippedCount++;
                continue;
            }

            // Insert new schedule
            $stmt->bindValue(1, $idmahasiswa);
            $stmt->bindValue(2, $wawancara->id_ruangan);
            $stmt->bindValue(3, $wawancara->jenis_wawancara);
            $stmt->bindValue(4, $wawancara->waktu);
            $stmt->bindValue(5, $wawancara->tanggal);
            $stmt->execute();
            $insertedCount++;
        }

        // Return info about skipped items
        if ($skippedCount > 0 && $insertedCount === 0) {
            if ($isTertulis) {
                throw new \Exception("Semua mahasiswa yang dipilih sudah memiliki jadwal Tes Tertulis");
            } else {
                throw new \Exception("Semua mahasiswa yang dipilih sudah memiliki jadwal wawancara");
            }
        }

        return true;
    }
    public function updateWawancara($id, Wawancara $wawancara) {
        $sql = "UPDATE " . self::$table . " SET id_ruangan = ?, jenis_wawancara = ?, waktu = ?, tanggal = ? WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindValue(1, $wawancara->id_ruangan);
        $stmt->bindValue(2, $wawancara->jenis_wawancara);
        $stmt->bindValue(3, $wawancara->waktu);
        $stmt->bindValue(4, $wawancara->tanggal);
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
    /**
     * Kembalikan ID mahasiswa sebagai SKALAR (bukan array).
     *
     * Sengaja TIDAK memakai Model::getIdMahasiswa() milik induk yang
     * mengembalikan array ['id' => ...]. Namanya dibedakan supaya perbedaan
     * kontrak ini jelas dan tidak lagi menimpa method induk.
     */
    private function getMahasiswaIdValue($id) {
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
            error_log("Error in getMahasiswaIdValue: " . $e->getMessage());
            return null; 
        }
    }
    

    /**
     * Mencari jadwal lain yang memakai ruangan + tanggal + jam yang sama.
     *
     * Tabel `wawancara` menampung jadwal TES TERTULIS dan WAWANCARA sekaligus,
     * jadi bentrok ruangan harus diperiksa lintas keduanya - satu ruangan tidak
     * bisa dipakai dua kegiatan pada jam yang sama, apa pun jenisnya.
     *
     * Sebelumnya logika ini hanya ada di JadwalTesController sebagai method
     * privat, sehingga penjadwalan WAWANCARA sama sekali tidak memeriksanya:
     * dua peserta bisa dijadwalkan di ruangan, tanggal, dan jam yang sama.
     * Dipindahkan ke model supaya kedua alur memakai aturan yang sama persis.
     *
     * @param  int|string      $id_ruangan
     * @param  string          $tanggal
     * @param  string          $waktu
     * @param  int|string|null $abaikanId  id jadwal yang sedang diubah
     * @return string|null  Keterangan pemakai slot, atau null bila kosong.
     */
    public static function cariBentrokJadwal($id_ruangan, $tanggal, $waktu, $abaikanId = null): ?string
    {
        $sql = "SELECT m.nama_lengkap, w.jenis_wawancara
                FROM " . self::$table . " w
                JOIN mahasiswa m ON w.id_mahasiswa = m.id
                WHERE w.id_ruangan = ? AND w.tanggal = ? AND w.waktu = ?";
        $params = [$id_ruangan, $tanggal, $waktu];

        if ($abaikanId !== null) {
            $sql .= " AND w.id <> ?";
            $params[] = $abaikanId;
        }
        $sql .= " LIMIT 1";

        $stmt = self::getDB()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }
        return trim($row['nama_lengkap']) . ' (' . $row['jenis_wawancara'] . ')';
    }
}
