<?php
namespace App\Controllers\user;
use App\Model\User\Absensi;
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
            $id = $input['mahasiswa'] ?? "tidak ada mahasiswa";
            $wawancaraI = $input['wawancara1'] ?? "tidak ada wawancara1";
            $wawancaraII = $input['wawancara2'] ?? "tidak ada wawancara2";
            $wawancaraIII = $input['wawancara3'] ?? "tidak ada wawancara3";
            $tesTertulis = $input['tesTertulis'] ?? "tidak ada tes tertulis";
            $presentasi = $input['presentasi'] ?? "tidak ada presentasi";
            if (empty($id) || empty($wawancaraI) || empty($wawancaraII) || empty($wawancaraIII) || empty($tesTertulis) || empty($presentasi)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'All fields are required' . 'mahasiswa : ' . $id . 'wawancaraI : ' . $wawancaraI . 'wawancaraII : ' . $wawancaraII . 'wawancaraIII : ' . $wawancaraIII . 'tesTertulis : ' . $tesTertulis . 'presentasi : ' . $presentasi]);
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
        $wawancaraIII = $input['wawancaraIII'];
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