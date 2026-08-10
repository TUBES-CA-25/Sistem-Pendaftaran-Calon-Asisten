<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

// Models

// User Controllers
use App\Controllers\User\ProfilController;

// Admin Controllers

// Shared Controllers
use App\Controllers\NotifikasiController;


class HomeController extends Controller
{
    // Bagian HomeController dipisah ke trait agar berkas ini tetap ringkas.
    // Perilaku tidak berubah: trait digabung ke class saat runtime.
    use \App\Controllers\Concerns\ProvidesUserData;
    use \App\Controllers\Concerns\ProvidesAdminData;
    use \App\Controllers\Concerns\FormatsViewData;
    use \App\Controllers\Concerns\ManagesUserPhoto;

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
            View::render('main_admin', 'layouts', $data);

        } else {
            View::render('index', 'auth');
            exit();
        }
    }

    /**
     * Titik masuk semua halaman ber-sidebar.
     *
     * $page selalu berupa string: Router mengubah `{page}` jadi grup regex
     * lalu meneruskan hasil preg_match (selalu string) via
     * call_user_func_array; tiga rute lain memanggilnya dengan literal
     * ('tesTulis', 'dashboard'). Karena itu cabang is_array($page) yang
     * dulu ada di sini tidak pernah tercapai dan sudah dihapus.
     */
    /**
     * Daftar nama halaman yang sah per peran.
     *
     * Sumber kebenaran tunggal untuk validasi URL. Harus selalu sinkron dengan
     * label `case` di getPageData() dan renderPageContent().
     */
    private const HALAMAN_VALID = [
        'Admin' => [
            'dashboard', 'ruangan', 'lihatPeserta', 'daftarKehadiran',
            'presentasi', 'pengajuanJudul', 'jadwalPresentasi', 'tesTulis',
            'importSoal', 'bankSoal', 'wawancara', 'profile', 'lihatnilai',
            'penjadwalan',
        ],
        'User' => [
            'dashboard', 'biodata', 'presentasi', 'tesTulis', 'uploadBerkas',
            'wawancara', 'profile', 'editprofile', 'notifikasi', 'notification',
            'pengumuman',
        ],
    ];

    public function loadContent(string $page): void
    {
        if (!$this->isLoggedIn()) {
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            header('Location: ' . $baseUrl . '/login');
            exit();
        }

        // Tolak nama halaman yang tidak dikenal.
        //
        // Dulu cabang `default:` mengembalikan array kosong TETAPI layout tetap
        // dirender, sehingga URL ngawur seperti /admin atau /xyz123 membalas
        // HTTP 200 dengan halaman yang terlihat normal namun seluruh datanya
        // kosong (statistik 0, kalender tanpa tanggal). Menyesatkan bagi
        // pengguna maupun mesin pencari.
        $peran = $this->getRole() === 'Admin' ? 'Admin' : 'User';
        if (!in_array($page, self::HALAMAN_VALID[$peran], true)) {
            $this->renderNotFound($page);
            return;
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
     * Balas 404 untuk halaman yang tidak ada.
     *
     * Permintaan SPA (AJAX) menerima potongan HTML agar app.js bisa
     * menyuntikkannya ke #content; akses URL langsung menerima halaman utuh.
     */
    private function renderNotFound(string $page): void
    {
        http_response_code(404);

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        $data = ['pageTidakDikenal' => $page];

        if ($isAjax) {
            View::render('404', 'errors', $data);
            return;
        }

        // Akses langsung: bungkus dengan layout supaya sidebar tetap ada dan
        // pengguna bisa langsung menavigasi ke halaman lain.
        $data = array_merge($this->getSidebarData(), $data);
        $data['initialPage'] = '404';
        View::render($this->getRole() === 'Admin' ? 'main_admin' : 'main', 'layouts', $data);
    }

    /**
     * Render full page with layout (for direct URL access)
     */
    private function renderFullPage(string $page): void
    {
        $data = $this->getSidebarData();
        $data['initialPage'] = $page;

        // Get page-specific data
        $pageData = $this->getPageData($page);
        $data = array_merge($data, $pageData);

        if ($this->getRole() == "Admin") {
            View::render('main_admin', 'layouts', $data);
        } else {
            View::render('main', 'layouts', $data);
        }
    }

    /**
     * Get data for specific page
     */
    private function getPageData(string $page): array
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
                // Halaman notifikasi/pengumuman tidak punya data khusus, tetapi
                // layout tetap merender dashboard sebagai $initialPage. Tanpa
                // data dashboard, view memunculkan "Undefined variable
                // $graduationStatus / $profileDisplay" di halaman.
                case 'notifikasi':
                case 'notification':
                case 'pengumuman': return $this->getDashboardData();
                default: return [];
            }
        }
    }

    /**
     * Render only page content (for AJAX requests)
     */
    private function renderPageContent(string $page): void
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
                    View::render('index', 'admin/ruangan', $data);
                    break;
                case 'lihatPeserta':
                    $data = array_merge($sidebarData, $this->getDaftarPesertaData());
                    View::render('index', 'admin/peserta', $data);
                    break;
                case 'daftarKehadiran':
                    $data = array_merge($sidebarData, $this->getDaftarHadirData());
                    View::render('index', 'admin/kehadiran', $data);
                    break;
                case 'presentasi': // Fallback or user role? Admin specific logic below
                    $data = array_merge($sidebarData, $this->getPresentasiAdminData());
                    View::render('index', 'admin/penjadwalan/presentasi', $data);
                    break;
                case 'pengajuanJudul':
                    $data = array_merge($sidebarData, $this->getPengajuanJudulData());
                    View::render('index', 'admin/judul', $data);
                    break;
                case 'jadwalPresentasi':
                    $data = array_merge($sidebarData, $this->getJadwalPresentasiData());
                    View::render('index', 'admin/penjadwalan/presentasi', $data);
                    break;
                // Halaman induk Penjadwalan. Tidak mengambil data jadwal apa
                // pun - isi tiap tab diambil terpisah lewat rute aslinya
                // (/jadwaltes, /jadwalPresentasi, /wawancara) oleh
                // penjadwalan.js, sehingga hanya satu markup tab yang pernah
                // ada di DOM dan id yang bertabrakan tidak saling mengganggu.
                case 'penjadwalan':
                    View::render('index', 'admin/penjadwalan', $sidebarData);
                    break;
                case 'tesTulis':
                case 'bankSoal':
                    $data = array_merge($sidebarData, $this->getTesTulisAdminData());
                    View::render('index', 'admin/ujian', $data);
                    break;
                // Import/Export tidak lagi berdiri sebagai halaman sendiri -
                // isinya menjadi tab di halaman Bank Soal. Rute lama tetap
                // dilayani dan langsung membuka tab tersebut supaya bookmark
                // atau tautan lama tidak mati.
                case 'importSoal':
                    $data = array_merge($sidebarData, $this->getTesTulisAdminData());
                    $data['tabAwal'] = 'impor';
                    View::render('index', 'admin/ujian', $data);
                    break;
                case 'wawancara':
                    $data = array_merge($sidebarData, $this->getWawancaraAdminData());
                    View::render('index', 'admin/penjadwalan/wawancara', $data);
                    break;
                case 'profile':
                    // View 'admin/profil' tidak pernah ada di repo ini, sehingga
                    // View::render() selalu jatuh ke cabang error/404. Arahkan ke
                    // dashboard admin agar tidak gagal. (Sisi user memetakan
                    // 'profile' ke user/biodata - lihat case serupa di bawah.)
                    $data = array_merge($sidebarData, $this->getDashboardAdminData());
                    View::render('index', 'admin/dashboard', $data);
                    break;
                case 'lihatnilai':
                    $data = array_merge($sidebarData, $this->getNilaiAdminData());
                    View::render('index', 'admin/nilai', $data);
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
                    View::render('pengumuman', 'user/dashboard', $sidebarData);
                    break;
                case 'presentasi':
                    $data = array_merge($sidebarData, $this->getPresentasiData());
                    View::render('index', 'user/presentasi', $data);
                    break;
                case 'tesTulis':
                    $data = array_merge($sidebarData, $this->getTesTulisData());
                    View::render('index', 'user/ujian', $data);
                    break;
                case 'uploadBerkas':
                    $data = array_merge($sidebarData, $this->getUploadBerkasData());
                    View::render('index', 'user/berkas', $data);
                    break;
                case 'wawancara':
                    $data = array_merge($sidebarData, $this->getWawancaraData());
                    View::render('index', 'user/wawancara', $data);
                    break;
                case 'profile':
                case 'editprofile':
                    $data = array_merge($sidebarData, $this->getBiodataData());
                    View::render('index', 'user/biodata', $data);
                    break;
                case 'notification':
                    View::render('index', 'user/notifikasi', $sidebarData);
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
        $user = ProfilController::viewUser();
        
        // Use Session role as source of truth
        $role = $_SESSION['user']['role'] ?? ($user['role'] ?? 'User');
        
        if ($role === 'Admin') {
            $photoPath = HomeController::getAdminPhoto($_SESSION['user']['id']);
            $notifikasi = [];
        } else {
            // Updated Logic: Fetch Profile Photo specifically
            $mahasiswaModel = new \App\Model\Mahasiswa();
            $mahasiswa = $mahasiswaModel->getMahasiswaId($_SESSION['user']['id']);

            $photoName = $mahasiswa['foto_profil'] ?? 'default.png';
            $photoPath = $this->getUserPhotoPath($photoName);

            $notifikasi = NotifikasiController::getMessageById() ?? [];
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
}
