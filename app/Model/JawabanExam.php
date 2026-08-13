<?php
namespace App\Model;

use app\Core\Model;
use PDO;
class JawabanExam extends Model {
    protected static $table = 'jawaban';
    protected $id;
    protected $id_soal;
    protected $id_mahasiswa;
    protected $jawaban;


    public function saveJawaban($id_soal, $id_user, $jawaban) {
        try {
            $mahasiswa = $this->getIdMahasiswa($id_user);
            if (!$mahasiswa) {
                 return false; // Or throw Exception
            }
            $id_mahasiswa = $mahasiswa['id'];

            // Since there's no unique constraint on (id_soal, id_mahasiswa), we must manual check
            $checkQ = "SELECT id FROM jawaban WHERE id_soal = :id_soal AND id_mahasiswa = :id_mahasiswa";
            $checkStmt = self::getDB()->prepare($checkQ);
            $checkStmt->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
            $checkStmt->bindParam(':id_mahasiswa', $id_mahasiswa, PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                // Update
                $query = "UPDATE jawaban SET jawaban = :jawaban, modified = NOW() WHERE id_soal = :id_soal AND id_mahasiswa = :id_mahasiswa";
            } else {
                // Insert
                $query = "INSERT INTO jawaban (id_soal, id_mahasiswa, jawaban, created_at) VALUES (:id_soal, :id_mahasiswa, :jawaban, NOW())";
            }
        
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
            $stmt->bindParam(':id_mahasiswa', $id_mahasiswa, PDO::PARAM_INT);
            $stmt->bindParam(':jawaban', $jawaban, PDO::PARAM_STR);
        
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error saving jawaban: " . $e->getMessage());
            return false;
        }
    }

    public static function updateSkorAdmin($id_soal, $id_mahasiswa, $skor) {
        try {
            $checkQ = "SELECT id FROM jawaban WHERE id_soal = :id_soal AND id_mahasiswa = :id_mahasiswa";
            $checkStmt = self::getDB()->prepare($checkQ);
            $checkStmt->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
            $checkStmt->bindParam(':id_mahasiswa', $id_mahasiswa, PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                $query = "UPDATE " . self::$table . " SET skor_admin = :skor, modified = NOW() WHERE id_soal = :id_soal AND id_mahasiswa = :id_mahasiswa";
            } else {
                $query = "INSERT INTO " . self::$table . " (id_soal, id_mahasiswa, jawaban, skor_admin, created_at) VALUES (:id_soal, :id_mahasiswa, '', :skor, NOW())";
            }

            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':skor', $skor, PDO::PARAM_INT);
            $stmt->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
            $stmt->bindParam(':id_mahasiswa', $id_mahasiswa, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error updating skor admin: " . $e->getMessage());
            return false;
        }
    }
    
    public function getJawaban($id_soal) {
        $query = "SELECT jawaban FROM " . self::$table . " WHERE id_soal = :id_soal";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id_soal', $id_soal);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // getIdMahasiswa() dipindahkan ke App\Core\Model (induk) karena
    // isinya identik di 4 model. Lihat catatan kontrak return di sana.
    public function getJawabanByIdMahasiswa() {
        $query = "SELECT * FROM " . self::$table . " WHERE id_mahasiswa = :id";
        $id = $this->getIdMahasiswa($_SESSION['user']['id'])['id'];
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function deleteJawabanByMahasiswa($id_mahasiswa) {
        $query = "DELETE FROM " . self::$table . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id_mahasiswa, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
