<?php

namespace App\Controllers\Concerns;

use App\Controllers\User\BerkasController;
use App\Controllers\User\BiodataController;
use App\Controllers\User\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\Admin\JadwalPresentasiController;
use App\Controllers\Admin\JadwalWawancaraController;
use App\Core\Model;
use App\Controllers\NotifikasiController;
use App\Controllers\User\PresentasiUserController;
use App\Controllers\User\ProfilController;
use App\Controllers\User\TesTulisController;

/**
 * Penyedia data untuk halaman sisi USER (dashboard, biodata, berkas, ujian, presentasi, wawancara).
 *
 * Dipisah dari HomeController agar tiap berkas tetap pendek dan mudah dirawat.
 * Isi method TIDAK diubah - hanya dipindahkan.
 */
trait ProvidesUserData
{
    private function getDashboardData(): array
    {
        // Get mahasiswa ID for current user
        $jadwalPresentasiUser = null;
        if (isset($_SESSION['user']['id'])) {
            $id_user = $_SESSION['user']['id'];
            $sql = "SELECT id FROM mahasiswa WHERE id_user = ?";
            $stmt = Model::getDB()->prepare($sql);
            $stmt->bindParam(1, $id_user, \PDO::PARAM_INT);
            $stmt->execute();
            $mahasiswa = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($mahasiswa) {
                $jadwalPresentasiUser = JadwalPresentasiController::getJadwalByMahasiswaId($mahasiswa['id']);

                // Format schedule dates
                if ($jadwalPresentasiUser && is_array($jadwalPresentasiUser)) {
                    if (isset($jadwalPresentasiUser['tanggal'])) {
                        $jadwalPresentasiUser['formattedDate'] = $this->formatDate($jadwalPresentasiUser['tanggal']);
                    }
                    if (isset($jadwalPresentasiUser['waktu'])) {
                        $jadwalPresentasiUser['formattedTime'] = $this->formatTime($jadwalPresentasiUser['waktu']);
                    }
                }
            }
        }

        // Tambahkan data biodata, user, dan photo
        $biodata = ProfilController::viewBiodata();
        $user = ProfilController::viewUser();

        // Updated Logic: Fetch Profile Photo specifically
        $mahasiswaModel = new \App\Model\Mahasiswa();
        $mahasiswa = $mahasiswaModel->getMahasiswaId($_SESSION['user']['id']);
        $photoName = $mahasiswa['foto_profil'] ?? 'default.png';
        $photoPath = $this->getUserPhotoPath($photoName);

        // Format profile display
        $profileDisplay = $this->formatProfileDisplay($biodata, $user, $photoName);

        // Calculate progress
        $tahapanSelesai = DashboardController::getMajorStagesSelesai();
        $progress = $this->calculateProgress($tahapanSelesai);

        // Tambahkan data dokumen/berkas
        $berkas = BerkasController::viewBerkas();
        $dokumen = $this->getDokumenStatus($berkas);

        return [
            'notifikasi' => NotifikasiController::getMessageById() ?? [],
            'tahapanSelesai' => $tahapanSelesai,
            // Timeline bertanggal; tanggalnya diatur admin lewat dashboard admin.
            'timelineSeleksi' => \App\Model\DashboardUser::getTimelineSeleksi($tahapanSelesai),
            'percentage' => $progress['percentage'],
            'stepProgress' => $progress['percentage'],
            'jadwalPresentasiUser' => $jadwalPresentasiUser,
            'tahapan' => [
                ["1", "Pemberkasan", $tahapanSelesai >= 1, "tahap ini"],
                ["2", "Tes Seleksi", $tahapanSelesai >= 2, "tahap ini"],
                ["3", "Presentasi", $tahapanSelesai >= 3, "tahap ini"],
                ["4", "Wawancara", $tahapanSelesai >= 4, "tahap ini"],
                ["5", "Pengumuman", $tahapanSelesai >= 5, "tahap ini"],
            ],
            'biodata' => $biodata,
            'user' => $user,
            'photo' => $photoPath,
            'profileDisplay' => $profileDisplay,
            'dokumen' => $dokumen,
            'graduationStatus' => DashboardController::getGraduationStatus(),
            'isPengumumanOpen' => DashboardController::isPengumumanOpen(),
            'currentActivities' => DashboardController::getKegiatanByMonth(),
            'isBiodataEmpty' => BiodataController::isEmpty()
        ];
    }

    /**
     * Get status dokumen/berkas user
     */

    private function getDokumenStatus($berkas): array
    {
        return [
            [
                'nama' => 'Ijazah Terakhir',
                'status' => $berkas['statusIjazah'] ?? 'Menunggu',
                'jumlah' => 1
            ],
            [
                'nama' => 'Curriculum Vitae (CV)',
                'status' => $berkas['statusCV'] ?? 'Menunggu',
                'jumlah' => 1
            ],
            [
                'nama' => 'Kartu Tanda Mahasiswa (KTM)',
                'status' => $berkas['statusKTM'] ?? 'Menunggu',
                'jumlah' => 1
            ],
            [
                'nama' => 'Transkrip Nilai',
                'status' => $berkas['statusTranskrip'] ?? 'Menunggu',
                'jumlah' => 1
            ],
            [
                'nama' => 'Surat Keterangan Sehat',
                'status' => $berkas['statusSuratSehat'] ?? 'Menunggu',
                'jumlah' => 1
            ]
        ];
    }

    /**
     * Data untuk biodata view
     */

    private function getBiodataData(): array
    {
        $biodata = ProfilController::viewBiodata();
        $user = ProfilController::viewUser();
        
        $mahasiswaModel = new \App\Model\Mahasiswa();
        $mahasiswa = $mahasiswaModel->getMahasiswaId($_SESSION['user']['id'] ?? 1);
        $photoName = $mahasiswa['foto_profil'] ?? 'default.png';
        $photoPath = $this->getUserPhotoPath($photoName);

        return [
            'nama' => $biodata['namaLengkap'] ?? 'Nama Lengkap',
            'stambuk' => $user['stambuk'] ?? '',
            'jurusan' => $biodata['jurusan'] ?? 'Jurusan',
            'alamat' => $biodata['alamat'] ?? 'Alamat',
            'kelas' => $biodata['kelas'] ?? 'Kelas',
            'jenisKelamin' => $biodata['jenisKelamin'] ?? 'Jenis Kelamin',
            'tempatLahir' => $biodata['tempatLahir'] ?? 'Tempat Lahir',
            'tanggalLahir' => $biodata['tanggalLahir'] ?? 'Tanggal Lahir',
            'noHp' => $biodata['noHp'] ?? 'No Telephone',
            'photo' => $photoPath,
            'isBiodataEmpty' => BiodataController::isEmpty()
        ];
    }

    /**
     * Data untuk profile view
     */

    private function getProfileData(): array
    {
        $biodata = ProfilController::viewBiodata();
        $user = ProfilController::viewUser();
        
        $role = $user['role'] ?? 'User';
        
        if ($role === 'Admin') {
            $photoPath = HomeController::getAdminPhoto($_SESSION['user']['id']);
        } else {
             $mahasiswaModel = new \App\Model\Mahasiswa();
             $mahasiswa = $mahasiswaModel->getMahasiswaId($_SESSION['user']['id']);
             $photoName = $mahasiswa['foto_profil'] ?? 'default.png';
             $photoPath = $this->getUserPhotoPath($photoName);
        }

        return [
            'userName' => $user['username'] ?? 'Guest',
            'nama' => $biodata['namaLengkap'] ?? 'Nama Lengkap',
            'stambuk' => $user['stambuk'] ?? '',
            'jurusan' => $biodata['jurusan'] ?? 'Jurusan',
            'alamat' => $biodata['alamat'] ?? 'Alamat',
            'kelas' => $biodata['kelas'] ?? 'Kelas',
            'jenisKelamin' => $biodata['jenisKelamin'] ?? 'Jenis Kelamin',
            'tempatLahir' => $biodata['tempatLahir'] ?? 'Tempat Lahir',
            'tanggalLahir' => $biodata['tanggalLahir'] ?? 'Tanggal Lahir',
            'noHp' => $biodata['noHp'] ?? 'No Telephone',
            'photo' => $photoPath
        ];
    }

    /**
     * Data untuk upload berkas view
     */

    private function getUploadBerkasData(): array
    {
        $biodata = ProfilController::viewBiodata();
        return [
            'res' => BerkasController::viewBerkas() ?? [],
            'nama' => $biodata['namaLengkap'] ?? 'Nama Lengkap',
            'biodataStatus' => DashboardController::getBiodataStatus(),
            'isBerkasEmpty' => BerkasController::isEmptyBerkas()
        ];
    }

    /**
     * Data untuk tes tulis view
     */

    private function getTesTulisData(): array
    {
        $absensiTesTertulis = DashboardController::getAbsensiTesTertulis();
        $berkasStatus = DashboardController::getBerkasStatus();
        $biodataStatus = DashboardController::getBiodataStatus();

        // Check access
        $accessCheck = $this->canAccessExam(
            $absensiTesTertulis,
            $berkasStatus,
            $biodataStatus
        );

        return [
            'absensiTesTertulis' => $absensiTesTertulis,
            'berkasStatus' => $berkasStatus,
            'biodataStatus' => $biodataStatus,
            'canAccess' => $accessCheck['allowed'],
            'accessReason' => $accessCheck['reason'],
            'accessMessage' => $accessCheck['message'],
            'activeBank' => TesTulisController::getActiveBank()
        ];
    }

    /**
     * Data untuk presentasi view
     */

    private function getPresentasiData(): array
    {
        return [
            'results' => PresentasiUserController::viewAll() ?? [],
            'biodataStatus' => DashboardController::getBiodataStatus(),
            'berkasStatus' => DashboardController::getBerkasStatus(),
            'absensiTesTertulis' => DashboardController::getAbsensiTesTertulis(),
            'pptStatus' => DashboardController::getPptStatus()
        ];
    }

    /**
     * Data untuk wawancara view
     */

    private function getWawancaraData(): array
    {
        return [
            'wawancara' => JadwalWawancaraController::getAllById() ?? []
        ];
    }

    // ==================== ADMIN DATA METHODS ====================

    /**
     * Data untuk dashboard admin
     */
}
