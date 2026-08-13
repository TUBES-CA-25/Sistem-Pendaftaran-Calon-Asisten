<?php

namespace App\Model;
use App\Core\Model;

class Mahasiswa extends Model
{


    protected static $table = 'mahasiswa';
    protected static $tabelJurusan = 'jurusan';
    protected static $tabelKelas = 'kelas';
    protected $id;
    protected $idUser;
    protected $idJurusan;
    protected $jurusan;
    protected $kelas;
    protected $stambuk;
    protected $id_kelas;
    protected $namaLengkap;
    protected $alamat;
    protected $jenisKelamin;
    protected $tempatLahir;
    protected $tanggalLahir;
    protected $noHp;
    protected $fotoProfil;

    public function __construct(
        $idUser = null,
        $jurusan = null,
        $stambuk = null,
        $kelas = null,
        $namaLengkap = null,
        $alamat = null,
        $jenisKelamin = null,
        $tempatLahir = null,
        $tanggalLahir = null,
        $noHp = null
    ) {
        $this->idUser = $idUser;
        $this->jurusan = $jurusan;
        $this->stambuk = $stambuk;
        $this->kelas = $kelas;
        $this->namaLengkap = $namaLengkap;
        $this->alamat = $alamat;
        $this->jenisKelamin = $jenisKelamin;
        $this->tempatLahir = $tempatLahir;
        $this->tanggalLahir = $tanggalLahir;
        $this->noHp = $noHp;
    }

    public function getAll()
    {
        // Select ALL users (excluding Admins) and join their biodata (if any)
        $query = "SELECT u.id as id_user_real, u.username, u.stambuk as stambuk_user, m.*
                  FROM user u
                  LEFT JOIN " . static::$table . " m ON u.id = m.id_user
                  WHERE (u.role != 'Admin' OR u.role IS NULL)";

        $stmt = self::getDB()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach ($result as $stmt) {
            // Only fetch relations if Mahasiswa record exists (id is not null)
            $mahasiswaId = $stmt['id'] ?? null;

            // Skip users who haven't registered as mahasiswa (no biodata filled)
            if (!$mahasiswaId) {
                continue;
            }
            
            $berkas = $mahasiswaId ? $this->getBerkasMahasiswa($mahasiswaId) : [
                'foto' => null, 'cv' => null, 'transkrip_nilai' => null, 'surat_pernyataan' => null, 'accepted' => null
            ];
            
            $presentasi = $mahasiswaId ? $this->getPresentasiMahasiswa($mahasiswaId) : [
                'judul' => null, 'makalah' => null, 'ppt' => null, 'is_accepted' => null, 'is_revisi' => null
            ];

            // Resolve Name: Prefer Biodata Name, else Username
            $displayName = $stmt['nama_lengkap'] ?? $stmt['username'];

            // Resolve Stambuk: Prefer Biodata Stambuk, else User Stambuk
            $displayStambuk = $stmt['stambuk'] ?? ($stmt['stambuk_user'] ?? '-');

            $data[] = [
                'id' => $stmt['id'], // Can be null if not yet registered in mahasiswa table
                'idUser' => $stmt['id_user_real'],
                'nama_lengkap' => $displayName,
                'stambuk' => $displayStambuk,
                'jurusan' => isset($stmt['id_jurusan']) ? ($this->getJurusan($stmt['id_jurusan'])['nama'] ?? null) : null,
                'kelas' => isset($stmt['id_kelas']) ? ($this->getKelas($stmt['id_kelas'])['nama'] ?? null) : null,
                'alamat' => $stmt['alamat'] ?? null,
                'notelp' => $stmt['no_telp'] ?? null,
                'tempat_lahir' => $stmt['tempat_lahir'] ?? null,
                'tanggal_lahir' => $stmt['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $stmt['jenis_kelamin'] ?? null,
                'foto_profil' => $stmt['foto_profil'] ?? null,
                'judul_presentasi' => $presentasi['judul'] ?? null,
                'berkas' => [
                    'foto' => $berkas['foto'],
                    'cv' => $berkas['cv'],
                    'transkrip_nilai' => $berkas['transkrip_nilai'],
                    'surat_pernyataan' => $berkas['surat_pernyataan'],
                    'accepted' => $berkas['accepted'] ?? null
                ],
                'presentasi' => [
                    'judul' => $presentasi['judul'] ?? null,
                    'makalah' => $presentasi['makalah'] ?? null,
                    'ppt' => $presentasi['ppt'] ?? null,
                    'is_accepted' => $presentasi['is_accepted'] ?? null,
                    'is_revisi' => $presentasi['is_revisi'] ?? null
                ],
                'status_akhir' => $stmt['status_akhir'] ?? 'Pending'
            ];
        }

        return $data;
    }
    public function deleteMahasiswaById($mahasiswaId)
    {
        $pdo = self::getDB();
        try {
            $pdo->beginTransaction();

            // Get berkas files before deletion
            $berkasQuery = "SELECT foto, cv, transkrip_nilai, surat_pernyataan FROM berkas_mahasiswa WHERE id_mahasiswa = :id";
            $berkasStmt = $pdo->prepare($berkasQuery);
            $berkasStmt->bindParam(':id', $mahasiswaId);
            $berkasStmt->execute();
            $berkasFiles = $berkasStmt->fetch(\PDO::FETCH_ASSOC);

            // Get presentasi files before deletion
            $presentasiQuery = "SELECT makalah, ppt FROM presentasi WHERE id_mahasiswa = :id";
            $presentasiStmt = $pdo->prepare($presentasiQuery);
            $presentasiStmt->bindParam(':id', $mahasiswaId);
            $presentasiStmt->execute();
            $presentasiFiles = $presentasiStmt->fetch(\PDO::FETCH_ASSOC);

            // Get foto_profil from mahasiswa before deletion
            $fotoQuery = "SELECT foto_profil FROM " . static::$table . " WHERE id = :id";
            $fotoStmt = $pdo->prepare($fotoQuery);
            $fotoStmt->bindParam(':id', $mahasiswaId);
            $fotoStmt->execute();
            $mahasiswaData = $fotoStmt->fetch(\PDO::FETCH_ASSOC);

            // 1. Delete Berkas
            $query = "DELETE FROM berkas_mahasiswa WHERE id_mahasiswa = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $mahasiswaId);
            $stmt->execute();

            // 2. Delete Presentasi
            $query = "DELETE FROM presentasi WHERE id_mahasiswa = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $mahasiswaId);
            $stmt->execute();

            // 3. Delete Jawaban
            $query = "DELETE FROM jawaban WHERE id_mahasiswa = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $mahasiswaId);
            $stmt->execute();

            // 4. Delete Nilai Akhir
            $query = "DELETE FROM nilai_akhir WHERE id_mahasiswa = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $mahasiswaId);
            $stmt->execute();

            // 5. Delete Absensi
            $query = "DELETE FROM absensi WHERE id_mahasiswa = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $mahasiswaId);
            $stmt->execute();

            // 6. Delete Notifikasi
            $query = "DELETE FROM notifikasi WHERE id_mahasiswa = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $mahasiswaId);
            $stmt->execute();

            // 7. Finally Delete Mahasiswa
            $query = "DELETE FROM " . static::$table . " WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $mahasiswaId);
            $stmt->execute();

            $pdo->commit();

            // After successful commit, delete all physical files
            $this->deleteAllMahasiswaFiles($berkasFiles, $presentasiFiles, $mahasiswaData);

        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Delete all files related to a mahasiswa
     * @param array|null $berkasFiles Berkas files (foto, cv, transkrip, surat)
     * @param array|null $presentasiFiles Presentasi files (makalah, ppt)
     * @param array|null $mahasiswaData Mahasiswa data (foto_profil)
     */
    private function deleteAllMahasiswaFiles($berkasFiles, $presentasiFiles, $mahasiswaData) {
        $basePathImage = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/profile/';
        $basePathBerkas = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser/';
        $basePathMakalah = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/makalahUser/';
        $basePathPpt = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/pptUser/';
        $basePathProfile = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/profile/';

        // Delete berkas files
        if ($berkasFiles) {
            if (!empty($berkasFiles['foto'])) {
                $fotoPath = $basePathImage . $berkasFiles['foto'];
                if (file_exists($fotoPath)) {
                    if (@unlink($fotoPath)) {
                        error_log("Foto berkas berhasil dihapus saat delete mahasiswa: " . $fotoPath);
                    } else {
                        error_log("Gagal menghapus foto berkas saat delete mahasiswa: " . $fotoPath);
                    }
                }
            }
            if (!empty($berkasFiles['cv'])) {
                $cvPath = $basePathBerkas . $berkasFiles['cv'];
                if (file_exists($cvPath)) {
                    if (@unlink($cvPath)) {
                        error_log("CV berhasil dihapus saat delete mahasiswa: " . $cvPath);
                    } else {
                        error_log("Gagal menghapus CV saat delete mahasiswa: " . $cvPath);
                    }
                }
            }
            if (!empty($berkasFiles['transkrip_nilai'])) {
                $transkripPath = $basePathBerkas . $berkasFiles['transkrip_nilai'];
                if (file_exists($transkripPath)) {
                    if (@unlink($transkripPath)) {
                        error_log("Transkrip berhasil dihapus saat delete mahasiswa: " . $transkripPath);
                    } else {
                        error_log("Gagal menghapus transkrip saat delete mahasiswa: " . $transkripPath);
                    }
                }
            }
            if (!empty($berkasFiles['surat_pernyataan'])) {
                $suratPath = $basePathBerkas . $berkasFiles['surat_pernyataan'];
                if (file_exists($suratPath)) {
                    if (@unlink($suratPath)) {
                        error_log("Surat pernyataan berhasil dihapus saat delete mahasiswa: " . $suratPath);
                    } else {
                        error_log("Gagal menghapus surat pernyataan saat delete mahasiswa: " . $suratPath);
                    }
                }
            }
        }

        // Delete presentasi files
        if ($presentasiFiles) {
            if (!empty($presentasiFiles['makalah'])) {
                $makalahPath = $basePathMakalah . $presentasiFiles['makalah'];
                if (file_exists($makalahPath)) {
                    if (@unlink($makalahPath)) {
                        error_log("Makalah berhasil dihapus saat delete mahasiswa: " . $makalahPath);
                    } else {
                        error_log("Gagal menghapus makalah saat delete mahasiswa: " . $makalahPath);
                    }
                }
            }
            if (!empty($presentasiFiles['ppt'])) {
                $pptPath = $basePathPpt . $presentasiFiles['ppt'];
                if (file_exists($pptPath)) {
                    if (@unlink($pptPath)) {
                        error_log("PPT berhasil dihapus saat delete mahasiswa: " . $pptPath);
                    } else {
                        error_log("Gagal menghapus PPT saat delete mahasiswa: " . $pptPath);
                    }
                }
            }
        }

        // Delete profile photo
        if ($mahasiswaData && !empty($mahasiswaData['foto_profil'])) {
            $profilePath = $basePathProfile . $mahasiswaData['foto_profil'];
            if (file_exists($profilePath)) {
                if (@unlink($profilePath)) {
                    error_log("Foto profil berhasil dihapus saat delete mahasiswa: " . $profilePath);
                } else {
                    error_log("Gagal menghapus foto profil saat delete mahasiswa: " . $profilePath);
                }
            }
        }
    }

    public function updateBiodataMahasiswaById(
        $id,
        $nama,
        $stambuk,
        $jurusan,
        $jenisKelamin,
        $kelas,
        $alamat,
        $tempatLahir,
        $tanggalLahir,
    ) {

        $query = "UPDATE " . static::$table .
            " SET nama_lengkap = :nama, 
            stambuk = :stambuk, 
            id_jurusan = :jurusan, 
            jenis_kelamin = :jenis_kelamin, 
            id_kelas = :kelas, 
            alamat = :alamat, 
            tempat_lahir = :tempat_lahir, 
            tanggal_lahir = :tanggal_lahir 
            WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':stambuk', $stambuk);
        $stmt->bindParam(':jurusan', $jurusan);
        $stmt->bindParam(':jenis_kelamin', $jenisKelamin);
        $stmt->bindParam(':kelas', $kelas);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':tempat_lahir', $tempatLahir);
        $stmt->bindParam(':tanggal_lahir', $tanggalLahir);
        $stmt->execute();
    }

    public function updateStatusAkhir($id, $status)
    {
        $query = "UPDATE " . static::$table . " SET status_akhir = :status WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getBerkasMahasiswa($mahasiswaId)
    {
        $query = "SELECT foto, cv, transkrip_nilai, surat_pernyataan, accepted FROM berkas_mahasiswa WHERE id_mahasiswa = :mahasiswa_id ORDER BY id DESC LIMIT 1";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':mahasiswa_id', $mahasiswaId);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result ?: [
            'foto' => null,
            'cv' => null,
            'transkrip_nilai' => null,
            'surat_pernyataan' => null,
            'accepted' => null
        ];
    }

    /**
     * Get presentasi data for mahasiswa
     */
    public function getPresentasiMahasiswa($mahasiswaId)
    {
        $query = "SELECT judul, makalah, ppt, is_accepted, is_revisi FROM presentasi WHERE id_mahasiswa = :mahasiswa_id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':mahasiswa_id', $mahasiswaId);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result ?: [
            'judul' => null,
            'makalah' => null,
            'ppt' => null,
            'is_accepted' => null,
            'is_revisi' => null
        ];
    }

    private function getJurusan($id)
    {
        $query = "SELECT nama FROM jurusan WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getKelas($id)
    {
        $query = "SELECT nama FROM kelas WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function getMahasiswaId($id)
    {
        $query = "SELECT * FROM " . static::$table . " WHERE id_user = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get mahasiswa detail by mahasiswa ID
     */
    public function getMahasiswaById($mahasiswaId)
    {
        $query = "SELECT * FROM " . static::$table . " WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $mahasiswaId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        $berkas = $this->getBerkasMahasiswa($result['id']);
        $presentasi = $this->getPresentasiMahasiswa($result['id']);

        return [
            'id' => $result['id'],
            'idUser' => $result['id_user'],
            'nama_lengkap' => $result['nama_lengkap'],
            'stambuk' => $result['stambuk'],
            'jurusan' => $this->getJurusan($result['id_jurusan'])['nama'] ?? null,
            'id_jurusan' => $result['id_jurusan'],
            'kelas' => $this->getKelas($result['id_kelas'])['nama'] ?? null,
            'id_kelas' => $result['id_kelas'],
            'alamat' => $result['alamat'],
            'notelp' => $result['no_telp'],
            'tempat_lahir' => $result['tempat_lahir'],
            'tanggal_lahir' => $result['tanggal_lahir'],
            'jenis_kelamin' => $result['jenis_kelamin'],
            'foto_profil' => $result['foto_profil'] ?? null,
            'judul_presentasi' => $presentasi['judul'] ?? null,
            'berkas' => [
                'foto' => $berkas['foto'],
                'cv' => $berkas['cv'],
                'transkrip_nilai' => $berkas['transkrip_nilai'],
                'surat_pernyataan' => $berkas['surat_pernyataan'],
                'accepted' => $berkas['accepted'] ?? null
            ],
            'presentasi' => [
                'judul' => $presentasi['judul'] ?? null,
                'makalah' => $presentasi['makalah'] ?? null,
                'ppt' => $presentasi['ppt'] ?? null,
                'is_accepted' => $presentasi['is_accepted'] ?? null,
                'is_revisi' => $presentasi['is_revisi'] ?? null
            ]
        ];
    }
    public static function create($idUser, $stambuk, $nama)
    {
        $query = "INSERT INTO " . static::$table . " (id_user, stambuk, nama_lengkap) VALUES (:id_user, :stambuk, :nama)";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindValue(':id_user', $idUser);
        $stmt->bindValue(':stambuk', $stambuk);
        $stmt->bindValue(':nama', $nama);
        $stmt->execute();
        
        // Return the newly created mahasiswa ID
        return self::getDB()->lastInsertId();
    }
    public function updateProfilePhoto($idUser, $filename)
    {
        // Get old photo to delete after successful update
        $oldPhotoQuery = "SELECT foto_profil FROM " . static::$table . " WHERE id_user = :id";
        $stmtOld = self::getDB()->prepare($oldPhotoQuery);
        $stmtOld->bindParam(':id', $idUser);
        $stmtOld->execute();
        $oldData = $stmtOld->fetch(\PDO::FETCH_ASSOC);

        $query = "UPDATE " . static::$table . " SET foto_profil = :foto WHERE id_user = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':foto', $filename);
        $stmt->bindParam(':id', $idUser);
        $result = $stmt->execute();

        // Delete old photo after successful update
        if ($result && $oldData && !empty($oldData['foto_profil'])) {
            $basePath = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/profile/';
            $oldPhotoPath = $basePath . $oldData['foto_profil'];
            if (file_exists($oldPhotoPath)) {
                if (@unlink($oldPhotoPath)) {
                    error_log("Foto profil berhasil dihapus: " . $oldPhotoPath);
                } else {
                    error_log("Gagal menghapus foto profil: " . $oldPhotoPath);
                }
            } else {
                error_log("Foto profil tidak ditemukan untuk dihapus: " . $oldPhotoPath);
            }
        }

        return $stmt->rowCount();
    }

    /**
     * Get students with biodata who are NOT scheduled for Tes Tertulis
     */
    public static function getAvailableForTesTulis() {
        $sql = "SELECT m.id, m.nama_lengkap, m.stambuk 
                FROM mahasiswa m
                LEFT JOIN wawancara w ON m.id = w.id_mahasiswa AND w.jenis_wawancara LIKE 'Tes Tertulis%'
                WHERE w.id IS NULL
                ORDER BY m.nama_lengkap ASC";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get students who have completed Tes Tertulis (for Presentasi scheduling)
     */
    public static function getAvailableForPresentasi() {
        $sql = "SELECT m.id, m.nama_lengkap, m.stambuk
                FROM mahasiswa m
                INNER JOIN absensi a ON m.id = a.id_mahasiswa
                WHERE a.absensi_tes_tertulis != 'Alpha'
                ORDER BY m.nama_lengkap ASC";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get students who have completed Presentasi (for Wawancara scheduling)
     */
    public static function getAvailableForWawancara() {
        // Wawancara adalah tahap KETIGA, jadi dua tahap sebelumnya harus
        // tuntas: hadir di tes tertulis DAN hadir di presentasi.
        //
        // Perbandingannya '= Hadir', bukan "!= 'Alpha'" seperti sebelumnya.
        // Nilai default kolom absensi adalah '-' (belum ditandai), sehingga
        // pembandingan negatif meloloskan peserta yang belum diabsen sama
        // sekali - persis yang ingin dicegah aturan urutan ini.
        //
        // Peserta yang SUDAH punya jadwal wawancara juga disembunyikan supaya
        // tidak terjadwal dua kali. Baris 'Tes Tertulis%' dikecualikan dari
        // pengecekan itu karena tabel `wawancara` menampung keduanya.
        // Kolom sudah_lab1 / sudah_lab2 menandai wawancara yang SUDAH
        // dijadwalkan untuk peserta ini.
        //
        // Peserta tidak lagi disembunyikan begitu punya satu wawancara: Lab I
        // dan Lab II adalah dua tahap terpisah yang dijalani berurutan. Dulu
        // subquery di sini mengecualikan siapa pun yang punya wawancara apa
        // pun, sehingga peserta yang selesai Lab I tidak pernah bisa
        // dijadwalkan Lab II.
        //
        // Penyaringan akhir dilakukan di sisi antarmuka sesuai jenis yang
        // sedang dipilih admin, dan divalidasi ulang saat menyimpan.
        $sql = "SELECT m.id, m.nama_lengkap, m.stambuk,
                       MAX(w.jenis_wawancara LIKE '%lab I')  AS sudah_lab1,
                       MAX(w.jenis_wawancara LIKE '%lab II') AS sudah_lab2
                FROM mahasiswa m
                INNER JOIN absensi a ON m.id = a.id_mahasiswa
                LEFT JOIN wawancara w ON w.id_mahasiswa = m.id
                WHERE a.absensi_tes_tertulis = 'Hadir'
                  AND a.absensi_presentasi = 'Hadir'
                GROUP BY m.id, m.nama_lengkap, m.stambuk
                HAVING sudah_lab1 = 0 OR sudah_lab2 = 0
                ORDER BY m.nama_lengkap ASC";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Memeriksa apakah peserta sudah menuntaskan tahap-tahap sebelumnya.
     *
     * Dropdown di antarmuka memang sudah tersaring, tetapi endpoint bisa
     * dipanggil langsung tanpa lewat halaman - jadi urutan tahap tetap harus
     * ditegakkan di server. Ini pemeriksaan terakhir sebelum data disimpan.
     *
     * Kehadiran ('Hadir') dipakai sebagai bukti "sudah mengerjakan"; punya
     * jadwal saja tidak cukup karena peserta bisa terjadwal lalu tidak datang.
     * Nilai default kolom absensi adalah '-', sehingga perbandingannya harus
     * positif ('= Hadir') - bukan negatif seperti "!= 'Alpha'" yang akan
     * meloloskan peserta yang belum diabsen sama sekali.
     *
     * @param  int    $idMahasiswa
     * @param  string $tahap 'presentasi' (butuh tes tertulis) atau
     *                       'wawancara' (butuh tes tertulis + presentasi)
     * @return string|null  null bila memenuhi syarat, atau alasan penolakan.
     */
    public static function alasanBelumBolehTahap(int $idMahasiswa, string $tahap): ?string
    {
        $sql = "SELECT m.nama_lengkap,
                       a.absensi_tes_tertulis,
                       a.absensi_presentasi
                FROM mahasiswa m
                LEFT JOIN absensi a ON a.id_mahasiswa = m.id
                WHERE m.id = ?";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute([$idMahasiswa]);
        $baris = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$baris) {
            return 'Data peserta tidak ditemukan';
        }

        $nama = $baris['nama_lengkap'] ?: 'Peserta';

        if (($baris['absensi_tes_tertulis'] ?? '') !== 'Hadir') {
            return $nama . ' belum mengerjakan tes tertulis';
        }

        if ($tahap === 'wawancara' && ($baris['absensi_presentasi'] ?? '') !== 'Hadir') {
            return $nama . ' belum mengikuti presentasi';
        }

        return null;
    }
}
