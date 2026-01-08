<?php

namespace App\Controllers\Home;

use App\Core\Controller;
use App\Core\View;
use App\Model\User\DashboardUser;
use App\Model\User\BiodataUser;
use App\Model\User\UserModel;
use App\Model\User\BerkasUser;
use App\Model\User\NotificationUser;
use App\Model\User\PresentasiUser;
use App\Model\User\Mahasiswa;
use App\Model\User\Absensi;
use App\Model\Wawancara\Wawancara;
use App\Model\presentasi\Ruangan;
use App\Model\presentasi\JadwalPresentasi;
use App\Model\exam\SoalExam;
use App\Model\exam\NilaiAkhir;
use App\Model\admin\DashboardAdmin;


class HomeController extends Controller
{
    public function index()
    {
        if ($this->isLoggedIn() && $this->getRole() == "User") {
            View::render('main', 'layouts');

        } else if ($this->isLoggedIn() && $this->getRole() == "Admin") {
            View::render('mainAdmin', 'layouts');

        } else {
            View::render('login', 'auth');
            exit();
        }
    }

    /**
     * Get dashboard data from Model
     */
    private function getDashboardData(): array
    {
        $dashboardUser = new DashboardUser();
        $notificationUser = new NotificationUser($_SESSION['user']['id'], '');
        
        $statuses = [
            'biodata' => $dashboardUser->getBiodataStatus(),
            'berkas' => $dashboardUser->getBerkasStatus(),
            'tesTertulis' => $dashboardUser->getAbsensiTesTertulis(),
            'wawancaraI' => $dashboardUser->getAbsensiWawancaraI(),
            'wawancaraII' => $dashboardUser->getAbsensiWawancaraII(),
            'wawancaraIII' => $dashboardUser->getAbsensiWawancaraIII(),
            'presentasi' => $dashboardUser->getAbsensiPresentasi(),
            'ppt' => $dashboardUser->getStatusPpt(),
            'pptJudul' => $dashboardUser->getPptAccStatus(),
        ];
        
        $completed = array_sum(array_map(fn($s) => $s ? 1 : 0, $statuses));
        $total = 9;
        $percentage = $completed > 0 ? round(($completed / $total) * 100) : 0;
        
        return [
            'statuses' => $statuses,
            'completed' => $completed,
            'percentage' => $percentage,
            'notifikasi' => $notificationUser->getById($notificationUser) ?? []
        ];
    }

    /**
     * Get biodata page data from Model
     */
    private function getBiodataData(): array
    {
        $biodataModel = new BiodataUser();
        $userModel = new UserModel();
        
        $biodata = $biodataModel->getBiodata($_SESSION['user']['id']);
        $user = $userModel->getUser($_SESSION['user']['id']);
        $isEmpty = $biodataModel->isEmpty($_SESSION['user']['id']);
        
        return [
            'isEmpty' => $isEmpty,
            'stambuk' => $user['stambuk'] ?? '',
            'nama' => $biodata['namaLengkap'] ?? 'Nama Lengkap',
            'jurusan' => $biodata['jurusan'] ?? 'Jurusan',
            'alamat' => $biodata['alamat'] ?? 'Alamat',
            'kelas' => $biodata['kelas'] ?? 'Kelas',
            'jenisKelamin' => $biodata['jenisKelamin'] ?? 'Jenis Kelamin',
            'tempatLahir' => $biodata['tempatLahir'] ?? 'Tempat Lahir',
            'tanggalLahir' => $biodata['tanggalLahir'] ?? 'Tanggal Lahir',
            'noHp' => $biodata['noHp'] ?? 'No Telephone',
        ];
    }

    /**
     * Get profile page data from Model
     */
    private function getProfileData(): array
    {
        $biodataModel = new BiodataUser();
        $userModel = new UserModel();
        $berkasModel = new BerkasUser();
        
        $biodata = $biodataModel->getBiodata($_SESSION['user']['id']);
        $user = $userModel->getUser($_SESSION['user']['id']);
        $berkas = $berkasModel->getBerkas($_SESSION['user']['id']);
        
        return [
            'userName' => $user['username'] ?? '',
            'stambuk' => $user['stambuk'] ?? '',
            'nama' => $biodata['namaLengkap'] ?? 'Nama Lengkap',
            'jurusan' => $biodata['jurusan'] ?? 'Jurusan',
            'alamat' => $biodata['alamat'] ?? 'Alamat',
            'kelas' => $biodata['kelas'] ?? 'Kelas',
            'jenisKelamin' => $biodata['jenisKelamin'] ?? 'Jenis Kelamin',
            'tempatLahir' => $biodata['tempatLahir'] ?? 'Tempat Lahir',
            'tanggalLahir' => $biodata['tanggalLahir'] ?? 'Tanggal Lahir',
            'noHp' => $biodata['noHp'] ?? 'No Telephone',
            'photo' => $berkas['foto'] ?? 'default.png',
        ];
    }

    /**
     * Get upload berkas page data from Model
     */
    private function getUploadBerkasData(): array
    {
        $biodataModel = new BiodataUser();
        $berkasModel = new BerkasUser();
        
        $biodata = $biodataModel->getBiodata($_SESSION['user']['id']);
        $berkas = $berkasModel->getBerkas($_SESSION['user']['id']);
        
        return [
            'nama' => $biodata['namaLengkap'] ?? 'Nama Lengkap',
            'berkas' => $berkas ?? [],
        ];
    }

    /**
     * Get presentasi page data from Model (User)
     */
    private function getPresentasiData(): array
    {
        $dashboardUser = new DashboardUser();
        $presentasiModel = new PresentasiUser();
        
        return [
            'results' => $presentasiModel->viewAll($_SESSION['user']['id']) ?? [],
            'biodataStatus' => $dashboardUser->getBiodataStatus(),
            'berkasStatus' => $dashboardUser->getBerkasStatus(),
            'tesTertulisStatus' => $dashboardUser->getAbsensiTesTertulis(),
            'pptStatus' => $dashboardUser->getStatusPpt(),
        ];
    }

    /**
     * Get wawancara page data from Model (User)
     */
    private function getWawancaraData(): array
    {
        $wawancaraModel = new Wawancara(0, 0, 0, 0);
        return [
            'wawancara' => $wawancaraModel->getWawancaraById($_SESSION['user']['id']) ?? []
        ];
    }

    /**
     * Get tes tulis page data from Model (User)
     */
    private function getTesTulisData(): array
    {
        $dashboardUser = new DashboardUser();
        return [
            'biodataStatus' => $dashboardUser->getBiodataStatus(),
            'berkasStatus' => $dashboardUser->getBerkasStatus(),
            'tesTertulisStatus' => $dashboardUser->getAbsensiTesTertulis(),
        ];
    }

    // ==================== ADMIN DATA METHODS ====================

    /**
     * Get dashboard admin data from Model
     */
    private function getDashboardAdminData(): array
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        return [
            'totalPendaftar' => DashboardAdmin::getTotalPendaftar(),
            'pendaftarLulus' => DashboardAdmin::getPendaftarLulus(),
            'pendaftarPending' => DashboardAdmin::getPendaftarPending(),
            'pendaftarGagal' => DashboardAdmin::getPendaftarGagal(),
            'statusKegiatan' => DashboardAdmin::getStatusKegiatan(),
            'kegiatanBulanIni' => DashboardAdmin::getKegiatanByMonth($currentYear, $currentMonth) ?? [],
            'currentMonthName' => date('F Y'),
        ];
    }

    /**
     * Get wawancara admin data from Model
     */
    private function getWawancaraAdminData(): array
    {
        $wawancaraModel = new Wawancara(0, 0, 0, 0);
        $mahasiswaModel = new Mahasiswa();
        $ruanganModel = new Ruangan();
        
        return [
            'wawancara' => $wawancaraModel->getAll() ?? [],
            'mahasiswaList' => $mahasiswaModel->getAll() ?? [],
            'ruanganList' => $ruanganModel->getAll() ?? [],
        ];
    }

    /**
     * Get ruangan admin data from Model
     */
    private function getRuanganData(): array
    {
        $ruanganModel = new Ruangan();
        return [
            'ruanganList' => $ruanganModel->getAll() ?? [],
        ];
    }

    /**
     * Get daftar peserta admin data from Model
     */
    private function getDaftarPesertaData(): array
    {
        $mahasiswaModel = new Mahasiswa();
        return [
            'mahasiswaList' => $mahasiswaModel->getAll() ?? [],
        ];
    }

    /**
     * Get daftar hadir admin data from Model
     */
    private function getDaftarHadirData(): array
    {
        $absensiModel = new Absensi();
        $mahasiswaModel = new Mahasiswa();
        
        return [
            'absensiList' => $absensiModel->getAbsensi() ?? [],
            'mahasiswaList' => $mahasiswaModel->getAll() ?? [],
        ];
    }

    /**
     * Get presentasi admin data from Model
     */
    private function getPresentasiAdminData(): array
    {
        $presentasiModel = new PresentasiUser();
        $ruanganModel = new Ruangan();
        $jadwalModel = new JadwalPresentasi(0, 0, 0);
        
        return [
            'mahasiswaList' => $presentasiModel->viewAllForAdmin() ?? [],
            'mahasiswaAccStatus' => $presentasiModel->viewAllAccStatusForAdmin() ?? [],
            'ruanganList' => $ruanganModel->getAll() ?? [],
            'jadwalPresentasi' => $jadwalModel->getJadwalPresentasi() ?? [],
        ];
    }

    /**
     * Get tes tulis admin data from Model
     */
    private function getTesTulisAdminData(): array
    {
        $soalModel = new SoalExam();
        return [
            'allSoal' => $soalModel->getAll() ?? [],
        ];
    }

    /**
     * Get daftar nilai admin data from Model
     */
    private function getDaftarNilaiData(): array
    {
        $nilaiModel = new NilaiAkhir();
        return [
            'nilai' => $nilaiModel->getAllNilai() ?? [],
        ];
    }

    public function loadContent($page)
    {
        if (is_array($page)) {
            $page = $page['page'];
        }

        if ($this->getRole() == "Admin") {
            switch ($page) {
                case 'dashboard':
                    View::render('dashboard', 'admin', $this->getDashboardAdminData());
                    break;
                case 'ruangan':
                    View::render('ruangan', 'admin', $this->getRuanganData());
                    break;
                case 'lihatPeserta':
                    View::render('daftarPeserta', 'admin', $this->getDaftarPesertaData());
                    break;
                case 'daftarKehadiran':
                    View::render('daftarHadir', 'admin', $this->getDaftarHadirData());
                    break;
                case 'presentasi':
                    View::render('admin', 'presentasi', $this->getPresentasiAdminData());
                    break;
                case 'tesTulis':
                    View::render('admin', 'exam/pages', $this->getTesTulisAdminData());
                    break;
                case 'wawancara':
                    View::render('admin', 'wawancara', $this->getWawancaraAdminData());
                    break;
                case 'lihatnilai':
                    View::render('daftarNilai', 'admin', $this->getDaftarNilaiData());
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
                    View::render('dashboard', 'user', $this->getDashboardData());
                    break;
                case 'biodata':
                    View::render('biodata', 'user', $this->getBiodataData());
                    break;
                case 'pengumuman':
                    View::render('pengumuman', 'user');
                    break;
                case 'presentasi':
                    View::render('index', 'presentasi', $this->getPresentasiData());
                    break;
                case 'tesTulis':
                    View::render('user', 'exam/pages', $this->getTesTulisData());
                    break;
                case 'uploadBerkas':
                    View::render('uploadBerkas', 'user', $this->getUploadBerkasData());
                    break;
                case 'wawancara':
                    View::render('index', 'wawancara', $this->getWawancaraData());
                    break;
                case 'profile':
                    View::render('profile', 'user', $this->getProfileData());
                    break;
                case 'editprofile':
                    View::render('editprofile', 'user');
                    break;
                case 'notifcation':
                    View::render('index', 'notification');
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
}
