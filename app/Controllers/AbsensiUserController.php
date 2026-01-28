<?php
namespace App\Controllers;
use App\Model\Absensi;
use App\Core\Controller;
class AbsensiUserController extends Controller
{
    public static function viewAbsensi()
    {
        $absensi = new Absensi();
        $data = $absensi->getAbsensi();
        return $data;

    }

    public function saveData()
    {
        try {
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
            $id = $input['mahasiswa'] ?? null;
            $wawancaraI = !empty($input['wawancara1']) ? $input['wawancara1'] : '-';
            $wawancaraII = !empty($input['wawancara2']) ? $input['wawancara2'] : '-';
            $wawancaraIII = '-'; // Removed Wawancara III
            $tesTertulis = !empty($input['tesTertulis']) ? $input['tesTertulis'] : '-';
            $presentasi = !empty($input['presentasi']) ? $input['presentasi'] : '-';

            if (empty($id)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Mahasiswa belum dipilih']);
                return;
            }
            
            $absensi = new Absensi(
                null,
                $wawancaraI,
                $wawancaraII,
                $wawancaraIII,
                $tesTertulis,
                $presentasi
            );
            if ($absensi->addMahasiswa($absensi, $id)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Absensi berhasil disimpan']);
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            return;
        }
    }
    public function updateData() {
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
        
        $id = $input['id'];
        $wawancaraI = $input['wawancaraI'];
        $wawancaraII = $input['wawancaraII'];
        $wawancaraIII = '-'; // Removed Wawancara III
        $tesTertulis = $input['tesTertulis'];
        $presentasi = $input['presentasi'];

        $absensi = new Absensi(
            $id,
            $wawancaraI,
            $wawancaraII,
            $wawancaraIII,
            $tesTertulis,
            $presentasi
        );
        if($absensi->updateAbsensi()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Absensi berhasil diupdate']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate absensi']);
        }


    }

    public function deleteData() {
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
        $id = $input['id'] ?? null;

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            return;
        }

        try {
            $absensi = new Absensi();
            if ($absensi->deleteAbsensi($id)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Absensi berhasil dihapus']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus absensi']);
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}