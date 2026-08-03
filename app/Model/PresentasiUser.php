<?php

namespace App\Model;
use App\Core\Model;
use PDO;
use \Exception;
class PresentasiUser extends Model {
    protected static $table = 'presentasi';
    protected $id;
    protected $id_mahasiswa;
    protected $judul;
    protected $makalah;
    protected $ppt;
    protected $is_revisi;
    protected $is_accepted;
    protected $absensi;
    protected $pptSize;
    protected $makalahSize;
    protected $fileMakalahAcc = "pdf";
    protected $filePptAcc = ["ppt", "pptx"];
    protected $maxMakalahSize = 2048 * 1024;
    protected $maxPptSize = 10240 * 1024;
    protected $keterangan;
    public function __construct(
        $id_mahasiswa = null,
        $judul = null,
        $makalah = null,
        $makalahSize = null,
        $ppt = null,
        $pptSize = null,
        $is_revisi = null,
        $is_accepted = null
    ) {
        if ($makalah === null && $makalahSize === null && $ppt === null && $pptSize === null) {
            $this->id_mahasiswa = $id_mahasiswa;
            $this->judul = $judul;
        } else if($judul === null && $is_revisi === null && $is_accepted === null) {
            $this->id_mahasiswa = $id_mahasiswa;
            $this->makalah = $makalah;
            $this->ppt = $ppt;
            $this->makalahSize = $makalahSize;
            $this->pptSize = $pptSize;

        } else if($judul === null && $makalah === null && $ppt === null && $makalahSize === null && $pptSize === null) {
            $this->id_mahasiswa = $id_mahasiswa;
            $this->is_revisi = $is_revisi;
            $this->is_accepted = $is_accepted;
        }
         else {
            $this->id_mahasiswa = $id_mahasiswa;
            $this->makalah = $makalah;
            $this->ppt = $ppt;
            $this->is_revisi = $is_revisi;
            $this->is_accepted = $is_accepted;
            $this->makalahSize = $makalahSize;
            $this->pptSize = $pptSize;
        }
    }

    /**
     * Simpan pengajuan judul.
     *
     * Perilaku riwayat: tiap pengajuan dicatat sebagai BARIS BARU, sehingga
     * judul yang pernah ditolak tetap tersimpan dan terlihat di tabel riwayat.
     * Baris "aktif" adalah yang terbaru (id terbesar) — lihat getAllPresentasi()
     * dan getValueForTable().
     *
     * Baris baru selalu dimulai dengan status MENUNGGU
     * (is_accepted = 0, is_revisi = 0, keterangan kosong), supaya keterangan
     * penolakan sebelumnya tidak ikut terbawa ke pengajuan yang baru.
     *
     * Judul hanya di-UPDATE bila pengajuan terakhir masih menunggu — artinya
     * mahasiswa sekadar mengoreksi ketikan sebelum dinilai admin, bukan
     * mengajukan ulang setelah ditolak.
     */
    public function saveJudul(PresentasiUser $presentasiUser) {
        $idMahasiswa = $this->getIdMahasiswa($presentasiUser->id_mahasiswa);
        if (!$idMahasiswa || !isset($idMahasiswa['id'])) {
            throw new Exception("Mahasiswa tidak ditemukan: " . var_export($idMahasiswa, true));
        }
        $idMahasiswa = $idMahasiswa['id'];

        // Ambil pengajuan TERAKHIR untuk menentukan update vs insert baru.
        $stmtCheck = self::getDB()->prepare(
            "SELECT id, is_accepted, is_revisi FROM " . static::$table . "
             WHERE id_mahasiswa = ? ORDER BY id DESC LIMIT 1"
        );
        $stmtCheck->bindParam(1, $idMahasiswa, PDO::PARAM_INT);
        $stmtCheck->execute();
        $last = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        $masihMenunggu = $last
            && (int) $last['is_accepted'] === 0
            && (int) $last['is_revisi'] === 0;

        if ($masihMenunggu) {
            // Belum dinilai admin -> cukup perbarui judulnya.
            $stmt = self::getDB()->prepare(
                "UPDATE " . static::$table . " SET judul = ? WHERE id = ?"
            );
            $stmt->bindParam(1, $presentasiUser->judul, PDO::PARAM_STR);
            $stmt->bindParam(2, $last['id'], PDO::PARAM_INT);
            return $stmt->execute();
        }

        // Belum pernah mengajukan, atau pengajuan terakhir sudah dinilai
        // (ditolak/diterima) -> catat sebagai pengajuan baru berstatus menunggu.
        $stmt = self::getDB()->prepare(
            "INSERT INTO " . static::$table . "
             (id_mahasiswa, judul, is_accepted, is_revisi, keterangan)
             VALUES (?, ?, 0, 0, '')"
        );
        $stmt->bindParam(1, $idMahasiswa, PDO::PARAM_INT);
        $stmt->bindParam(2, $presentasiUser->judul, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function updateMakalahAndPpt(PresentasiUser $presentasiUser) {
        $idMahasiswa = $this->getIdMahasiswa($presentasiUser->id_mahasiswa);
        if (!$idMahasiswa || !isset($idMahasiswa['id'])) {
            throw new Exception("Mahasiswa tidak ditemukan" );
        }
        $idMahasiswa = $idMahasiswa['id'];

        // Get old files to delete after successful update
        $oldFiles = $this->getOldPresentasiFiles($idMahasiswa);

        $query = "UPDATE " . static::$table . "
            SET makalah = ?, ppt = ? WHERE id_mahasiswa = ?";
        $stmt = self::getDB()->prepare($query);

        $filePpt = $this->getFilePpt($presentasiUser->ppt, $presentasiUser->pptSize);
        if (!$filePpt) {
            throw new Exception("Gagal memproses ppt");
        }
        $fileMakalah = $this->getFileMakalah($presentasiUser->makalah, $presentasiUser->makalahSize);
        if(!$fileMakalah) {
            throw new Exception("Gagal memproses makalah");
        }

        $stmt->bindParam(1, $fileMakalah, PDO::PARAM_STR);
        $stmt->bindParam(2, $filePpt, PDO::PARAM_STR);
        $stmt->bindParam(3, $idMahasiswa, PDO::PARAM_STR);

        $result = $stmt->execute();

        // Delete old files after successful update
        if($result && $oldFiles) {
            $this->deleteOldPresentasiFiles($oldFiles);
        }

        return $result;
    }
    
    private function getFilePpt($berkas, $berkasSize) {
        $fileExt = strtolower(pathinfo($_FILES['ppt']['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $this->filePptAcc)) {
            throw new Exception("Gunakan ekstensi PPT atau PPTX untuk file ppt.");
        }
    
        if ($berkasSize > $this->maxPptSize) {
            throw new Exception("Ukuran file terlalu besar.");
        }
    
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/pptUser/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); 
        }
    
        $newFileName = uniqid() . '.' . $fileExt;
    
        if (empty($berkas)) {
            throw new Exception("Path file sementara untuk ppt kosong.");
        }
        $destination = $uploadDir . $newFileName;    
        if (!move_uploaded_file($berkas, $destination)) {
            throw new Exception("Gagal memindahkan file ppt. Pastikan folder tujuan dapat diakses.");
        }
    
        return $newFileName;
    }
    
    private function getFileMakalah($berkas, $berkasSize) {
        $fileExt = strtolower(pathinfo($_FILES['makalah']['name'], PATHINFO_EXTENSION));
        if ($fileExt !== $this->fileMakalahAcc) {
            throw new Exception("Gunakan ekstensi pdf untuk file.");
        }
    
        if ($berkasSize > $this->maxMakalahSize) {
            throw new Exception("Ukuran file terlalu besar.");
        }
    
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/makalahUser/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); 
        }
    
        $newFileName = uniqid() . '.' . $fileExt;
    
        if (empty($berkas)) {
            throw new Exception("Path file sementara untuk makalah kosong.");
        }
    
        $destination = $uploadDir . $newFileName;
        if (!move_uploaded_file($berkas, $destination)) {
            throw new Exception("Gagal memindahkan file makalah. Pastikan folder tujuan dapat diakses.");
        }
    
        return $newFileName;
    }
    // getIdMahasiswa() dipindahkan ke App\Core\Model (induk) karena
    // isinya identik di 4 model. Lihat catatan kontrak return di sana.
    /**
     * Pengajuan AKTIF (terbaru) milik seorang mahasiswa.
     * ORDER BY id DESC penting: sejak riwayat disimpan per-baris, tanpa ini
     * yang terambil adalah pengajuan paling lama.
     */
    public function getAllPresentasi($id) {
        $idMahasiswa = $this->getIdMahasiswa($id);
        if (!$idMahasiswa || !isset($idMahasiswa['id'])) {
            return null;
        }
        $stmt = self::getDB()->prepare(
            "SELECT * FROM " . static::$table . "
             WHERE id_mahasiswa = ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->bindValue(1, $idMahasiswa['id'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * SELURUH riwayat pengajuan judul, terbaru di atas.
     * Mengembalikan array-of-rows (dulu hanya satu baris), sehingga judul yang
     * pernah ditolak tetap terlihat di tabel riwayat.
     */
    public function getValueForTable($id) {
        $idMahasiswa = $this->getIdMahasiswa($id);
        if (!$idMahasiswa || !isset($idMahasiswa['id'])) {
            return [];
        }
        $stmt = self::getDB()->prepare(
            "SELECT id, judul, is_revisi, is_accepted, created_at, keterangan
             FROM " . static::$table . "
             WHERE id_mahasiswa = ? ORDER BY id DESC"
        );
        $stmt->bindValue(1, $idMahasiswa['id'], PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Status penerimaan pengajuan TERBARU. */
    public function isAccepted($id) {
        $stmt = self::getDB()->prepare(
            "SELECT is_accepted FROM " . static::$table . "
             WHERE id_mahasiswa = ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            return null;
        }
        return $result['is_accepted'];
    }

    /**
     * Get old presentasi files before update/delete
     * @param int $idMahasiswa
     * @return array|null Array containing old file names
     */
    private function getOldPresentasiFiles($idMahasiswa) {
        $query = "SELECT makalah, ppt FROM " . static::$table . " WHERE id_mahasiswa = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $idMahasiswa);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete old presentasi files from filesystem
     * @param array $files Array containing file names to delete
     */
    private function deleteOldPresentasiFiles($files) {
        $basePathMakalah = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/makalahUser/';
        $basePathPpt = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/pptUser/';

        // Delete makalah
        if (!empty($files['makalah'])) {
            $makalahPath = $basePathMakalah . $files['makalah'];
            if (file_exists($makalahPath)) {
                if (@unlink($makalahPath)) {
                    error_log("File makalah berhasil dihapus: " . $makalahPath);
                } else {
                    error_log("Gagal menghapus file makalah: " . $makalahPath);
                }
            } else {
                error_log("File makalah tidak ditemukan untuk dihapus: " . $makalahPath);
            }
        }

        // Delete ppt
        if (!empty($files['ppt'])) {
            $pptPath = $basePathPpt . $files['ppt'];
            if (file_exists($pptPath)) {
                if (@unlink($pptPath)) {
                    error_log("File PPT berhasil dihapus: " . $pptPath);
                } else {
                    error_log("Gagal menghapus file PPT: " . $pptPath);
                }
            } else {
                error_log("File PPT tidak ditemukan untuk dihapus: " . $pptPath);
            }
        }
    }

}
