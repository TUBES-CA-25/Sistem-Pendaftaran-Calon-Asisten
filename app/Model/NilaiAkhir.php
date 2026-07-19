<?php
namespace App\Model;

use app\Core\Model;
use PDO;
class NilaiAkhir extends Model
{
    protected static $table = "nilai_akhir";
    protected $id;

    protected $id_mahasiswa;
    protected $nilai;

    public function __construct(
        $id_mahasiswa = null,
        $nilai = null
    ) {
        $this->id_mahasiswa = $id_mahasiswa;
        $this->nilai = $nilai;
    }
    public function saveNilai($id)
    {
        try {
            $total_nilai = 0;

            $mahasiswa = $this->getIdMahasiswa($id);
            if (!$mahasiswa) {
                 throw new \Exception("Data mahasiswa tidak ditemukan untuk User ID: " . $id);
            }
            $id_user = $mahasiswa['id'];

            $query = "SELECT id_soal, jawaban FROM jawaban WHERE id_mahasiswa = :id_mahasiswa";
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':id_mahasiswa', $id_user, PDO::PARAM_INT);
            $stmt->execute();
            $user_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($user_answers as $answer) {
                $id_soal = $answer['id_soal'];
                $jawaban_user = $answer['jawaban'];

                $query_soal = "SELECT s.pilihan, s.jawaban, b.poin_per_soal FROM soal s LEFT JOIN bank_soal b ON s.bank_soal_id = b.id WHERE s.id = :id_soal";
                $stmt_soal = self::getDB()->prepare($query_soal);
                $stmt_soal->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
                $stmt_soal->execute();
                $soal_data = $stmt_soal->fetch(PDO::FETCH_ASSOC);

                if ($soal_data) {
                    $pilihan = json_decode($soal_data['pilihan'], true);
                    $correct_answer = $soal_data['jawaban'];
                    $poin_per_benar = isset($soal_data['poin_per_soal']) ? (int)$soal_data['poin_per_soal'] : 10;
                    
                    // Normalize answers for comparison
                    if (isset($pilihan[$jawaban_user]) && trim($pilihan[$jawaban_user]) === trim($correct_answer)) {
                        $total_nilai += $poin_per_benar;
                    } elseif (trim($jawaban_user) === trim($correct_answer)) {
                         // Fallback for essay or direct match
                         $total_nilai += $poin_per_benar;
                    }
                }
            }

            // Check if record exists first to avoid ON DUPLICATE KEY issues if keys are messy
            $checkQ = "SELECT id FROM nilai_akhir WHERE id_mahasiswa = :id";
            $checkStmt = self::getDB()->prepare($checkQ);
            $checkStmt->bindParam(':id', $id_user);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                 $query_update = "UPDATE nilai_akhir SET nilai = :nilai, modified = NOW() WHERE id_mahasiswa = :id_mahasiswa";
            } else {
                 $query_update = "INSERT INTO nilai_akhir (id_mahasiswa, nilai, created_at) VALUES (:id_mahasiswa, :nilai, NOW())";
            }
            
            $stmt_update = self::getDB()->prepare($query_update);
            $stmt_update->bindParam(':id_mahasiswa', $id_user, PDO::PARAM_INT);
            $stmt_update->bindParam(':nilai', $total_nilai, PDO::PARAM_INT);
            $stmt_update->execute();

            return $total_nilai;
        } catch (\Exception $e) {
            error_log("Error saving nilai: " . $e->getMessage());
            throw $e;
        }
    }

    public function getNilai($id)
    {
        $query = "SELECT nilai FROM " . self::$table . " WHERE id_mahasiswa = :id_mahasiswa";
        $stmt = self::getDB()->prepare($query);
        $id_mahasiswa = $this->getIdMahasiswa($id);
        $stmt->bindParam(':id_mahasiswa', $id_mahasiswa);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getIdMahasiswa($idUser)
    {
        $query = "SELECT id FROM mahasiswa WHERE id_user = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $idUser, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result : null;
    }

    public function updateTotalNilai($id,$nilai)
    {
        $sql = "UPDATE " . static::$table . " SET total_nilai = :nilai WHERE id_mahasiswa = :id_mahasiswa";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindValue(':nilai', $nilai);
        $stmt->bindValue(':id_mahasiswa', $id);
        return $stmt->execute();
    }

    public function getAllNilai()
    {
        $sql = "SELECT m.id, m.nama_lengkap, m.stambuk, n.nilai, n.total_nilai as total,
                       (SELECT foto FROM berkas_mahasiswa WHERE id_mahasiswa = m.id ORDER BY id DESC LIMIT 1) as foto
                FROM mahasiswa m
                JOIN nilai_akhir n ON m.id = n.id_mahasiswa";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getSoalAndJawaban($id)
    {
        // Get all soal with LEFT JOIN to jawaban to show all questions
        // This will return all questions, with jawaban_user as NULL for unanswered questions
        $query = "SELECT s.id, s.deskripsi, s.pilihan, s.jawaban, s.status_soal, s.image_url, j.jawaban as jawaban_user
                  FROM soal s
                  LEFT JOIN jawaban j ON s.id = j.id_soal AND j.id_mahasiswa = :id_mahasiswa
                  ORDER BY s.id ASC";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id_mahasiswa', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteNilaiByMahasiswa($id_mahasiswa) {
        $query = "DELETE FROM " . self::$table . " WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id_mahasiswa, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
