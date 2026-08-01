<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

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
        $sql = "SELECT w.id, w.id_mahasiswa, m.nama_lengkap, m.stambuk, r.nama as ruangan, w.jenis_wawancara as kegiatan, w.waktu, w.tanggal,
                       (SELECT foto FROM berkas_mahasiswa WHERE id_mahasiswa = m.id ORDER BY id DESC LIMIT 1) as foto
                FROM wawancara w 
                JOIN mahasiswa m ON w.id_mahasiswa = m.id 
                JOIN ruangan r ON w.id_ruangan = r.id 
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
             $this->view('admin/jadwaltes/index', $data);
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
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ids = $input['id'] ?? []; // Array of mahasiswa IDs
        $id_ruangan = $input['ruangan'] ?? null;
        $tanggal = $input['tanggal'] ?? null;
        $waktu = $input['waktu'] ?? null;
        $kegiatan = $input['kegiatan'] ?? 'Tes Tertulis';

        if (empty($ids) || !$id_ruangan || !$tanggal || !$waktu) {
            self::jsonError('Lengkapi semua data');
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
     * AJAX Endpoint to update schedule
     */
    public function update()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $id_ruangan = $input['ruangan'] ?? null;
        $tanggal = $input['tanggal'] ?? null;
        $waktu = $input['waktu'] ?? null;
        $kegiatan = $input['kegiatan'] ?? null;

        if (!$id || !$id_ruangan || !$tanggal || !$waktu || !$kegiatan) {
            self::jsonError('Lengkapi semua data');
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
