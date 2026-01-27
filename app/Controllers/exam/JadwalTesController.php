<?php
namespace App\Controllers\exam;

use App\Core\Controller;
use App\Model\exam\BankSoal;

class JadwalTesController extends Controller
{
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
        $sql = "SELECT w.id, w.id_mahasiswa, m.nama_lengkap, m.stambuk, r.nama as ruangan, w.jenis_wawancara as kegiatan, w.waktu, w.tanggal 
                FROM wawancara w 
                JOIN mahasiswa m ON w.id_mahasiswa = m.id 
                JOIN ruangan r ON w.id_ruangan = r.id 
                WHERE w.jenis_wawancara LIKE 'Tes Tertulis%'
                ORDER BY w.tanggal DESC, w.waktu DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $jadwalTesList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $mahasiswaList = \App\Controllers\user\MahasiswaController::getAvailableForTesTulis() ?? [];
        $ruanganList = \App\Controllers\presentasi\RuanganController::viewAllRuangan() ?? [];

        $data = [
            'jadwalTesList' => $jadwalTesList,
            'mahasiswaList' => $mahasiswaList,
            'ruanganList' => $ruanganList,
        ];

        // Detect AJAX (sidebar navigation)
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($isAjax) {
             $this->view('admin/exam/schedule', $data);
        } else {
             $sidebarData = [
                 'role' => 'Admin',
                 'userName' => $_SESSION['user']['username'] ?? 'Admin',
                 'photo' => '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/default-avatar.png',
                 'initialPage' => 'jadwaltes'
             ];
             
             if (class_exists('App\Controllers\Admin\AdminProfileController')) {
                 $photoPath = \App\Controllers\Admin\AdminProfileController::getAdminPhoto($_SESSION['user']['id']);
                 $sidebarData['photo'] = $photoPath;
             }
             
             $fullData = array_merge($data, $sidebarData);
             $this->view('layouts/mainAdmin', $fullData);
        }
    }

    /**
     * AJAX Endpoint to save schedules
     */
    public function save()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ids = $input['id'] ?? []; // Array of mahasiswa IDs
        $id_ruangan = $input['ruangan'] ?? null;
        $tanggal = $input['tanggal'] ?? null;
        $waktu = $input['waktu'] ?? null;
        $kegiatan = $input['kegiatan'] ?? 'Tes Tertulis';

        if (empty($ids) || !$id_ruangan || !$tanggal || !$waktu) {
            echo json_encode(['status' => 'error', 'message' => 'Lengkapi semua data']);
            return;
        }

        try {
            $db = \App\Core\Model::getDB();
            $sql = "INSERT INTO wawancara (id_mahasiswa, id_ruangan, jenis_wawancara, waktu, tanggal) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);

            foreach ($ids as $id_mahasiswa) {
                $stmt->execute([$id_mahasiswa, $id_ruangan, $kegiatan, $waktu, $tanggal]);
            }

            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil disimpan']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX Endpoint to delete schedule
     */
    public function delete()
    {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
            return;
        }

        try {
            $db = \App\Core\Model::getDB();
            $stmt = $db->prepare("DELETE FROM wawancara WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dihapus']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX Endpoint to update schedule
     */
    public function update()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $id_ruangan = $input['ruangan'] ?? null;
        $tanggal = $input['tanggal'] ?? null;
        $waktu = $input['waktu'] ?? null;
        $kegiatan = $input['kegiatan'] ?? null;

        if (!$id || !$id_ruangan || !$tanggal || !$waktu || !$kegiatan) {
            echo json_encode(['status' => 'error', 'message' => 'Lengkapi semua data']);
            return;
        }

        try {
            $db = \App\Core\Model::getDB();
            $sql = "UPDATE wawancara SET id_ruangan = ?, jenis_wawancara = ?, waktu = ?, tanggal = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_ruangan, $kegiatan, $waktu, $tanggal, $id]);

            echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diupdate']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
