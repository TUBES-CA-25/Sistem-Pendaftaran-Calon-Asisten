<?php
namespace App\Model;
use App\Core\Model;
class Absensi extends Model {
    protected static $table = 'absensi';
    protected $id;
    protected $wawancaraI;
    protected $wawancaraII;
    protected $wawancaraIII;
    protected $tesTertulis;
    protected $presentasi;
    
    public function __construct(
        $id = null,
        $wawancaraI = null,
        $wawancaraII = null,
        $wawancaraIII = null,
        $tesTertulis = null,
        $presentasi = null
    ) {
        $this->id = $id;
        $this->wawancaraI = $wawancaraI;
        $this->wawancaraII = $wawancaraII;
        $this->wawancaraIII = $wawancaraIII;
        $this->tesTertulis = $tesTertulis;
        $this->presentasi = $presentasi;
    }
    public function getAbsensi() {
        $sql = "SELECT
        a.id,
        a.id_mahasiswa,
                    m.nama_lengkap,
                    m.stambuk,
                    m.foto_profil,
                    a.absensi_wawancara_I,
                    a.absensi_wawancara_II,
                    a.absensi_wawancara_III,
                    a.absensi_tes_tertulis,
                    a.absensi_presentasi,
                    COALESCE(na.total_nilai, na.nilai) as nilai_akhir,
                    bm.accepted as berkas_status,
                    bm.foto as berkas_foto,
                    m.status_akhir
                FROM mahasiswa m
                LEFT JOIN " . self::$table . " a ON m.id = a.id_mahasiswa
                LEFT JOIN nilai_akhir na ON m.id = na.id_mahasiswa
                LEFT JOIN berkas_mahasiswa bm ON m.id = bm.id_mahasiswa
                    AND bm.id = (
                        SELECT MAX(id)
                        FROM berkas_mahasiswa
                        WHERE id_mahasiswa = m.id
                    )
                GROUP BY m.id";

        try {
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getAbsensi: " . $e->getMessage());
            return [];
        }
    }
    

    public function updateTesTertulisAbsensi($id) {
        $sql = "UPDATE ". self::$table . " SET absensi_tes_tertulis = 'Hadir' WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    public function updatePresentasiAbsensi($id) {
        $sql = "UPDATE ". self::$table . " SET absensi_presentasi = 'Hadir' WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function updateWawancaraAbsensiI($id) {
        $sql = "UPDATE ". self::$table . " SET absensi_wawancara_I = 'Hadir' WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    public function updateWawancaraAbsensiII($id) {
        $sql = "UPDATE ". self::$table . " SET absensi_wawancara_II = 'Hadir' WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    public function updateWawancaraAbsensiIII($id) {
        $sql = "UPDATE ". self::$table . " SET absensi_wawancara_III = 'Hadir' WHERE id_mahasiswa = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    
    public function updateAbsensi() {
        $sql = "UPDATE ". self::$table . " SET absensi_wawancara_I = ?, absensi_wawancara_II = ?, absensi_wawancara_III = ?, absensi_tes_tertulis = ?, absensi_presentasi = ? WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindValue(1, $this->wawancaraI);
        $stmt->bindValue(2, $this->wawancaraII);
        $stmt->bindValue(3, $this->wawancaraIII);
        $stmt->bindValue(4, $this->tesTertulis);
        $stmt->bindValue(5, $this->presentasi);
        $stmt->bindValue(6, $this->id);
        return $stmt->execute();
    }
    
    /**
     * Create default absensi record for a new mahasiswa
     * @param int $id_mahasiswa The mahasiswa ID
     * @return bool Success status
     */
    public function createDefaultAbsensi($id_mahasiswa) {
        // Check if absensi already exists
        $checkSql = "SELECT COUNT(*) FROM " . self::$table . " WHERE id_mahasiswa = ?";
        $checkStmt = self::getDB()->prepare($checkSql);
        $checkStmt->bindValue(1, $id_mahasiswa);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() > 0) {
            return true; // Already exists, skip
        }
        
        // Insert default absensi
        $sql = "INSERT INTO " . self::$table . " 
                (id_mahasiswa, absensi_wawancara_I, absensi_wawancara_II, 
                 absensi_wawancara_III, absensi_tes_tertulis, absensi_presentasi) 
                VALUES (?, '-', '-', '-', '-', '-')";
        
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindValue(1, $id_mahasiswa);
        return $stmt->execute();
    }
    
    public function addMahasiswa(Absensi $absensi, $id) {
        if (!is_array($id) || empty($id)) {
            throw new \InvalidArgumentException("Parameter 'id' harus berupa array dan tidak boleh kosong.");
        }
    
        $sql = "INSERT INTO " . self::$table . " 
                (id_mahasiswa, absensi_wawancara_I, absensi_wawancara_II, absensi_wawancara_III, absensi_tes_tertulis, absensi_presentasi) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = self::getDB()->prepare($sql);
        
        $checkSql = "SELECT COUNT(*) FROM " . self::$table . " WHERE id_mahasiswa = ?";
        $checkStmt = self::getDB()->prepare($checkSql);

        foreach ($id as $id_mahasiswa) {
            // Check if exists
            $checkStmt->bindValue(1, $id_mahasiswa);
            $checkStmt->execute();
            if ($checkStmt->fetchColumn() > 0) {
                continue; // Skip if already exists
            }

            $stmt->bindValue(1, $id_mahasiswa);
            $stmt->bindValue(2, $absensi->wawancaraI);
            $stmt->bindValue(3, $absensi->wawancaraII);
            $stmt->bindValue(4, $absensi->wawancaraIII);
            $stmt->bindValue(5, $absensi->tesTertulis);
            $stmt->bindValue(6, $absensi->presentasi);
            $stmt->execute();
        }
        return true;
    }
    
    /**
     * Backfill absensi records for existing mahasiswa who don't have one
     * This is a one-time operation to populate historical data
     * @return int Number of records created
     */
    public function backfillAbsensi() {
        $sql = "INSERT INTO " . self::$table . " 
                (id_mahasiswa, absensi_wawancara_I, absensi_wawancara_II, 
                 absensi_wawancara_III, absensi_tes_tertulis, absensi_presentasi)
                SELECT m.id, '-', '-', '-', '-', '-'
                FROM mahasiswa m
                LEFT JOIN " . self::$table . " a ON m.id = a.id_mahasiswa
                WHERE a.id IS NULL";
        
        try {
            $affected = self::getDB()->exec($sql);
            return $affected;
        } catch (\PDOException $e) {
            error_log("Error in backfillAbsensi: " . $e->getMessage());
            return 0;
        }
    }

    public function deleteAbsensi($id) {
        $sql = "DELETE FROM " . self::$table . " WHERE id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->bindValue(1, $id);
        return $stmt->execute();
    }
    public function resetAbsensiTesTertulis($id_mahasiswa) {
        try {
            $sql = "UPDATE " . self::$table . " SET absensi_tes_tertulis = '-' WHERE id_mahasiswa = :id";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':id', $id_mahasiswa, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (\Exception $e) {
            error_log("Error resetting absensi: " . $e->getMessage());
            return false;
        }
    }
}