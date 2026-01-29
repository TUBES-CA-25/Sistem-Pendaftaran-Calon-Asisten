<?php

namespace App\Controllers\Admin;

use App\Model\UserModel;
use App\Core\Controller;
use App\Core\View;
use App\Model\Mahasiswa;

class PesertaController extends Controller {
    
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
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        $idUser = $_POST['id'] ?? '';
        $idMahasiswa = $_POST['mahasiswaId'] ?? '';

        if (!$idUser && !$idMahasiswa) {
            echo json_encode(['status' => 'error', 'message' => 'ID peserta tidak ditemukan']);
            return;
        }

        try {
            if ($idUser) {
                // Delete Mahasiswa record first to avoid Foreign Key Constraint fail
                $mahasiswa = new Mahasiswa();
                $mhsData = $mahasiswa->getMahasiswaId($idUser);
                
                if ($mhsData) {
                    $mahasiswa->deleteMahasiswaById($mhsData['id']);
                }

                // Primary: Delete User
                if (UserModel::deleteUser($idUser)) {
                    echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil dihapus']);
                    return;
                }
            } elseif ($idMahasiswa) {
                // Fallback: Delete Mahasiswa Record Only
                $mahasiswa = new Mahasiswa();
                $mahasiswa->deleteMahasiswaById($idMahasiswa);
                echo json_encode(['status' => 'success', 'message' => 'Data mahasiswa berhasil dihapus']);
                return;
            }
            
            // If we get here without return, something failed silently or logic gap
            throw new \Exception('Gagal menghapus data');

        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            return;
        }
    }
}
