<?php

namespace App\Controllers\user;

use App\Model\User\UserModel;
use App\Core\Controller;
use App\Core\View;
use App\Model\User\Mahasiswa;

class MahasiswaController extends Controller {
    
    public static function viewAllMahasiswa() {
        $mahasiswa = new Mahasiswa();
        $mahasiswa = $mahasiswa->getAll();
        return $mahasiswa == null ? [] : $mahasiswa;
    }

    /**
     * Get detail peserta by ID (AJAX)
     */
    public static function getDetailPeserta() {
        header('Content-Type: application/json');
        ob_clean();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        $id = $_POST['id'] ?? '';
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID peserta tidak ditemukan']);
            return;
        }

        try {
            $mahasiswa = new Mahasiswa();
            $data = $mahasiswa->getMahasiswaById($id);
            
            if ($data) {
                echo json_encode(['status' => 'success', 'data' => $data]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Data peserta tidak ditemukan']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public static function deleteMahasiswa() {
        header('Content-Type: application/json');
        ob_clean();
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
        $id = $_POST['id'] ?? '';
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
            return;
        }
        try {
            if (UserModel::deleteUser($id)) {
                header('Content-Type: application/json');
                ob_clean(); 
                echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil dihapus']);
                return;
            }
        }catch (\Exception $e) {
            header('Content-Type: application/json');
            ob_clean(); 
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            return;
        }
    }
}