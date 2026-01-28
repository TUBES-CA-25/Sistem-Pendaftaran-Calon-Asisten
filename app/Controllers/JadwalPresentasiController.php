<?php

namespace App\Controllers;
use App\Core\Controller;
use App\Model\JadwalPresentasi;
class JadwalPresentasiController extends Controller
{

    public function saveJadwal()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
        if (!isset($_SESSION['user']['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $id_ruangan = $input['ruangan'] ?? "";
        $tanggal = $input['tanggal'] ?? "";
        $waktu = $input['waktu'] ?? "";
        $mahasiswa = $input['selectedMahasiswa'] ?? "";
        if ( empty($id_ruangan) || empty($tanggal) || empty($waktu) || empty($mahasiswa)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'All fields are required'. 'id ruangan : '.$id_ruangan.'tanggal : '.$tanggal.'waktu : '.$waktu.'Mahasiswa : '.$mahasiswa]);
            return;
        }
        $presentasi = new JadwalPresentasi(
            $id_ruangan,
            $tanggal,
            $waktu
        );
        if ($presentasi->save($presentasi,$mahasiswa)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Jadwal dan mahasiswa berhasil disimpan']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Jadwal gagal disimpan']);
        }
    }
    public static function getJadwalPresentasi() {
        $jadwal = new JadwalPresentasi(0,0,0);
        $data = $jadwal->getJadwalPresentasi();
        return $data;
    }

    /**
     * Get all jadwal untuk admin (AJAX)
     */
    public function getAllJadwal()
    {
        header('Content-Type: application/json');

        try {
            $jadwal = new JadwalPresentasi();
            $data = $jadwal->getAllJadwalWithDetails();

            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get jadwal untuk user (berdasarkan session)
     */
    public function getJadwalUser()
    {
        header('Content-Type: application/json');

        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
                return;
            }

            $id_user = $_SESSION['user']['id'];

            // Get mahasiswa id from user id
            $sql = "SELECT id FROM mahasiswa WHERE id_user = ?";
            $stmt = \App\Core\Model::getDB()->prepare($sql);
            $stmt->bindParam(1, $id_user, \PDO::PARAM_INT);
            $stmt->execute();
            $mahasiswa = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$mahasiswa) {
                echo json_encode(['status' => 'error', 'message' => 'Mahasiswa not found']);
                return;
            }

            $jadwal = new JadwalPresentasi();
            $data = $jadwal->getJadwalByMahasiswaId($mahasiswa['id']);

            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get upcoming jadwal (untuk dashboard)
     */
    public static function getUpcomingJadwal($limit = 5)
    {
        $jadwal = new JadwalPresentasi();
        return $jadwal->getUpcomingJadwal($limit);
    }

    /**
     * Get jadwal by mahasiswa id (static untuk dashboard user)
     */
    public static function getJadwalByMahasiswaId($id_mahasiswa)
    {
        $jadwal = new JadwalPresentasi();
        return $jadwal->getJadwalByMahasiswaId($id_mahasiswa);
    }

    /**
     * Update jadwal (AJAX)
     */
    public function updateJadwal()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        try {
            $id = $_POST['id'] ?? null;
            $id_ruangan = $_POST['id_ruangan'] ?? null;
            $tanggal = $_POST['tanggal'] ?? null;
            $waktu = $_POST['waktu'] ?? null;

            if (!$id || !$id_ruangan || !$tanggal || !$waktu) {
                echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
                return;
            }

            $jadwal = new JadwalPresentasi();
            if ($jadwal->updateJadwal($id, $id_ruangan, $tanggal, $waktu)) {
                echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diupdate']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate jadwal']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete jadwal (AJAX)
     */
    public function deleteJadwal()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        try {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                echo json_encode(['status' => 'error', 'message' => 'ID jadwal diperlukan']);
                return;
            }

            $jadwal = new JadwalPresentasi();
            if ($jadwal->deleteJadwal($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dihapus']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus jadwal']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get mahasiswa yang available untuk dijadwalkan
     */
    public function getAvailableMahasiswa()
    {
        header('Content-Type: application/json');

        try {
            $jadwal = new JadwalPresentasi();
            $data = $jadwal->getMahasiswaWithoutSchedule();

            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get all ruangan
     */
    public function getAllRuangan()
    {
        header('Content-Type: application/json');

        try {
            $jadwal = new JadwalPresentasi();
            $data = $jadwal->getAllRuangan();

            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Save single jadwal (AJAX)
     */
    public function saveSingleJadwal()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        try {
            $id_presentasi = $_POST['id_presentasi'] ?? null;
            $id_ruangan = $_POST['id_ruangan'] ?? null;
            $tanggal = $_POST['tanggal'] ?? null;
            $waktu = $_POST['waktu'] ?? null;

            if (!$id_presentasi || !$id_ruangan || !$tanggal || !$waktu) {
                echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
                return;
            }

            $jadwal = new JadwalPresentasi();

            // Check if already scheduled
            if ($jadwal->hasSchedule($id_presentasi)) {
                echo json_encode(['status' => 'error', 'message' => 'Mahasiswa sudah memiliki jadwal presentasi']);
                return;
            }

            if ($jadwal->saveSingle($id_presentasi, $id_ruangan, $tanggal, $waktu)) {
                echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil disimpan']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan jadwal']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}