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
}