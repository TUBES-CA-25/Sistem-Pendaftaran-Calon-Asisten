<?php
namespace App\Model;
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
        jp.id AS id,
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
                'id' => $result['id'],
                'stambuk' => $result['stambuk'],
                'nama' => $result['nama_lengkap'],
                'nama_lengkap' => $result['nama_lengkap'],
                'judul' => $result['judul_presentasi'],
                'judul_presentasi' => $result['judul_presentasi'],
                'id_ruangan' => $result['id_ruangan'],
                'ruangan' => $ruangan['nama'],
                'tanggal' => $result['tanggal'],
                'waktu' => $result['waktu']
            ];
        }

        return $finalResults;
    }


    public function save(JadwalPresentasi $jadwalPresentasi, $mahasiswas)
    {
        $skippedCount = 0;
        $insertedCount = 0;

        foreach ($mahasiswas as $mahasiswa) {
            $idRuangan = (int) $jadwalPresentasi->id_ruangan;

            // The mahasiswa array might have different keys depending on source
            // Could be 'id', 'id_presentasi', or 'id_mahasiswa'
            $idPresentasi = null;
            if (isset($mahasiswa['id_presentasi'])) {
                $idPresentasi = (int) $mahasiswa['id_presentasi'];
            } elseif (isset($mahasiswa['id'])) {
                $idPresentasi = (int) $mahasiswa['id'];
            }

            if (!$idPresentasi) {
                error_log("JadwalPresentasi::save - No id_presentasi found in data: " . print_r($mahasiswa, true));
                $skippedCount++;
                continue;
            }

            // Get id_mahasiswa from presentasi table
            $getMhsSql = "SELECT id_mahasiswa FROM presentasi WHERE id = ?";
            $getMhsStmt = self::getDB()->prepare($getMhsSql);
            $getMhsStmt->bindValue(1, $idPresentasi, \PDO::PARAM_INT);
            $getMhsStmt->execute();
            $mhsData = $getMhsStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$mhsData) {
                error_log("JadwalPresentasi::save - Presentasi not found for id: $idPresentasi");
                $skippedCount++;
                continue;
            }

            $idMahasiswa = $mhsData['id_mahasiswa'];

            // Check if mahasiswa already has ANY presentation schedule
            $checkSql = "SELECT COUNT(*) as count
                         FROM " . static::$table . " jp
                         JOIN presentasi p ON jp.id_presentasi = p.id
                         WHERE p.id_mahasiswa = ?";
            $checkStmt = self::getDB()->prepare($checkSql);
            $checkStmt->bindValue(1, $idMahasiswa, \PDO::PARAM_INT);
            $checkStmt->execute();
            $result = $checkStmt->fetch(\PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                error_log("JadwalPresentasi::save - Mahasiswa $idMahasiswa already has schedule, skipping");
                $skippedCount++;
                continue;
            }

            // Insert new schedule
            $sql = "INSERT INTO " . static::$table . "
            (id_presentasi,id_ruangan,tanggal,waktu) VALUES (?,?,?,?)";
            $date = $this->validateAndFormatDate($jadwalPresentasi->tanggal);
            $time = $this->validateAndFormatTime($jadwalPresentasi->waktu);
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(1, $idPresentasi);
            $stmt->bindValue(2, $idRuangan);
            $stmt->bindValue(3, $date);
            $stmt->bindValue(4, $time);
            if (!$stmt->execute()) {
                error_log(print_r($stmt->errorInfo(), true));
                return false;
            }
            error_log("JadwalPresentasi::save - Successfully inserted schedule for mahasiswa $idMahasiswa");
            $insertedCount++;
        }

        // If all students were skipped because they already have schedules
        if ($skippedCount > 0 && $insertedCount === 0) {
            throw new \Exception("Semua mahasiswa yang dipilih sudah memiliki jadwal presentasi");
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
     * Only include mahasiswa who have completed Tes Tertulis
     * Check based on id_mahasiswa to prevent duplicate scheduling
     */
    public function getMahasiswaWithoutSchedule()
    {
        $logFile = __DIR__ . '/../../debug_presentasi.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - getMahasiswaWithoutSchedule called\n", FILE_APPEND);

        // Debug: Check how many presentasi are accepted
        $debugSql1 = "SELECT COUNT(*) as count FROM presentasi WHERE is_accepted = 1";
        $debugStmt1 = self::getDB()->prepare($debugSql1);
        $debugStmt1->execute();
        $acceptedCount = $debugStmt1->fetch(\PDO::FETCH_ASSOC)['count'];
        file_put_contents($logFile, "Total presentasi accepted: $acceptedCount\n", FILE_APPEND);

        // Debug: Check how many already scheduled
        $debugSql2 = "SELECT COUNT(DISTINCT p.id_mahasiswa) as count
                      FROM jadwal_presentasi jp
                      JOIN presentasi p ON jp.id_presentasi = p.id";
        $debugStmt2 = self::getDB()->prepare($debugSql2);
        $debugStmt2->execute();
        $scheduledCount = $debugStmt2->fetch(\PDO::FETCH_ASSOC)['count'];
        file_put_contents($logFile, "Total mahasiswa already scheduled: $scheduledCount\n", FILE_APPEND);

        // Main query - removed absensi filter for now to test
        $sql = "SELECT
                p.id as id_presentasi,
                m.id as id_mahasiswa,
                m.nama_lengkap,
                m.stambuk,
                p.judul
            FROM presentasi p
            JOIN mahasiswa m ON p.id_mahasiswa = m.id
            LEFT JOIN (
                SELECT p2.id_mahasiswa
                FROM jadwal_presentasi jp2
                JOIN presentasi p2 ON jp2.id_presentasi = p2.id
            ) scheduled ON scheduled.id_mahasiswa = m.id
            WHERE p.is_accepted = 1 AND scheduled.id_mahasiswa IS NULL
            ORDER BY m.nama_lengkap ASC";

        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        file_put_contents($logFile, "Available mahasiswa count: " . count($result) . "\n", FILE_APPEND);
        if (count($result) > 0) {
            file_put_contents($logFile, "First result: " . print_r($result[0], true) . "\n", FILE_APPEND);
        }
        file_put_contents($logFile, "---\n", FILE_APPEND);

        return $result;
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
        // Write to file log for debugging
        $logFile = __DIR__ . '/../../debug_presentasi.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - saveSingle called\n", FILE_APPEND);
        file_put_contents($logFile, "id_presentasi: $id_presentasi, id_ruangan: $id_ruangan, tanggal: $tanggal, waktu: $waktu\n", FILE_APPEND);

        // Get id_mahasiswa from presentasi table
        $getMhsSql = "SELECT id_mahasiswa FROM presentasi WHERE id = ?";
        $getMhsStmt = self::getDB()->prepare($getMhsSql);
        $getMhsStmt->bindParam(1, $id_presentasi, \PDO::PARAM_INT);
        $getMhsStmt->execute();
        $mhsData = $getMhsStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$mhsData) {
            file_put_contents($logFile, "ERROR: Data presentasi tidak ditemukan for id_presentasi: $id_presentasi\n", FILE_APPEND);
            throw new \Exception("Data presentasi tidak ditemukan");
        }

        $idMahasiswa = $mhsData['id_mahasiswa'];
        file_put_contents($logFile, "Found id_mahasiswa: $idMahasiswa\n", FILE_APPEND);

        // Check if mahasiswa already has ANY presentation schedule
        $checkSql = "SELECT COUNT(*) as count
                     FROM " . static::$table . " jp
                     JOIN presentasi p ON jp.id_presentasi = p.id
                     WHERE p.id_mahasiswa = ?";
        $checkStmt = self::getDB()->prepare($checkSql);
        $checkStmt->bindParam(1, $idMahasiswa, \PDO::PARAM_INT);
        $checkStmt->execute();
        $result = $checkStmt->fetch(\PDO::FETCH_ASSOC);

        file_put_contents($logFile, "Duplicate check count: " . $result['count'] . "\n", FILE_APPEND);

        if ($result['count'] > 0) {
            file_put_contents($logFile, "BLOCKING: Mahasiswa $idMahasiswa already has schedule - throwing exception\n", FILE_APPEND);
            throw new \Exception("Mahasiswa ini sudah memiliki jadwal presentasi");
        }

        file_put_contents($logFile, "Validation passed, inserting new schedule\n", FILE_APPEND);

        $sql = "INSERT INTO " . static::$table . " (id_presentasi, id_ruangan, tanggal, waktu) VALUES (?, ?, ?, ?)";

        $date = $this->validateAndFormatDate($tanggal);
        $time = $this->validateAndFormatTime($waktu);

        file_put_contents($logFile, "Formatted date: $date, time: $time\n", FILE_APPEND);

        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id_presentasi, \PDO::PARAM_INT);
        $stmt->bindParam(2, $id_ruangan, \PDO::PARAM_INT);
        $stmt->bindParam(3, $date);
        $stmt->bindParam(4, $time);

        $executeResult = $stmt->execute();
        file_put_contents($logFile, "Insert result: " . ($executeResult ? "SUCCESS" : "FAILED") . "\n\n", FILE_APPEND);

        return $executeResult;
    }
}
