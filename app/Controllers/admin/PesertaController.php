<?php

namespace App\Controllers\Admin;

use App\Model\UserModel;
use App\Core\Controller;
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
            self::jsonError('Invalid request method');
        }

        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }

        $id = $_POST['id'] ?? '';
        if (!$id) {
            self::jsonError('ID peserta tidak ditemukan');
        }

        try {
            $mahasiswa = new Mahasiswa();
            $data = $mahasiswa->getMahasiswaById($id);
            
            if ($data) {
                self::jsonSuccess(['data' => $data]);
            } else {
                self::jsonError('Data peserta tidak ditemukan');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
    public static function deleteMahasiswa() {
        header('Content-Type: application/json');
        ob_clean();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }

        $idUser = $_POST['id'] ?? '';   
        $idMahasiswa = $_POST['mahasiswaId'] ?? '';

        if (!$idUser && !$idMahasiswa) {
            self::jsonError('ID peserta tidak ditemukan');
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
                    self::jsonSuccess([], 'Mahasiswa berhasil dihapus');
                }
            } elseif ($idMahasiswa) {
                // Fallback: Delete Mahasiswa Record Only
                $mahasiswa = new Mahasiswa();
                $mahasiswa->deleteMahasiswaById($idMahasiswa);
                self::jsonSuccess([], 'Data mahasiswa berhasil dihapus');
            }
            
            // If we get here without return, something failed silently or logic gap
            throw new \Exception('Gagal menghapus data');

        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
}
