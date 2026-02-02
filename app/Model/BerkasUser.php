<?php
namespace App\Model;

use App\Core\Model;
use PDO;
use \Exception;

class BerkasUser extends Model {
    protected static $table = 'berkas_mahasiswa';
    protected $id;
    protected $id_mahasiswa;
    protected $foto;
    protected $cv;
    protected $transkripNilai;
    protected $suratPernyataan;
    protected $isRevisi = false;
    protected $isAccepted = false;
    protected $fotoSize;
    protected $cvSize;
    protected $transkripNilaiSize;
    protected $suratPernyataanSize;
    private $imageAccepted = ['jpg', 'jpeg', 'png'];
    private $fileAccepted = 'pdf';
    private $maxFileSize = 1024 * 1024 * 5; // 5mb 

    public function __construct(
        $id_mahasiswa = null,
        $foto = null,
        $cv = null,
        $transkripNilai = null,
        $suratPernyataan = null,
        $isRevisi = null,
        $isAccepted = null,
        $fotoSize = null,
        $cvSize = null,
        $transkripNilaiSize = null,
        $suratPernyataanSize = null
    ) {
        $this->id_mahasiswa = $id_mahasiswa;
        $this->foto = $foto;
        $this->cv = $cv;
        $this->transkripNilai = $transkripNilai;
        $this->suratPernyataan = $suratPernyataan;
        $this->isRevisi = $isRevisi;
        $this->isAccepted = $isAccepted;
        $this->fotoSize = $fotoSize;
        $this->cvSize = $cvSize;
        $this->transkripNilaiSize = $transkripNilaiSize;
        $this->suratPernyataanSize = $suratPernyataanSize;
    }

    public function save(BerkasUser $berkas) {
        // Get id_mahasiswa from id_user first
        $idMahasiswaData = $this->getIdMahasiswa($berkas->id_mahasiswa);
        if (!$idMahasiswaData || !isset($idMahasiswaData['id'])) {
            throw new Exception("Mahasiswa tidak ditemukan");
        }
        $idMahasiswa = $idMahasiswaData['id'];

        // Check if record already exists for this student (using correct id_mahasiswa)
        $checkQuery = "SELECT id FROM " . static::$table . " WHERE id_mahasiswa = ?";
        $stmtCheck = self::getDB()->prepare($checkQuery);
        $stmtCheck->bindParam(1, $idMahasiswa);
        $stmtCheck->execute();

        // If berkas already exists, call update instead (which will delete old files)
        if ($stmtCheck->rowCount() > 0) {
            return $this->update($berkas);
        }

        // If new berkas, proceed with insert
        $query = "INSERT INTO " . static::$table . "
            (id_mahasiswa, foto, cv, transkrip_nilai, surat_pernyataan)
            VALUES
            (?, ?, ?, ?, ?)";

        $stmt = self::getDB()->prepare($query);

        $gambar = $this->getImageName($berkas->foto, $berkas->fotoSize);
        if (!$gambar) {
            throw new Exception("Gagal memproses foto");
        }

        $fileCv = $this->getFileCv($berkas->cv, $berkas->cvSize);
        if (!$fileCv) {
            throw new Exception("Gagal memproses CV");
        }

        $fileNilai = $this->getFileTranskrip($berkas->transkripNilai, $berkas->transkripNilaiSize);
        if (!$fileNilai) {
            throw new Exception("Gagal memproses transkrip nilai");
        }

        $filePernyataan = $this->getFileSuratPernyataan($berkas->suratPernyataan, $berkas->suratPernyataanSize);
        if (!$filePernyataan) {
            throw new Exception("Gagal memproses surat pernyataan");
        }

        $stmt->bindParam(1, $idMahasiswa);
        $stmt->bindParam(2, $gambar);
        $stmt->bindParam(3, $fileCv);
        $stmt->bindParam(4, $fileNilai);
        $stmt->bindParam(5, $filePernyataan);

        return $stmt->execute();
    }

    private function getImageName($berkas, $berkasSize, $inputName = 'foto') {
        if (!isset($_FILES[$inputName])) {
            throw new Exception("File input '$inputName' tidak ditemukan.");
        }
        $imageExt = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        if (!in_array($imageExt, $this->imageAccepted)) {
            throw new Exception("Gunakan ekstensi jpg, jpeg, atau png untuk gambar.");
        }
    
        if ($berkasSize > $this->maxFileSize) {
            throw new Exception("Ukuran file gambar terlalu besar.");
        }
    
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Membuat direktori jika tidak ada
        }
    
        $newImageName = uniqid() . '.' . $imageExt;
    
        if (empty($berkas)) {
            throw new Exception("Path file sementara kosong.");
        }
    
        $destination = $uploadDir . $newImageName;
        if (!move_uploaded_file($berkas, $destination)) {
            throw new Exception("Gagal memindahkan file foto. Pastikan folder tujuan dapat diakses.");
        }
    
        return $newImageName;
    }
    
    private function getFileCv($berkas, $berkasSize) {
        $fileExt = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
        if ($fileExt !== $this->fileAccepted) {
            throw new Exception("Gunakan ekstensi pdf untuk file.");
        }
    
        if ($berkasSize > $this->maxFileSize) {
            throw new Exception("Ukuran file terlalu besar.");
        }
    
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Membuat direktori jika tidak ada
        }
    
        $newFileName = uniqid() . '.' . $fileExt;
    
        if (empty($berkas)) {
            throw new Exception("Path file sementara untuk CV kosong.");
        }
    
        $destination = $uploadDir . $newFileName;
        if (!move_uploaded_file($berkas, $destination)) {
            throw new Exception("Gagal memindahkan file CV. Pastikan folder tujuan dapat diakses.");
        }
    
        return $newFileName;
    }
    private function getFileTranskrip($berkas, $berkasSize) {
        $fileExt = strtolower(pathinfo($_FILES['transkrip']['name'], PATHINFO_EXTENSION));
        if ($fileExt !== $this->fileAccepted) {
            throw new Exception("Gunakan ekstensi pdf untuk file.");
        }
    
        if ($berkasSize > $this->maxFileSize) {
            throw new Exception("Ukuran file terlalu besar.");
        }
    
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Membuat direktori jika tidak ada
        }
    
        $newFileName = uniqid() . '.' . $fileExt;
    
        if (empty($berkas)) {
            throw new Exception("Path file sementara untuk CV kosong.");
        }
    
        $destination = $uploadDir . $newFileName;
        if (!move_uploaded_file($berkas, $destination)) {
            throw new Exception("Gagal memindahkan file CV. Pastikan folder tujuan dapat diakses.");
        }
    
        return $newFileName;
    }
    private function getFileSuratPernyataan($berkas, $berkasSize) {
        $fileExt = strtolower(pathinfo($_FILES['suratpernyataan']['name'], PATHINFO_EXTENSION));
        if ($fileExt !== $this->fileAccepted) {
            throw new Exception("Gunakan ekstensi pdf untuk file.");
        }
    
        if ($berkasSize > $this->maxFileSize) {
            throw new Exception("Ukuran file terlalu besar.");
        }
    
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); 
        }
    

        $newFileName = uniqid() . '.' . $fileExt;
    
        if (empty($berkas)) {
            throw new Exception("Path file sementara untuk CV kosong.");
        }
    
        $destination = $uploadDir . $newFileName;
        if (!move_uploaded_file($berkas, $destination)) {
            throw new Exception("Gagal memindahkan file CV. Pastikan folder tujuan dapat diakses.");
        }
    
        return $newFileName;
    }
    private function getIdMahasiswa($idUser) {
        $query = "SELECT id FROM mahasiswa WHERE id_user = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $idUser, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result : null;
    }
    
    public function getBerkas($id) {
        $query = "SELECT * FROM " . static::$table . " WHERE id_mahasiswa = ?";
        
        $idMahasiswa = $this->getIdMahasiswa($id);
    
        if ($idMahasiswa === null) {
            return null;
        }
    
        $stmt = self::getDB()->prepare($query);
        $stmt->bindValue(1, $idMahasiswa['id']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $result ?: null; 
    }
    
    
    public function updateAccepted($id, $status = 1) {
        // Cek apakah data sudah ada
        $checkQuery = "SELECT id_mahasiswa FROM " . static::$table . " WHERE id_mahasiswa = ?";
        $stmtCheck = self::getDB()->prepare($checkQuery);
        $stmtCheck->bindParam(1, $id);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Update jika ada
            $query = "UPDATE " . static::$table . " SET accepted = ? WHERE id_mahasiswa = ?";
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(1, $status, PDO::PARAM_INT);
            $stmt->bindParam(2, $id);
            return $stmt->execute();
        } else {
            // Insert jika belum ada (dengan nilai default kosong untuk file)
            $query = "INSERT INTO " . static::$table . " (id_mahasiswa, accepted, foto, cv, transkrip_nilai, surat_pernyataan) VALUES (?, ?, '', '', '', '')";
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->bindParam(2, $status, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }

    public function update(BerkasUser $berkasUser) {
        $idMahasiswaData = $this->getIdMahasiswa($berkasUser->id_mahasiswa);
        if(!$idMahasiswaData || !isset($idMahasiswaData['id'])) {
            throw new Exception("Mahasiswa tidak ditemukan");
        }
        $idMahasiswa = $idMahasiswaData['id'];

        // Get old files to delete them after successful update
        $oldFiles = $this->getOldBerkasFiles($idMahasiswa);

        $query = "UPDATE " . static::$table . " SET foto = ?, cv = ?, transkrip_nilai = ?, surat_pernyataan = ?, accepted = 0, created_at = CURRENT_TIMESTAMP WHERE id_mahasiswa = ?";
        $stmt = self::getDB()->prepare($query);

        $gambar = $this->getImageName($berkasUser->foto, $berkasUser->fotoSize);
        if (!$gambar) {
            throw new Exception("Gagal memproses foto");
        }
        $fileCv = $this->getFileCv($berkasUser->cv, $berkasUser->cvSize);
        if (!$fileCv) {
            throw new Exception("Gagal memproses CV");
        }
        $fileNilai = $this->getFileTranskrip($berkasUser->transkripNilai, $berkasUser->transkripNilaiSize);

        if(!$fileNilai) {
            throw new Exception("Gagal memproses transkrip nilai");
        }
        $filePernyataan = $this->getFileSuratPernyataan($berkasUser->suratPernyataan, $berkasUser->suratPernyataanSize);
        if(!$filePernyataan) {
            throw new Exception("Gagal memproses surat pernyataan");
        }

        $stmt->bindParam(1,$gambar);
        $stmt->bindParam(2,$fileCv);
        $stmt->bindParam(3,$fileNilai);
        $stmt->bindParam(4,$filePernyataan);
        $stmt->bindParam(5,$idMahasiswa);

        $result = $stmt->execute();

        // Delete old files after successful update
        if ($result && $oldFiles) {
            $this->deleteOldFiles($oldFiles);
        }

        return $result;
    }

    public function updatePhoto($idUser, $foto, $fotoSize) {
        $idMahasiswaData = $this->getIdMahasiswa($idUser);
        if (!$idMahasiswaData) {
            // If no mahasiswa record yet, we might need to handle it or just return false
            return false;
        }
        $idMahasiswa = $idMahasiswaData['id'];

        // Get old photo to delete after successful update
        $oldFiles = $this->getOldBerkasFiles($idMahasiswa);

        $gambar = $this->getImageName($foto, $fotoSize, 'image');
        if (!$gambar) {
            throw new \Exception("Gagal memproses foto");
        }

        // Check if record exists in berkas
        $checkQuery = "SELECT id_mahasiswa FROM " . static::$table . " WHERE id_mahasiswa = ?";
        $stmtCheck = self::getDB()->prepare($checkQuery);
        $stmtCheck->bindParam(1, $idMahasiswa);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            $query = "UPDATE " . static::$table . " SET foto = ?, created_at = CURRENT_TIMESTAMP WHERE id_mahasiswa = ?";
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(1, $gambar);
            $stmt->bindParam(2, $idMahasiswa);
        } else {
            $query = "INSERT INTO " . static::$table . " (id_mahasiswa, foto, cv, transkrip_nilai, surat_pernyataan, accepted) VALUES (?, ?, '', '', '', 0)";
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(1, $idMahasiswa);
            $stmt->bindParam(2, $gambar);
        }

        $result = $stmt->execute();

        // Delete old photo after successful update
        if ($result && $oldFiles && !empty($oldFiles['foto'])) {
            $basePathImage = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/';
            $fotoPath = $basePathImage . $oldFiles['foto'];
            if (file_exists($fotoPath)) {
                @unlink($fotoPath);
            }
        }

        return $result;
    }
    public function getBerkasAdmin() {
        $query = "SELECT * FROM " . static::$table;
        $stmt = self::getDB()->prepare($query);
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$stmt) {
            return null;
        }
        return $stmt;
    }
    public function isEmpty($id) {
        $query = "SELECT * FROM " . static::$table . " WHERE id_mahasiswa = ?";
        $idMahasiswa = $this->getIdMahasiswa($id);
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1,$idMahasiswa['id']);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? false : true;
    }

    public function isAcceptedBerkasUser() {
        $query = "SELECT accepted FROM " . static::$table . " WHERE id_mahasiswa = ?";
        $idMahasiswa = $this->getIdMahasiswa($_SESSION['user']['id']);
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1,$idMahasiswa['id']);
        $stmt->execute();
        $stmt = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$stmt) {
            return false;
        }
        if($stmt['accepted'] == 1) {
            return true;
        }
        return false;
    }

    /**
     * Get old berkas files before update/delete
     * @param int $idMahasiswa
     * @return array|null Array containing old file names
     */
    private function getOldBerkasFiles($idMahasiswa) {
        $query = "SELECT foto, cv, transkrip_nilai, surat_pernyataan FROM " . static::$table . " WHERE id_mahasiswa = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $idMahasiswa);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete old files from filesystem
     * @param array $files Array containing file names to delete
     */
    private function deleteOldFiles($files) {
        $basePathImage = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/';
        $basePathBerkas = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/';

        // Delete foto (from imageUser directory)
        if (!empty($files['foto'])) {
            $fotoPath = $basePathImage . $files['foto'];
            if (file_exists($fotoPath)) {
                if (@unlink($fotoPath)) {
                    error_log("File berhasil dihapus: " . $fotoPath);
                } else {
                    error_log("Gagal menghapus file: " . $fotoPath);
                }
            } else {
                error_log("File tidak ditemukan untuk dihapus: " . $fotoPath);
            }
        }

        // Delete cv (from berkasUser directory)
        if (!empty($files['cv'])) {
            $cvPath = $basePathBerkas . $files['cv'];
            if (file_exists($cvPath)) {
                if (@unlink($cvPath)) {
                    error_log("File berhasil dihapus: " . $cvPath);
                } else {
                    error_log("Gagal menghapus file: " . $cvPath);
                }
            } else {
                error_log("File tidak ditemukan untuk dihapus: " . $cvPath);
            }
        }

        // Delete transkrip_nilai (from berkasUser directory)
        if (!empty($files['transkrip_nilai'])) {
            $transkripPath = $basePathBerkas . $files['transkrip_nilai'];
            if (file_exists($transkripPath)) {
                if (@unlink($transkripPath)) {
                    error_log("File berhasil dihapus: " . $transkripPath);
                } else {
                    error_log("Gagal menghapus file: " . $transkripPath);
                }
            } else {
                error_log("File tidak ditemukan untuk dihapus: " . $transkripPath);
            }
        }

        // Delete surat_pernyataan (from berkasUser directory)
        if (!empty($files['surat_pernyataan'])) {
            $suratPath = $basePathBerkas . $files['surat_pernyataan'];
            if (file_exists($suratPath)) {
                if (@unlink($suratPath)) {
                    error_log("File berhasil dihapus: " . $suratPath);
                } else {
                    error_log("Gagal menghapus file: " . $suratPath);
                }
            } else {
                error_log("File tidak ditemukan untuk dihapus: " . $suratPath);
            }
        }
    }

}
