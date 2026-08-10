<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\Presentasi;
use App\Model\JadwalPresentasi;

class JadwalPresentasiController extends Controller
{
    public static function getAll()
    {
        $presentasiModel = new Presentasi();
        return $presentasiModel->getAll();
    }

    /**
     * Menegakkan urutan tahap sebelum jadwal presentasi disimpan.
     *
     * Dropdown sudah menyaring peserta yang belum hadir tes tertulis, tapi
     * endpoint bisa dipanggil langsung - jadi aturannya diulang di server.
     *
     * Perhatikan: yang diterima adalah id PRESENTASI, bukan id mahasiswa.
     * Keduanya sering tertukar karena sama-sama integer, jadi pemetaannya
     * dilakukan eksplisit di sini.
     *
     * @param array  $idPresentasiList
     * @param string $tahap Diteruskan ke Mahasiswa::alasanBelumBolehTahap()
     */
    private static function requireLolosTahapSebelumnya(array $idPresentasiList, string $tahap): void
    {
        foreach ($idPresentasiList as $idPresentasi) {
            $sql = "SELECT id_mahasiswa FROM presentasi WHERE id = ?";
            $stmt = \App\Core\Model::getDB()->prepare($sql);
            $stmt->execute([$idPresentasi]);
            $baris = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$baris) {
                self::jsonError('Data presentasi tidak ditemukan');
            }

            $alasan = \App\Model\Mahasiswa::alasanBelumBolehTahap(
                (int) $baris['id_mahasiswa'],
                $tahap
            );
            if ($alasan !== null) {
                self::jsonError($alasan);
            }
        }
    }

    // Methods dari PresentasiController (JadwalPresentasi)
    public function saveJadwal()
    {
        self::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }
        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $id_ruangan = $input['ruangan'] ?? "";
        $tanggal = $input['tanggal'] ?? "";
        $waktu = $input['waktu'] ?? "";
        $mahasiswa = $input['selectedMahasiswa'] ?? "";
        if ( empty($id_ruangan) || empty($tanggal) || empty($waktu) || empty($mahasiswa)) {
            self::jsonError('All fields are required'. 'id ruangan : '.$id_ruangan.'tanggal : '.$tanggal.'waktu : '.$waktu.'Mahasiswa : '.$mahasiswa);
        }

        self::requireTanggalTidakLampau($tanggal, 'Tanggal presentasi');
        try {
            $presentasi = new JadwalPresentasi(
                $id_ruangan,
                $tanggal,
                $waktu
            );
            if ($presentasi->save($presentasi,$mahasiswa)) {
                self::jsonSuccess([], 'Jadwal dan mahasiswa berhasil disimpan');
            } else {
                self::jsonError('Jadwal gagal disimpan');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public static function getJadwalPresentasi() {
        $jadwal = new JadwalPresentasi(0,0,0);
        $data = $jadwal->getJadwalPresentasi();
        return $data;
    }

    public function getAllJadwal()
    {
        self::requireAuth();
        header('Content-Type: application/json');

        try {
            $jadwal = new JadwalPresentasi();
            $data = $jadwal->getAllJadwalWithDetails();

            // Resolve photo path for each row
            foreach ($data as &$row) {
                $row['photoPath'] = \App\Controllers\HomeController::getUserPhotoPath($row['foto'] ?? 'default.png');
            }

            self::jsonSuccess(['data' => $data]);
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function getJadwalUser()
    {
        self::requireAuth();
        header('Content-Type: application/json');

        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                self::jsonError('User not logged in');
            }

            $id_user = $_SESSION['user']['id'];

            // Get mahasiswa id from user id
            $sql = "SELECT id FROM mahasiswa WHERE id_user = ?";
            $stmt = \App\Core\Model::getDB()->prepare($sql);
            $stmt->bindParam(1, $id_user, \PDO::PARAM_INT);
            $stmt->execute();
            $mahasiswa = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$mahasiswa) {
                self::jsonError('Mahasiswa not found');
            }

            $jadwal = new JadwalPresentasi();
            $data = $jadwal->getJadwalByMahasiswaId($mahasiswa['id']);

            self::jsonSuccess(['data' => $data]);
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public static function getUpcomingJadwal($limit = 5)
    {
        $jadwal = new JadwalPresentasi();
        return $jadwal->getUpcomingJadwal($limit);
    }

    public static function getJadwalByMahasiswaId($id_mahasiswa)
    {
        $jadwal = new JadwalPresentasi();
        return $jadwal->getJadwalByMahasiswaId($id_mahasiswa);
    }

    public function updateJadwal()
    {
        self::requireAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        try {
            $id = $_POST['id'] ?? null;
            $id_ruangan = $_POST['id_ruangan'] ?? null;
            $tanggal = $_POST['tanggal'] ?? null;
            $waktu = $_POST['waktu'] ?? null;

            if (!$id || !$id_ruangan || !$tanggal || !$waktu) {
                self::jsonError('Semua field harus diisi');
            }

            self::requireTanggalTidakLampau($tanggal, 'Tanggal presentasi');

            $jadwal = new JadwalPresentasi();
            if ($jadwal->updateJadwal($id, $id_ruangan, $tanggal, $waktu)) {
                self::jsonSuccess([], 'Jadwal berhasil diupdate');
            } else {
                self::jsonError('Gagal mengupdate jadwal');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function deleteJadwal()
    {
        self::requireAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        try {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                self::jsonError('ID jadwal diperlukan');
            }

            $jadwal = new JadwalPresentasi();
            if ($jadwal->deleteJadwal($id)) {
                self::jsonSuccess([], 'Jadwal berhasil dihapus');
            } else {
                self::jsonError('Gagal menghapus jadwal');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function getAvailableMahasiswa()
    {
        self::requireAuth();
        header('Content-Type: application/json');

        try {
            $jadwal = new JadwalPresentasi();
            $data = $jadwal->getMahasiswaWithoutSchedule();

            self::jsonSuccess(['data' => $data]);
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function getAllRuangan()
    {
        self::requireAuth();
        header('Content-Type: application/json');

        try {
            $jadwal = new JadwalPresentasi();
            $data = $jadwal->getAllRuangan();

            self::jsonSuccess(['data' => $data]);
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function saveSingleJadwal()
    {
        self::requireAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        try {
            $id_presentasi = $_POST['id_presentasi'] ?? null;
            $id_ruangan = $_POST['id_ruangan'] ?? null;
            $tanggal = $_POST['tanggal'] ?? null;
            $waktu = $_POST['waktu'] ?? null;

            if (!$id_presentasi || !$id_ruangan || !$tanggal || !$waktu) {
                self::jsonError('Semua field harus diisi');
            }

            self::requireTanggalTidakLampau($tanggal, 'Tanggal presentasi');
            self::requireLolosTahapSebelumnya([$id_presentasi], 'presentasi');

            $jadwal = new JadwalPresentasi();

            if ($jadwal->saveSingle($id_presentasi, $id_ruangan, $tanggal, $waktu)) {
                self::jsonSuccess([], 'Jadwal berhasil disimpan');
            } else {
                self::jsonError('Gagal menyimpan jadwal');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    /**
     * Menjadwalkan banyak peserta presentasi sekaligus dengan slot berurutan.
     *
     * Payload dibaca dari body JSON (bukan $_POST) karena berisi array id -
     * mengikuti pola saveJadwalTes yang dipanggil lewat dom.postBodyJSON().
     */
    public function saveBatchJadwal()
    {
        self::requireAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];

            $ids     = $input['id'] ?? [];
            $ruangan = $input['id_ruangan'] ?? null;
            $tanggal = $input['tanggal'] ?? null;
            $mulai   = $input['waktu_mulai'] ?? null;
            $durasi  = $input['durasi'] ?? 20;

            if (!is_array($ids)) {
                $ids = [$ids];
            }
            // Buang nilai kosong supaya baris terhapus di sisi klien tidak
            // ikut terkirim sebagai id kosong.
            $ids = array_values(array_filter($ids, function ($v) {
                return $v !== null && $v !== '';
            }));

            if (empty($ids)) {
                self::jsonError('Pilih minimal satu peserta');
            }
            if (!$ruangan || !$tanggal || !$mulai) {
                self::jsonError('Ruangan, tanggal, dan waktu mulai harus diisi');
            }

            self::requireTanggalTidakLampau($tanggal, 'Tanggal presentasi');
            self::requireLolosTahapSebelumnya($ids, 'presentasi');

            $jadwal = new JadwalPresentasi();
            $hasil = $jadwal->saveBatch($ids, $ruangan, $tanggal, $mulai, $durasi);

            self::jsonSuccess(
                $hasil,
                $hasil['jumlah'] . ' jadwal presentasi berhasil dibuat'
            );
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
}
