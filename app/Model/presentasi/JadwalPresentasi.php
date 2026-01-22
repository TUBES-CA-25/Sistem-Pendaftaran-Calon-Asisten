<?php
namespace App\Model\Presentasi;
use App\Core\Model;

class JadwalPresentasi extends Model
{
    protected static $table = 'jadwal_presentasi';
    protected $id;
    protected $id_presentasi;
    protected $id_ruangan;
    protected $tanggal;
    protected $waktu;

    public function __construct(
        $id_ruangan = null,
        $tanggal = null,
        $waktu = null
    ) {
        $this->id_ruangan = $id_ruangan;
        $this->tanggal = $tanggal;
        $this->waktu = $waktu;
    }
    public function getJadwalPresentasi()
    {
        $sql = "SELECT 
        m.stambuk AS stambuk,
        m.nama_lengkap AS nama_lengkap,
        p.judul AS judul_presentasi,
        jp.id_ruangan AS id_ruangan,
        jp.tanggal AS tanggal,
        jp.waktu AS waktu
    FROM 
        mahasiswa m
    JOIN 
        presentasi p ON p.id_mahasiswa = m.id
    JOIN 
        jadwal_presentasi jp ON jp.id_presentasi = p.id";

        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC); 

        $finalResults = [];

        foreach ($results as $result) {
            $ruangan = $this->getRuangan($result['id_ruangan']);
            $finalResults[] = [
                'stambuk' => $result['stambuk'],
                'nama' => $result['nama_lengkap'],
                'judul_presentasi' => $result['judul_presentasi'],
                'ruangan' => $ruangan['nama'],
                'tanggal' => $result['tanggal'],
                'waktu' => $result['waktu']
            ];
        }

        return $finalResults;
    }


    public function save(JadwalPresentasi $jadwalPresentasi, $mahasiswas)
    {
        foreach ($mahasiswas as $mahasiswa) {
            $sql = "INSERT INTO " . static::$table . " 
            (id_presentasi,id_ruangan,tanggal,waktu) VALUES (?,?,?,?)";
            $idRuangan = (int) $jadwalPresentasi->id_ruangan;
            $idPresentasi = (int) $mahasiswa['id'];
            
            if ($this->hasSchedule($idPresentasi)) {
                continue;
            }

            $date = $this->validateAndFormatDate($jadwalPresentasi->tanggal);
            $time = $this->validateAndFormatTime($jadwalPresentasi->waktu);
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindParam(1, $idPresentasi);
            $stmt->bindParam(2, $idRuangan);
            $stmt->bindParam(3, $date);
            $stmt->bindParam(4, $time);
            if (!$stmt->execute()) {
                error_log(print_r($stmt->errorInfo(), true));
                return false;
            }
        }
        return true;

    }

    private function getId()
    {
        $sql = "SELECT id_presentasi FROM " . static::$table;
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    private function validateAndFormatDate($date)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $year = (int) substr($date, 0, 4);
            if ($year >= 1900 && $year <= (int) date('Y')) {
                return $date;
            }
        }

        return null;
    }

    private function validateAndFormatTime($time)
    {
        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
            if (strlen($time) === 5) {
                $time .= ":00";
            }
            return $time;
        }
        return null;
    }

    private function getRuangan($id)
    {
        $sql = "SELECT nama FROM ruangan WHERE id = ? limit 1";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get all jadwal with full details (untuk admin)
     */
    public function getAllJadwalWithDetails()
    {
        $sql = "SELECT
                jp.id,
                jp.id_presentasi,
                jp.id_ruangan,
                jp.tanggal,
                jp.waktu,
                m.id as id_mahasiswa,
                m.stambuk,
                m.nama_lengkap,
                p.judul,
                r.nama as ruangan
            FROM jadwal_presentasi jp
            JOIN presentasi p ON jp.id_presentasi = p.id
            JOIN mahasiswa m ON p.id_mahasiswa = m.id
            JOIN ruangan r ON jp.id_ruangan = r.id
            ORDER BY jp.tanggal ASC, jp.waktu ASC";

        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get jadwal by mahasiswa ID (untuk user dashboard)
     */
    public function getJadwalByMahasiswaId($id_mahasiswa)
    {
        $sql = "SELECT
                jp.id,
                jp.tanggal,
                jp.waktu,
                p.judul,
                r.nama as ruangan
            FROM jadwal_presentasi jp
            JOIN presentasi p ON jp.id_presentasi = p.id
            JOIN ruangan r ON jp.id_ruangan = r.id
            WHERE p.id_mahasiswa = ?
            ORDER BY jp.tanggal ASC, jp.waktu ASC
            LIMIT 1";

        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id_mahasiswa, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get upcoming jadwal (untuk dashboard)
     */
    public function getUpcomingJadwal($limit = 5)
    {
        $sql = "SELECT
                jp.id,
                jp.tanggal,
                jp.waktu,
                m.nama_lengkap,
                m.stambuk,
                p.judul,
                r.nama as ruangan
            FROM jadwal_presentasi jp
            JOIN presentasi p ON jp.id_presentasi = p.id
            JOIN mahasiswa m ON p.id_mahasiswa = m.id
            JOIN ruangan r ON jp.id_ruangan = r.id
            WHERE jp.tanggal >= CURDATE()
            GROUP BY p.id
            ORDER BY jp.tanggal ASC, jp.waktu ASC
            LIMIT ?";

        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update jadwal
     */
    public function updateJadwal($id, $id_ruangan, $tanggal, $waktu)
    {
        $sql = "UPDATE " . static::$table . "
                SET id_ruangan = ?, tanggal = ?, waktu = ?
                WHERE id = ?";

        $date = $this->validateAndFormatDate($tanggal);
        $time = $this->validateAndFormatTime($waktu);

        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id_ruangan, \PDO::PARAM_INT);
        $stmt->bindParam(2, $date);
        $stmt->bindParam(3, $time);
        $stmt->bindParam(4, $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Delete jadwal
     */
    public function deleteJadwal($id)
    {
        $sql = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get mahasiswa yang sudah diterima judulnya tapi belum dijadwalkan
     */
    public function getMahasiswaWithoutSchedule()
    {
        $sql = "SELECT
                p.id as id_presentasi,
                m.id as id_mahasiswa,
                m.nama_lengkap,
                m.stambuk,
                p.judul
            FROM presentasi p
            JOIN mahasiswa m ON p.id_mahasiswa = m.id
            LEFT JOIN jadwal_presentasi jp ON jp.id_presentasi = p.id
            WHERE p.is_accepted = 1 AND jp.id IS NULL
            ORDER BY m.nama_lengkap ASC";

        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all ruangan
     */
    public function getAllRuangan()
    {
        $sql = "SELECT id, nama FROM ruangan ORDER BY nama ASC";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Check if mahasiswa already has schedule
     */
    public function hasSchedule($id_presentasi)
    {
        $sql = "SELECT COUNT(*) as count FROM " . static::$table . " WHERE id_presentasi = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id_presentasi, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Save single jadwal
     */
    public function saveSingle($id_presentasi, $id_ruangan, $tanggal, $waktu)
    {
        $sql = "INSERT INTO " . static::$table . " (id_presentasi, id_ruangan, tanggal, waktu) VALUES (?, ?, ?, ?)";

        $date = $this->validateAndFormatDate($tanggal);
        $time = $this->validateAndFormatTime($waktu);

        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id_presentasi, \PDO::PARAM_INT);
        $stmt->bindParam(2, $id_ruangan, \PDO::PARAM_INT);
        $stmt->bindParam(3, $date);
        $stmt->bindParam(4, $time);

        return $stmt->execute();
    }
}