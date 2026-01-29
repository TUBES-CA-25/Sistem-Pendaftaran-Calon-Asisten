<?php
namespace App\Model;

use App\Core\Model;

class JadwalWawancara extends Model {
    protected static $table = 'jadwal_wawancara';
    protected $id;
    protected $id_wawancara;
    protected $id_ruangan;
    protected $tanggal;
    protected $waktu;

    public function __construct(
        $id_wawancara = null,
        $id_ruangan = null,
        $tanggal = null,
        $waktu = null
    ) {
        $this->id_wawancara = $id_wawancara;
        $this->id_ruangan = $id_ruangan;
        $this->tanggal = $tanggal;
        $this->waktu = $waktu;
    }

    public function getAll() {
        $sql = "SELECT * FROM " . self::$table;
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function joinWithWawancara() {
        $sql = "SELECT jw.id, jw.id_wawancara, jw.id_ruangan, jw.tanggal, jw.waktu, w.id_mahasiswa, w.jenis_wawancara, w.absensi FROM " . self::$table . " jw JOIN wawancara w ON jw.id_wawancara = w.id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getAllKegiatanFilterByIdRuangan($id) {
        $sql = "SELECT jw.id, jw.id_wawancara, jw.id_ruangan, jw.tanggal, jw.waktu, w.id_mahasiswa, w.jenis_wawancara, w.absensi FROM " . self::$table . " jw JOIN wawancara w ON jw.id_wawancara = w.id"; 
    }
}
