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
}
