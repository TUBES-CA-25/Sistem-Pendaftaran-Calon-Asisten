<?php

namespace App\Controllers\Home;

use App\Core\Controller;
use App\Core\View;
use App\Controllers\user\DashboardUserController;
use App\Controllers\notifications\NotificationControllers;
use App\Controllers\Profile\ProfileController;
use App\Controllers\user\BerkasUserController;
use App\Controllers\user\BiodataUserController;
use App\Controllers\user\WawancaraController;
use App\Controllers\User\PresentasiUserController;
use App\Controllers\admin\DashboardAdminController;
use App\Controllers\user\MahasiswaController;
use App\Controllers\presentasi\RuanganController;
use App\Controllers\presentasi\JadwalPresentasiController;
use App\Controllers\user\AbsensiUserController;
use App\Controllers\Exam\NilaiAkhirController;
use App\Controllers\exam\ExamController;
use App\Core\Model;

class HomeController extends Controller
{
    public function index()
    {
        if ($this->isLoggedIn() && $this->getRole() == "User") {
            $data = $this->getSidebarData();
            View::render('main', 'Templates', $data);

        } else if ($this->isLoggedIn() && $this->getRole() == "Admin") {
            $data = $this->getSidebarData();
            $dashboardData = $this->getDashboardAdminData();
            $data = array_merge($data, $dashboardData);
            View::render('mainAdmin', 'Templates', $data);

        } else {
            View::render('index', 'login');
            exit();
        }
    }

    public function loadContent($page)
    {
        if (is_array($page)) {
            $page = $page['page'];
        }

        if ($this->getRole() == "Admin") {
            switch ($page) {
                case 'dashboard':
                    $data = $this->getDashboardAdminData();
                    View::render('dashboardAdmin', 'Templates', $data);
                    break;
                case 'ruangan':
                    $data = $this->getRuanganData();
                    View::render('ruangan', 'Templates', $data);
                    break;
                case 'lihatPeserta':
                    $data = $this->getDaftarPesertaData();
                    View::render('daftarPeserta', 'Templates', $data);
                    break;
                case 'daftarKehadiran':
                    $data = $this->getDaftarHadirData();
                    View::render('DaftarHadirPesertaAdmin', 'Templates', $data);
                    break;
                case 'presentasi':
                    $data = $this->getPresentasiAdminData();
                    View::render('presentasiAdmin', 'Templates', $data);
                    break;
                case 'tesTulis':
                    $data = $this->getTesTulisAdminData();
                    View::render('tesTulisAdmin', 'Templates', $data);
                    break;
                case 'uploadBerkas':
                    View::render('uploadBerkasAdmin', 'Templates');
                    break;
                case 'wawancara':
                    $data = $this->getWawancaraAdminData();
                    View::render('wawancaraAdmin', 'Templates', $data);
                    break;
                case 'profile':
                    $data = $this->getProfileData();
                    View::render('profileAdmin', 'Templates', $data);
                    break;
                case 'lihatnilai':
                    $data = $this->getNilaiAdminData();
                    View::render('DaftarNilaiTesTertulisAdmin', 'Templates', $data);
                    break;
                case 'tesTulis':
                case 'bankSoal':
                    $data = $this->getTesTulisAdminData();
                    View::render('tesTulisAdmin', 'Templates', $data);
                    break;
                case 'logout':
                    session_destroy();
                    $_SESSION = [];
                    echo "<script>window.location.href = '';</script>";
                    exit;
            }

        } else {
            switch ($page) {
                case 'dashboard':
                    $data = $this->getDashboardData();
                    View::render('dashboard', 'Templates', $data);
                    break;
                case 'biodata':
                    $data = $this->getBiodataData();
                    View::render('biodata', 'Templates', $data);
                    break;
                case 'pengumuman':
                    View::render('pengumuman', 'Templates');
                    break;
                case 'presentasi':
                    $data = $this->getPresentasiData();
                    View::render('presentasi', 'Templates', $data);
                    break;
                case 'tesTulis':
                    $data = $this->getTesTulisData();
                    View::render('tesTulis', 'Templates', $data);
                    break;
                case 'uploadBerkas':
                    $data = $this->getUploadBerkasData();
                    View::render('uploadBerkas', 'Templates', $data);
                    break;
                case 'wawancara':
                    $data = $this->getWawancaraData();
                    View::render('wawancara', 'Templates', $data);
                    break;
                case 'profile':
                    $data = $this->getProfileData();
                    View::render('profile', 'Templates', $data);
                    break;
                case 'editprofile':
                    $data = $this->getProfileData();
                    View::render('editprofile', 'Templates', $data);
                    break;
                case 'notifcation':
                    View::render('notification', 'Templates');
                    break;
            }
        }
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }
    private function getRole()
    {
        return $_SESSION['user']['role'];
    }

    /**
     * Data untuk sidebar
     */
    private function getSidebarData(): array
    {
        $user = ProfileController::viewUser();
        $photo = BerkasUserController::viewPhoto();
        return [
            'role' => $user['role'] ?? 'User',
            'userName' => $user['username'] ?? 'Guest',
            'photo' => '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . ($photo['foto'] ?? 'default.png')
        ];
    }

    /**
     * Mengambil semua data yang dibutuhkan untuk dashboard
     */
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
            }
        }

        return [
            'notifikasi' => NotificationControllers::getMessageById() ?? [],
            'tahapanSelesai' => DashboardUserController::getNumberTahapanSelesai(),
            'percentage' => DashboardUserController::getPercentage(),
            'jadwalPresentasiUser' => $jadwalPresentasiUser,
            'tahapan' => [
                ["1", "Lengkapi Biodata", DashboardUserController::getBiodataStatus(), "tahap ini"],
                ["2", "Lengkapi Berkas", DashboardUserController::getBerkasStatus(), "mensubmit berkas"],
                ["3", "Tes Tertulis", DashboardUserController::getAbsensiTesTertulis(), "tahap ini"],
                ["4", "Submit Judul Makalah dan PPT", DashboardUserController::getPptJudulAccStatus(), "submit judul presentasi"],
                ["5", "Submit Makalah dan PPT", DashboardUserController::getPptStatus(), "submit PPT dan makalah"],
                ["6", "Presentasi", DashboardUserController::getAbsensiPresentasi(), "tahap ini"],
                ["7", "Wawancara Asisten", DashboardUserController::getAbsensiWawancaraI(), "tahap ini"],
                ["8", "Wawancara Kepala Lab 1", DashboardUserController::getAbsensiWawancaraII(), "tahap ini"],
                ["9", "Wawancara Kepala Lab 2", DashboardUserController::getAbsensiWawancaraIII(), "tahap ini"],
            ]
        ];
    }

    /**
     * Data untuk biodata view
     */
    private function getBiodataData(): array
    {
        $biodata = ProfileController::viewBiodata();
        $user = ProfileController::viewUser();
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
            'isBiodataEmpty' => BiodataUserController::isEmpty()
        ];
    }

    /**
     * Data untuk profile view
     */
    private function getProfileData(): array
    {
        $biodata = ProfileController::viewBiodata();
        $user = ProfileController::viewUser();
        $photo = BerkasUserController::viewPhoto();
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
            'photo' => '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . ($photo['foto'] ?? 'default.png')
        ];
    }

    /**
     * Data untuk upload berkas view
     */
    private function getUploadBerkasData(): array
    {
        $biodata = ProfileController::viewBiodata();
        return [
            'res' => BerkasUserController::viewBerkas() ?? [],
            'nama' => $biodata['namaLengkap'] ?? 'Nama Lengkap',
            'biodataStatus' => DashboardUserController::getBiodataStatus(),
            'isBerkasEmpty' => BerkasUserController::isEmptyBerkas()
        ];
    }

    /**
     * Data untuk tes tulis view
     */
    private function getTesTulisData(): array
    {
        return [
            'absensiTesTertulis' => DashboardUserController::getAbsensiTesTertulis(),
            'berkasStatus' => DashboardUserController::getBerkasStatus(),
            'biodataStatus' => DashboardUserController::getBiodataStatus(),
            'activeBank' => ExamController::getActiveBank()
        ];
    }

    /**
     * Data untuk presentasi view
     */
    private function getPresentasiData(): array
    {
        return [
            'results' => PresentasiUserController::viewAll() ?? [],
            'biodataStatus' => DashboardUserController::getBiodataStatus(),
            'berkasStatus' => DashboardUserController::getBerkasStatus(),
            'absensiTesTertulis' => DashboardUserController::getAbsensiTesTertulis(),
            'pptStatus' => DashboardUserController::getPptStatus()
        ];
    }

    /**
     * Data untuk wawancara view
     */
    private function getWawancaraData(): array
    {
        return [
            'wawancara' => WawancaraController::getAllById() ?? []
        ];
    }

    // ==================== ADMIN DATA METHODS ====================

    /**
     * Data untuk dashboard admin
     */
    private function getDashboardAdminData(): array
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        return [
            'totalPendaftar' => DashboardAdminController::getTotalPendaftar(),
            'pendaftarLulus' => DashboardAdminController::getPendaftarLulus(),
            'pendaftarPending' => DashboardAdminController::getPendaftarPending(),
            'pendaftarGagal' => DashboardAdminController::getPendaftarGagal(),
            'statusKegiatan' => DashboardAdminController::getStatusKegiatan(),
            'kegiatanBulanIni' => DashboardAdminController::getKegiatanByMonth($currentYear, $currentMonth) ?? [],
            'jadwalPresentasiMendatang' => JadwalPresentasiController::getUpcomingJadwal(5)
        ];
    }

    /**
     * Data untuk ruangan view
     */
    private function getRuanganData(): array
    {
        return [
            'ruanganList' => RuanganController::viewAllRuangan() ?? []
        ];
    }

    /**
     * Data untuk daftar peserta view
     */
    private function getDaftarPesertaData(): array
    {
        $mahasiswa = MahasiswaController::viewAllMahasiswa() ?? [];
        
        // Root dir relative to this file (app/Controllers/Home) -> 3 levels up
        $imageDir = dirname(__DIR__, 3) . '/res/imageUser/';

        foreach ($mahasiswa as &$mhs) {
            // Check if foto is set and not empty
            if (!empty($mhs['foto'])) {
                $filePath = $imageDir . $mhs['foto'];
                if (!file_exists($filePath)) {
                    // File record exists in DB but not on disk -> Set to null to trigger default
                    $mhs['foto'] = null; 
                }
            }
        }
        unset($mhs); // Break reference

        return [
            'mahasiswaList' => $mahasiswa,
            'result' => $mahasiswa
        ];
    }

    /**
     * Data untuk daftar hadir view
     */
    private function getDaftarHadirData(): array
    {
        return [
            'absensiList' => AbsensiUserController::viewAbsensi() ?? [],
            'mahasiswaList' => MahasiswaController::viewAllMahasiswa() ?? []
        ];
    }

    /**
     * Data untuk presentasi admin view
     */
    private function getPresentasiAdminData(): array
    {
        return [
            'mahasiswaList' => PresentasiUserController::viewAllForAdmin() ?? [],
            'mahasiswaAccStatus' => PresentasiUserController::viewAllAccStatusForAdmin() ?? [],
            'ruanganList' => RuanganController::viewAllRuangan() ?? [],
            'jadwalPresentasi' => JadwalPresentasiController::getJadwalPresentasi() ?? []
        ];
    }

    /**
     * Data untuk tes tulis admin view
     */
    private function getTesTulisAdminData(): array
    {
        return [
            'allSoal' => ExamController::viewAllSoal() ?? []
        ];
    }

    /**
     * Data untuk wawancara admin view
     */
    private function getWawancaraAdminData(): array
    {
        return [
            'wawancara' => WawancaraController::getAll() ?? [],
            'mahasiswaList' => MahasiswaController::viewAllMahasiswa() ?? [],
            'ruanganList' => RuanganController::viewAllRuangan() ?? []
        ];
    }

    /**
     * Data untuk nilai admin view
     */
    private function getNilaiAdminData(): array
    {
        return [
            'nilai' => NilaiAkhirController::getAllNilaiAkhirMahasiswa() ?? []
        ];
    }
}
