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
            error_log("Error: ID mahasiswa tidak ditemukan untuk user ID $id");
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