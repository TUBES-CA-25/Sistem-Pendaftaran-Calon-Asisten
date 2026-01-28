<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Controllers\DashboardUserController;
use App\Controllers\NotificationControllers;
use App\Controllers\ProfileController;
use App\Controllers\BerkasUserController;
use App\Controllers\BiodataUserController;
use App\Controllers\WawancaraController;
use App\Controllers\PresentasiUserController;
use App\Controllers\DashboardAdminController;
use App\Controllers\MahasiswaController;
use App\Controllers\RuanganController;
use App\Controllers\JadwalPresentasiController;
use App\Controllers\AbsensiUserController;
use App\Controllers\NilaiAkhirController;
use App\Controllers\ExamController;
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
        if (!$this->isLoggedIn()) {
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            header('Location: ' . $baseUrl . '/login');
            exit();
        }

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
                case 'pengajuanJudul': return $this->getPengajuanJudulData();
                case 'jadwalPresentasi': return $this->getJadwalPresentasiData();
                case 'tesTulis':
                case 'importSoal':
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
                case 'presentasi': // Fallback or user role? Admin specific logic below
                    $data = array_merge($sidebarData, $this->getPresentasiAdminData());
                    View::render('index', 'admin/presentation', $data);
                    break;
                case 'pengajuanJudul':
                    $data = array_merge($sidebarData, $this->getPengajuanJudulData());
                    View::render('titles', 'admin/presentation', $data);
                    break;
                case 'jadwalPresentasi':
                    $data = array_merge($sidebarData, $this->getJadwalPresentasiData());
                    View::render('schedule', 'admin/presentation', $data);
                    break;
                case 'tesTulis':
                case 'bankSoal':
                    $data = array_merge($sidebarData, $this->getTesTulisAdminData());
                    View::render('index', 'admin/exam', $data);
                    break;
                case 'importSoal':
                    $data = array_merge($sidebarData, $this->getTesTulisAdminData());
                    View::render('importPage', 'admin/exam', $data);
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
        return $_SESSION['user']['role'] ?? null;
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
            $photoPath = \App\Controllers\AdminProfileController::getAdminPhoto($_SESSION['user']['id']);
            $notifikasi = [];
        } else {
            // Updated Logic: Fetch Profile Photo specifically
            $mahasiswaModel = new \App\Model\Mahasiswa();
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
        $biodata = ProfileController::viewBiodata();
        $user = ProfileController::viewUser();

        // Updated Logic: Fetch Profile Photo specifically
        $mahasiswaModel = new \App\Model\Mahasiswa();
        $mahasiswa = $mahasiswaModel->getMahasiswaId($_SESSION['user']['id']);
        $photoName = $mahasiswa['foto_profil'] ?? 'default.png';
        $photoPath = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $photoName;

        // Format profile display
        $profileDisplay = $this->formatProfileDisplay($biodata, $user, $photoName);

        // Calculate progress
        $tahapanSelesai = DashboardUserController::getMajorStagesSelesai();
        $progress = $this->calculateProgress($tahapanSelesai);

        // Tambahkan data dokumen/berkas
        $berkas = BerkasUserController::viewBerkas();
        $dokumen = $this->getDokumenStatus($berkas);

        return [
            'notifikasi' => NotificationControllers::getMessageById() ?? [],
            'tahapanSelesai' => $tahapanSelesai,
            'percentage' => $progress['percentage'],
            'stepProgress' => $progress['percentage'],
            'jadwalPresentasiUser' => $jadwalPresentasiUser,
            'tahapan' => [
                ["1", "Pemberkasan", $tahapanSelesai >= 1, "tahap ini"],
                ["2", "Tes Seleksi", $tahapanSelesai >= 2, "tahap ini"],
                ["3", "Wawancara", $tahapanSelesai >= 3, "tahap ini"],
                ["4", "Hasil Akhir", $tahapanSelesai >= 4, "tahap ini"],
            ],
            'biodata' => $biodata,
            'user' => $user,
            'photo' => $photoPath,
            'profileDisplay' => $profileDisplay,
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
            $photoPath = \App\Controllers\AdminProfileController::getAdminPhoto($_SESSION['user']['id']);
        } else {
             $mahasiswaModel = new \App\Model\Mahasiswa();
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
        $absensiTesTertulis = DashboardUserController::getAbsensiTesTertulis();
        $berkasStatus = DashboardUserController::getBerkasStatus();
        $biodataStatus = DashboardUserController::getBiodataStatus();

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

        // Format each participant
        $formattedMahasiswa = [];
        foreach ($mahasiswa as $mhs) {
            // Format participant data with photoPath and statusBadge
            $formattedMahasiswa[] = $this->formatParticipantForView($mhs);
        }

        return [
            'mahasiswaList' => $formattedMahasiswa,
            'result' => $formattedMahasiswa
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
     * Data untuk presentasi admin view (Legacy/Combined)
     */
    private function getPresentasiAdminData(): array
    {
        $mahasiswaList = PresentasiUserController::viewAllForAdmin() ?? [];

        // Format mahasiswa list with status badges
        $formattedMahasiswaList = $this->formatMahasiswaListForView($mahasiswaList);

        return [
            'mahasiswaList' => $formattedMahasiswaList,
            'mahasiswaAccStatus' => PresentasiUserController::viewAllAccStatusForAdmin() ?? [],
            'ruanganList' => RuanganController::viewAllRuangan() ?? [],
            'jadwalPresentasi' => JadwalPresentasiController::getJadwalPresentasi() ?? []
        ];
    }

    /**
     * Data untuk halaman pengajuan judul
     */
    private function getPengajuanJudulData(): array
    {
        return [
            'mahasiswaList' => PresentasiUserController::viewAllForAdmin() ?? [],
            'mahasiswaAccStatus' => PresentasiUserController::viewAllAccStatusForAdmin() ?? []
        ];
    }

    /**
     * Data untuk halaman jadwal presentasi
     */
    private function getJadwalPresentasiData(): array
    {
        return [
            'ruanganList' => RuanganController::viewAllRuangan() ?? [],
            'jadwalPresentasi' => JadwalPresentasiController::getJadwalPresentasi() ?? []
        ];
    }

    /**
     * Data untuk tes tulis admin view
     */
    private function getTesTulisAdminData(): array
    {
        $examData = ExamController::getAdminExamPageData();
        return [
            'allSoal' => $examData['allSoal'] ?? [],
            'bankSoalList' => $examData['bankSoalList'] ?? [],
            'stats' => $examData['stats'] ?? []
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

    // ==================== HELPER METHODS (menggantikan Services) ====================

    /**
     * Format date from string
     */
    private function formatDate($date, $format = 'd F Y')
    {
        if (empty($date)) {
            return '-';
        }
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Format time from string
     */
    private function formatTime($time, $format = 'H:i')
    {
        if (empty($time)) {
            return '-';
        }
        $timestamp = strtotime($time);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Get full path for user photo
     */
    private function getUserPhotoPath($filename)
    {
        $baseImagePath = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/';
        $defaultPhoto = 'default.png';

        if (empty($filename) || $filename === $defaultPhoto) {
            return $baseImagePath . $defaultPhoto;
        }

        if (strpos($filename, '/') !== false) {
            return $filename;
        }

        return $baseImagePath . $filename;
    }

    /**
     * Check if photo is valid (not default)
     */
    private function hasValidPhoto($filename)
    {
        return !empty($filename) && $filename !== 'default.png';
    }

    /**
     * Generate initials from full name
     */
    private function generateInitials($fullName)
    {
        if (empty($fullName)) {
            return 'U';
        }

        $words = explode(' ', $fullName);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            return strtoupper(substr($fullName, 0, 2));
        }
    }

    /**
     * Format complete profile display data
     */
    private function formatProfileDisplay($biodata, $user, $photo)
    {
        $nama = $biodata['namaLengkap'] ?? $user['username'] ?? 'User';
        $hasValidPhoto = $this->hasValidPhoto($photo);

        return [
            'hasValidPhoto' => $hasValidPhoto,
            'photoPath' => $this->getUserPhotoPath($photo),
            'initials' => $this->generateInitials($nama),
            'displayName' => $nama
        ];
    }

    /**
     * Calculate progress from tahapan selesai
     */
    private function calculateProgress($tahapanSelesai, $maxSteps = 4)
    {
        $percentage = min(($tahapanSelesai / $maxSteps) * 100, 100);

        return [
            'completed' => $tahapanSelesai,
            'total' => $maxSteps,
            'percentage' => $percentage
        ];
    }

    /**
     * Check if user can access exam
     */
    private function canAccessExam($absensiTesTertulis, $berkasStatus, $biodataStatus)
    {
        if ($absensiTesTertulis) {
            return [
                'allowed' => false,
                'reason' => 'completed',
                'message' => 'Anda sudah mengikuti tes tertulis'
            ];
        }

        if (!$biodataStatus) {
            return [
                'allowed' => false,
                'reason' => 'biodata_incomplete',
                'message' => 'Lengkapi biodata terlebih dahulu'
            ];
        }

        if (!$berkasStatus) {
            return [
                'allowed' => false,
                'reason' => 'berkas_incomplete',
                'message' => 'Lengkapi berkas terlebih dahulu'
            ];
        }

        return [
            'allowed' => true,
            'reason' => 'ok',
            'message' => ''
        ];
    }

    /**
     * Get badge style for berkas status
     */
    private function getBerkasStatusBadge($acceptedStatus)
    {
        $class = 'badge rounded-pill bg-secondary bg-opacity-10 text-secondary fw-semibold px-3 py-2';
        $text = 'Belum Upload';

        if (isset($acceptedStatus)) {
            if ($acceptedStatus == 1) {
                $class = 'badge rounded-pill bg-success bg-opacity-10 text-success fw-semibold px-3 py-2';
                $text = 'Disetujui';
            } elseif ($acceptedStatus == 2) {
                $class = 'badge rounded-pill bg-danger bg-opacity-10 text-danger fw-semibold px-3 py-2';
                $text = 'Ditolak';
            } elseif ($acceptedStatus == 0) {
                $class = 'badge rounded-pill bg-info bg-opacity-10 text-info fw-semibold px-3 py-2';
                $text = 'Proses';
            }
        }

        return ['class' => $class, 'text' => $text];
    }

    /**
     * Format participant data for view display
     */
    private function formatParticipantForView($rawData)
    {
        $formatted = $rawData;

        $photoName = $rawData['berkas']['foto'] ?? 'default.png';
        $formatted['photoPath'] = $this->getUserPhotoPath($photoName);

        $acceptedStatus = $rawData['berkas']['accepted'] ?? null;
        $formatted['statusBadge'] = $this->getBerkasStatusBadge($acceptedStatus);

        return $formatted;
    }

    /**
     * Get presentation status badge
     */
    private function getPresentationStatusBadge($isAccepted, $isRejected, $hasSchedule)
    {
        if ($hasSchedule) {
            return [
                'class' => 'bg-primary text-white',
                'text' => 'Terjadwal'
            ];
        } elseif ($isRejected) {
            return [
                'class' => 'bg-danger text-white',
                'text' => 'Ditolak'
            ];
        } elseif ($isAccepted) {
            return [
                'class' => 'bg-success text-white',
                'text' => 'Diterima'
            ];
        } else {
            return [
                'class' => 'bg-secondary text-white',
                'text' => 'Menunggu'
            ];
        }
    }

    /**
     * Format mahasiswa list with presentation status badges
     */
    private function formatMahasiswaListForView($mahasiswaList)
    {
        $formatted = [];

        foreach ($mahasiswaList as $mahasiswa) {
            $isAccepted = isset($mahasiswa['is_accepted']) && $mahasiswa['is_accepted'] == 1;
            $isRejected = isset($mahasiswa['is_accepted']) && $mahasiswa['is_accepted'] == 2;
            $hasSchedule = isset($mahasiswa['has_schedule']) && $mahasiswa['has_schedule'];

            $statusBadge = $this->getPresentationStatusBadge($isAccepted, $isRejected, $hasSchedule);

            $mahasiswa['statusBadge'] = $statusBadge;
            $formatted[] = $mahasiswa;
        }

        return $formatted;
    }
}
