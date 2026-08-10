<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

class JadwalTesController extends Controller
{
    /**
     * Satu-satunya nilai sah untuk jenis kegiatan di halaman ini.
     *
     * Bukan pilihan admin: tab Tes Tertulis memang hanya menjadwalkan tes
     * tertulis. Nilainya harus persis seperti ini karena banyak query
     * menyaring dengan LIKE 'Tes Tertulis%'.
     */
    private const JENIS_TES_TERTULIS = 'Tes Tertulis';

    public function index()
    {
        // Check if user is logged in
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            $baseUrl = str_replace('/public', '', $baseUrl);
            header('Location: ' . $baseUrl . '/login');
            exit;
        }

        // 1. Load Student Test Schedules from 'wawancara' table
        // We filter where jenis_wawancara is like 'Tes Tertulis%'
        $db = \App\Core\Model::getDB();
        // LEFT JOIN ke ruangan: tidak ada foreign key ke tabel ruangan, jadi
        // ruangan yang dihapus meninggalkan jadwal yatim. Dengan INNER JOIN
        // baris itu hilang dari daftar padahal tetap memblokir slot saat
        // pengecekan bentrok - lihat catatan di Model\Wawancara::getAll().
        $sql = "SELECT w.id, w.id_mahasiswa, m.nama_lengkap, m.stambuk,
                       COALESCE(r.nama, '(ruangan dihapus)') as ruangan,
                       w.jenis_wawancara as kegiatan, w.waktu, w.tanggal,
                       (SELECT foto FROM berkas_mahasiswa WHERE id_mahasiswa = m.id ORDER BY id DESC LIMIT 1) as foto
                FROM wawancara w
                JOIN mahasiswa m ON w.id_mahasiswa = m.id
                LEFT JOIN ruangan r ON w.id_ruangan = r.id
                WHERE w.jenis_wawancara LIKE 'Tes Tertulis%'
                ORDER BY w.tanggal DESC, w.waktu DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $jadwalTesList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $mahasiswaList = \App\Controllers\Admin\PesertaController::viewAllMahasiswa() ?? [];
        
        // Filter: Exclude students who already have a "Tes Tertulis" schedule
        $scheduledMahasiswaIds = array_column($jadwalTesList, 'id_mahasiswa');
        $mahasiswaList = array_filter($mahasiswaList, function($mhs) use ($scheduledMahasiswaIds) {
            return !in_array($mhs['id'], $scheduledMahasiswaIds);
        });
        $ruanganList = \App\Controllers\Admin\RuanganController::viewAllRuangan() ?? [];

        $data = [
            'jadwalTesList' => $jadwalTesList,
            'mahasiswaList' => $mahasiswaList,
            'ruanganList' => $ruanganList,
        ];

        // Detect AJAX (sidebar navigation)
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($isAjax) {
             $this->view('admin/penjadwalan/tes/index', $data);
        } else {
             $sidebarData = [
                 'role' => 'Admin',
                 'userName' => $_SESSION['user']['username'] ?? 'Admin',
                 'photo' => '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/default-avatar.png',
                 'initialPage' => 'jadwaltes'
             ];
             
             if (class_exists('App\Controllers\HomeController')) {
                 $photoPath = \App\Controllers\HomeController::getAdminPhoto($_SESSION['user']['id']);
                 $sidebarData['photo'] = $photoPath;
             }
             
             $fullData = array_merge($data, $sidebarData);
             $this->view('layouts/main_admin', $fullData);
        }
    }

    /**
     * AJAX Endpoint to save schedules
     */
    public function save()
    {
        self::requireAuth();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ids = $input['id'] ?? []; // Array of mahasiswa IDs
        $id_ruangan = $input['ruangan'] ?? null;
        $tanggal = $input['tanggal'] ?? null;
        $waktu = $input['waktu'] ?? null;
        // Jenis kegiatan DIKUNCI, tidak diambil dari input.
        //
        // Tabel `wawancara` menampung jadwal tes tertulis DAN wawancara; yang
        // memisahkannya hanya nilai kolom ini. Ada 19 tempat di kode yang
        // menyaring dengan LIKE 'Tes Tertulis%' - mulai dari daftar jadwal,
        // pengecekan duplikat, sampai kelayakan peserta untuk presentasi.
        // Kalau nilainya salah ketik sedikit saja ("tes tulis", "Tes tertulis "),
        // baris itu berhenti terhitung sebagai tes tertulis di seluruh sistem
        // dan peserta bisa terjadwal dua kali. Karena itu nilainya ditetapkan
        // di server, bukan dipercayakan ke input.
        $kegiatan = self::JENIS_TES_TERTULIS;

        if (empty($ids) || !$id_ruangan || !$tanggal || !$waktu) {
            self::jsonError('Lengkapi semua data');
        }

        self::requireTanggalTidakLampau($tanggal, 'Tanggal tes');

        /* Bentrok dicek terhadap jadwal yang SUDAH ada di ruangan+jam itu.
           Menjadwalkan banyak mahasiswa sekaligus ke satu ruangan pada jam
           yang sama tetap boleh - itu memang bentuk ujian massal. */
        $bentrok = self::cariBentrok($id_ruangan, $tanggal, $waktu);
        if ($bentrok !== null) {
            self::jsonError('Ruangan sudah dipakai pada jam tersebut oleh ' . $bentrok);
        }

        try {
            $db = \App\Core\Model::getDB();
            $sql = "INSERT INTO wawancara (id_mahasiswa, id_ruangan, jenis_wawancara, waktu, tanggal) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);

            // Prepare check statement for duplicate prevention
            $checkSql = "SELECT COUNT(*) FROM wawancara WHERE id_mahasiswa = ? AND jenis_wawancara LIKE 'Tes Tertulis%'";
            $checkStmt = $db->prepare($checkSql);

            $successCount = 0;
            $skippedCount = 0;

            foreach ($ids as $id_mahasiswa) {
                // Check if already scheduled
                $checkStmt->execute([$id_mahasiswa]);
                if ($checkStmt->fetchColumn() > 0) {
                    $skippedCount++;
                    continue; 
                }

                $stmt->execute([$id_mahasiswa, $id_ruangan, $kegiatan, $waktu, $tanggal]);
                $successCount++;
            }

            if ($successCount > 0) {
                $msg = "Berhasil menjadwalkan $successCount mahasiswa.";
                if ($skippedCount > 0) $msg .= " ($skippedCount dilewati karena sudah ada jadwal)";
                self::jsonSuccess([], $msg);
            } else {
                 self::jsonError('Semua mahasiswa yang dipilih sudah memiliki jadwal.');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    /**
     * AJAX Endpoint to delete schedule
     */
    public function delete()
    {
        self::requireAuth();
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            self::jsonError('ID tidak valid');
        }

        try {
            $db = \App\Core\Model::getDB();
            $stmt = $db->prepare("DELETE FROM wawancara WHERE id = ?");
            $stmt->execute([$id]);
            self::jsonSuccess([], 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    /**
     * Cek apakah ruangan sudah terpakai pada tanggal & jam yang sama.
     *
     * Tabel `wawancara` menampung Tes Tertulis DAN Wawancara, jadi
     * pengecekan sengaja TIDAK memfilter jenis_wawancara - satu ruangan
     * tidak bisa dipakai dua kegiatan berbeda pada jam yang sama.
     *
     * @param int|null $abaikanId id jadwal yang sedang diedit (dikecualikan)
     * @return string|null nama kegiatan yang bentrok, atau null bila aman
     */
    /**
     * Pembungkus tipis ke Model\Wawancara::cariBentrokJadwal().
     *
     * Isinya dipindahkan ke model karena penjadwalan WAWANCARA memerlukan
     * aturan yang sama - dulu logika ini privat di sini sehingga wawancara
     * tidak pernah memeriksa bentrok ruangan sama sekali.
     */
    private static function cariBentrok($id_ruangan, $tanggal, $waktu, $abaikanId = null): ?string
    {
        return \App\Model\Wawancara::cariBentrokJadwal($id_ruangan, $tanggal, $waktu, $abaikanId);
    }

    /**
     * AJAX Endpoint to update schedule
     */
    public function update()
    {
        self::requireAuth();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $id_ruangan = $input['ruangan'] ?? null;
        $tanggal = $input['tanggal'] ?? null;
        $waktu = $input['waktu'] ?? null;
        // Dikunci seperti pada save(): mengubah jenis lewat halaman ini akan
        // memindahkan baris itu ke tab Wawancara dan membuatnya lolos dari
        // pengecekan duplikat tes tertulis.
        $kegiatan = self::JENIS_TES_TERTULIS;

        if (!$id || !$id_ruangan || !$tanggal || !$waktu) {
            self::jsonError('Lengkapi semua data');
        }

        self::requireTanggalTidakLampau($tanggal, 'Tanggal tes');

        $bentrok = self::cariBentrok($id_ruangan, $tanggal, $waktu, $id);
        if ($bentrok !== null) {
            self::jsonError('Ruangan sudah dipakai pada jam tersebut oleh ' . $bentrok);
        }

        try {
            $db = \App\Core\Model::getDB();
            $sql = "UPDATE wawancara SET id_ruangan = ?, jenis_wawancara = ?, waktu = ?, tanggal = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_ruangan, $kegiatan, $waktu, $tanggal, $id]);

            self::jsonSuccess([], 'Jadwal berhasil diupdate');
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
}
