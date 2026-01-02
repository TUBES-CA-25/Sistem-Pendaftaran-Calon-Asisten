<?php
namespace App\Model\Exam;

use App\Core\Model;
use PDO;
class SoalExam extends Model {
    protected static $table = 'soal';
    protected $id;
    protected $deskripsi;
    protected $pilihan;
    protected $jawaban;
    protected $status;

    public function __construct(
        $deskripsi = null,
        $pilihan = null,
        $jawaban = null,
        $status = null
    ) {
        if($jawaban == null) {
            $this->deskripsi = $deskripsi;
            $this->pilihan = $pilihan;
            $this->status = $status;
        } 
         else {
            $this->deskripsi = $deskripsi;
            $this->pilihan = $pilihan;
            $this->jawaban = $jawaban;
            $this->status = $status;
        }
    }
    
    public function getJawaban() {
        return $this->jawaban;
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . self::$table;
        $stmt = self::getDB()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllByParity($isGanjil) {
        $parity = $isGanjil ? 1 : 0; 
        $query = "SELECT * FROM " . self::$table . " WHERE id % 2 = :parity LIMIT 60";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':parity', $parity, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function save(SoalExam  $soal) {
        $sql = "INSERT INTO " . static::$table . " (deskripsi,pilihan,jawaban,status_soal) VALUES (?,?,?,?)";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $soal->deskripsi);
        $stmt->bindParam(2, $soal->pilihan);
        $stmt->bindParam(3, $soal->jawaban);
        $stmt->bindParam(4, $soal->status);
        return $stmt->execute();
    }

    public function saveWithoutAnswer(SoalExam $soal) {
        $sql = "INSERT INTO " . static::$table . " (deskripsi,pilihan,status_soal) VALUES (?,?,?)";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $soal->deskripsi);
        $stmt->bindParam(2, $soal->pilihan);
        $stmt->bindParam(3, $soal->status);
        return $stmt->execute();
    }
    
    public function deleteSoal($id) {
        $sql = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function updateSoal($id, SoalExam $soal) {
        $sql = "UPDATE " . static::$table . " SET deskripsi = ?, pilihan = ?, jawaban = ?, status_soal = ?, modified = ? WHERE id = ?";
    
        $date = date('Y-m-d H:i:s');
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(1, $soal->deskripsi);
        $stmt->bindParam(2, $soal->pilihan);
        $stmt->bindParam(3, $soal->jawaban);
        $stmt->bindParam(4, $soal->status);
        $stmt->bindParam(5, $date);
        $stmt->bindParam(6, $id);
        return $stmt->execute();
    }
    
    public function getCountSoal() {
        $sql = "SELECT COUNT(*) as total FROM " . static::$table;
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}