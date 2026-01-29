<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

class PengajuanJudulController extends Controller
{
    public function index()
    {
        // Placeholder untuk fitur pengajuan judul
        // Akan diimplementasikan sesuai kebutuhan
    }

    public function simpanPengajuan()
    {
        header('Content-Type: application/json');
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $judulPenelitian = $input['judul'] ?? '';
            $deskripsi = $input['deskripsi'] ?? '';

            if (empty($judulPenelitian)) {
                throw new \Exception('Judul penelitian harus diisi');
            }

            // Logic untuk simpan pengajuan judul
            echo json_encode([
                'status' => 'success',
                'message' => 'Pengajuan judul berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAll()
    {
        header('Content-Type: application/json');
        try {
            // Logic untuk get all pengajuan judul
            echo json_encode([
                'status' => 'success',
                'data' => []
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
