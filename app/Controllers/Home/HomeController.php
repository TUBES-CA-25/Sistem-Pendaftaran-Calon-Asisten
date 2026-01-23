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
            // FIXED: Merge Dashboard Data for initial load!
            $dashboardData = $this->getDashboardData();
            $data = array_merge($data, $dashboardData);
            View::render('main', 'layouts', $data);

        } else if ($this->isLoggedIn() && $this->getRole() == "Admin") {
            $data = $this->getSidebarData();
            $dashboardData = $this->getDashboardAdminData();
            $data = array_merge($data, $dashboardData);
            View::render('mainAdmin', 'layouts', $data);

        } else {
            View::render('index', 'auth');
            exit();
        }
    }

    public function loadContent($page)
    {
        if (is_array($page)) {
            $page = $page['page'];
        }

        // Detect if AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($isAjax) {
            // AJAX: Return only content
            $this->renderPageContent($page);
        } else {
            // Direct URL: Return full layout with content
            $this->renderFullPage($page);
        }
    }

    /**
     * Render full page with layout (for direct URL access)
     */
    private function renderFullPage($page)
    {
        $data = $this->getSidebarData();
        $data['initialPage'] = $page;

        // Get page-specific data
        $pageData = $this->getPageData($page);
        $data = array_merge($data, $pageData);

        if ($this->getRole() == "Admin") {
            View::render('mainAdmin', 'layouts', $data);
        } else {
            View::render('main', 'layouts', $data);
        }
    }

    /**
     * Get data for specific page
     */
    private function getPageData($page): array
    {
        if ($this->getRole() == "Admin") {
            switch ($page) {
                case 'dashboard': return $this->getDashboardAdminData();
                case 'ruangan': return $this->getRuanganData();
                case 'lihatPeserta': return $this->getDaftarPesertaData();
                case 'daftarKehadiran': return $this->getDaftarHadirData();
                case 'presentasi': return $this->getPresentasiAdminData();
                case 'tesTulis':
                case 'bankSoal': return $this->getTesTulisAdminData();
                case 'wawancara': return $this->getWawancaraAdminData();
                case 'profile': return $this->getProfileData();
                case 'lihatnilai': return $this->getNilaiAdminData();
                default: return [];
            }
        } else {
            switch ($page) {
                case 'dashboard': return $this->getDashboardData();
                case 'biodata': return $this->getBiodataData();
                case 'presentasi': return $this->getPresentasiData();
                case 'tesTulis': return $this->getTesTulisData();
                case 'uploadBerkas': return $this->getUploadBerkasData();
                case 'wawancara': return $this->getWawancaraData();
                case 'profile':
                case 'editprofile': return $this->getProfileData();
                default: return [];
            }
        }
    }

    /**
     * Render only page content (for AJAX requests)
     */
    private function renderPageContent($page)
    {
        if ($this->getRole() == "Admin") {
            $sidebarData = $this->getSidebarData(); // Fetch once
            
            switch ($page) {
                case 'dashboard':
                    $data = array_merge($sidebarData, $this->getDashboardAdminData());
                    View::render('index', 'admin/dashboard', $data);
                    break;
                case 'ruangan':
                    $data = array_merge($sidebarData, $this->getRuanganData());
                    View::render('index', 'admin/rooms', $data);
                    break;
                case 'lihatPeserta':
                    $data = array_merge($sidebarData, $this->getDaftarPesertaData());
                    View::render('index', 'admin/participants', $data);
                    break;
                case 'daftarKehadiran':
                    $data = array_merge($sidebarData, $this->getDaftarHadirData());
                    View::render('index', 'admin/attendance', $data);
                    break;
                case 'presentasi':
                    $data = array_merge($sidebarData, $this->getPresentasiAdminData());
                    View::render('index', 'admin/presentation', $data);
                    break;
                case 'tesTulis':
                case 'bankSoal':
                    $data = array_merge($sidebarData, $this->getTesTulisAdminData());
                    View::render('index', 'admin/exam', $data);
                    break;
                case 'wawancara':
                    $data = array_merge($sidebarData, $this->getWawancaraAdminData());
                    View::render('index', 'admin/interview', $data);
                    break;
                case 'profile':
                    $data = array_merge($sidebarData, $this->getProfileData());
                    View::render('index', 'admin/profile', $data);
                    break;
                case 'lihatnilai':
                    $data = array_merge($sidebarData, $this->getNilaiAdminData());
                    View::render('index', 'admin/grades', $data);
                    break;
            }

        } else {
            // Only fetch once if not done in Admin block (though admin block returns early usually, structure implies shared or else)
            // Actually renderPageContent splits by logic. 
            // In the original code, $sidebarData variable scope was inside the Admin if block?
            // Wait, line 121 defined $sidebarData inside "if ($this->getRole() == 'Admin')".
            // I need to define it for User as well.
            
            $sidebarData = $this->getSidebarData(); 

            switch ($page) {
                case 'dashboard':
                    $data = array_merge($sidebarData, $this->getDashboardData());
                    View::render('index', 'user/dashboard', $data);
                    break;
                case 'biodata':
                    $data = array_merge($sidebarData, $this->getBiodataData());
                    View::render('index', 'user/biodata', $data);
                    break;
                case 'pengumuman':
                    // Pengumuman might not return data array, so just pass sidebarData
                    View::render('index', 'user/announcement', $sidebarData);
                    break;
                case 'presentasi':
                    $data = array_merge($sidebarData, $this->getPresentasiData());
                    View::render('index', 'user/presentation', $data);
                    break;
                case 'tesTulis':
                    $data = array_merge($sidebarData, $this->getTesTulisData());
                    View::render('index', 'user/exam', $data);
                    break;
                case 'uploadBerkas':
                    $data = array_merge($sidebarData, $this->getUploadBerkasData());
                    View::render('index', 'user/documents', $data);
                    break;
                case 'wawancara':
                    $data = array_merge($sidebarData, $this->getWawancaraData());
                    View::render('index', 'user/interview', $data);
                    break;
                case 'profile':
                    $data = array_merge($sidebarData, $this->getProfileData());
                    View::render('index', 'user/profile', $data);
                    break;
                case 'editprofile':
                    $data = array_merge($sidebarData, $this->getProfileData());
                    View::render('edit', 'user/profile', $data);
                    break;
                case 'notification':
                    View::render('index', 'user/notifications', $sidebarData);
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
        
        // Use Session role as source of truth
        $role = $_SESSION['user']['role'] ?? ($user['role'] ?? 'User');
        
        if ($role === 'Admin') {
            $photoPath = \App\Controllers\Admin\AdminProfileController::getAdminPhoto($_SESSION['user']['id']);
            $notifikasi = [];
        } else {
            // Updated Logic: Fetch Profile Photo specifically
            $mahasiswaModel = new \App\Model\User\Mahasiswa();
            $mahasiswa = $mahasiswaModel->getMahasiswaId($_SESSION['user']['id']);
            
            $photoName = $mahasiswa['foto_profil'] ?? 'default.png';
            $photoPath = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $photoName;
            
            // Fallback check if file doesn't exist (optional, but good for UX)
            // Note: Relative path check requires document root knowledge, simplistically trusting url for now
            // or we could check file_exists($_SERVER['DOCUMENT_ROOT'] ... )
            
            $notifikasi = NotificationControllers::getMessageById() ?? [];
        }

        return [
            'role' => $role,
            'userName' => $user['username'] ?? ($_SESSION['user']['username'] ?? 'Guest'),
            'photo' => $photoPath,
            'notifikasi' => $notifikasi
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

        // Tambahkan data biodata, user, dan photo
        $biodata = ProfileController::viewBiodata();
        $user = ProfileController::viewUser();
        
        // Updated Logic: Fetch Profile Photo specifically
        $mahasiswaModel = new \App\Model\User\Mahasiswa();
        $mahasiswa = $mahasiswaModel->getMahasiswaId($_SESSION['user']['id']);
        $photoName = $mahasiswa['foto_profil'] ?? 'default.png';
        $photoPath = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $photoName;

        // Tambahkan data dokumen/berkas
        $berkas = BerkasUserController::viewBerkas();
        $dokumen = $this->getDokumenStatus($berkas);

        return [
            'notifikasi' => NotificationControllers::getMessageById() ?? [],
            'tahapanSelesai' => DashboardUserController::getMajorStagesSelesai(),
            'percentage' => DashboardUserController::getPercentage(),
            'jadwalPresentasiUser' => $jadwalPresentasiUser,
            'tahapan' => [
                ["1", "Pemberkasan", DashboardUserController::getMajorStagesSelesai() >= 1, "tahap ini"],
                ["2", "Tes Seleksi", DashboardUserController::getMajorStagesSelesai() >= 2, "tahap ini"],
                ["3", "Wawancara", DashboardUserController::getMajorStagesSelesai() >= 3, "tahap ini"],
                ["4", "Hasil Akhir", DashboardUserController::getMajorStagesSelesai() >= 4, "tahap ini"],
            ],
            'biodata' => $biodata,
            'user' => $user,
            'photo' => $photoPath,
            'dokumen' => $dokumen,
            'graduationStatus' => DashboardUserController::getGraduationStatus(),
            'isPengumumanOpen' => DashboardUserController::isPengumumanOpen(),
            'currentActivities' => DashboardUserController::getKegiatanByMonth(),
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
        
        $role = $user['role'] ?? 'User';
        
        if ($role === 'Admin') {
            $photoPath = \App\Controllers\Admin\AdminProfileController::getAdminPhoto($_SESSION['user']['id']);
        } else {
             $mahasiswaModel = new \App\Model\User\Mahasiswa();
             $mahasiswa = $mahasiswaModel->getMahasiswaId($_SESSION['user']['id']);
             $photoName = $mahasiswa['foto_profil'] ?? 'default.png';
             $photoPath = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $photoName;
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
            'jadwalPresentasiMendatang' => JadwalPresentasiController::getUpcomingJadwal(5),
            'presentationStats' => DashboardAdminController::getPresentationStats()
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
