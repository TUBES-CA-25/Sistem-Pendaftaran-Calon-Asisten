<?php
namespace App\Controllers\user;
use App\Core\Controller;
use App\Model\Wawancara\Wawancara;
class WawancaraController extends Controller
{
    public static function getAll()
    {
        $wawancara = new Wawancara(0, 0, 0, 0);
        $data = $wawancara->getAll();
        return $data;
    }

    public function getAllFilterByIdRuangan()
    {
        header('Content-Type: application/json');
        ob_clean();

        try {
            if (!isset($_SESSION['user']['id'])) {
                error_log("Error: User not logged in");
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['id']) || !is_numeric($input['id'])) {
                echo json_encode(['status' => 'error', 'message' => 'ID ruangan tidak valid']);
                exit;
            }

            $id = (int) $input['id']; 

        
            $wawancara = new Wawancara(0, 0, 0, 0);
            if($id === 0) {
                $data = $wawancara->getAll();
                echo json_encode(['status' => 'success', 'data' => $data]);
                exit;
            }
            $data = $wawancara->getAllFilterByRuangan($id);

            if (empty($data)) {
                echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
                exit;
            }

            echo json_encode(['status' => 'success', 'data' => $data]);
            exit;

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
    public static function getAllById()
    {
        if (!isset($_SESSION['user']['id'])) {
            error_log("Error: User not logged in");
            return [];
        }

        $id = $_SESSION['user']['id'];
        $wawancara = new Wawancara(0, 0, 0, 0);

        try {
            $data = $wawancara->getWawancaraById($id);
            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            error_log("Error in getAllById: " . $e->getMessage());
            return [];
        }
    }

    public function save()
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
        $selectedMahasiswa = $input['id'] ?? [];
        $id_ruangan = $input['ruangan'] ?? "";
        $jenis_wawancara = $input['wawancara'] ?? "";
        $waktu = $input['waktu'] ?? "";
        $tanggal = $input['tanggal'] ?? "";

        if (empty($selectedMahasiswa) || empty($id_ruangan) || empty($jenis_wawancara) || empty($waktu) || empty($tanggal)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }
        $wawancara = new Wawancara(
            $id_ruangan,
            $jenis_wawancara,
            $waktu,
            $tanggal
        );
        if ($wawancara->save($wawancara, $selectedMahasiswa)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Jadwal wawancara berhasil disimpan']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Jadwal gagal disimpan']);
        }
    }
    public function update()
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
        $id = $input['id'] ?? "";
        $id_ruangan = $input['ruangan'] ?? "";
        $jenis_wawancara = $input['jenisWawancara'] ?? "";
        $waktu = $input['waktu'] ?? "";
        $tanggal = $input['tanggal'] ?? "";
        if (empty($id) || empty($id_ruangan) || empty($jenis_wawancara) || empty($waktu) || empty($tanggal)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }
        $wawancara = new Wawancara(
            $id_ruangan,
            $jenis_wawancara,
            $waktu,
            $tanggal
        );
        if ($wawancara->updateWawancara($id, $wawancara)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Jadwal wawancara berhasil diupdate']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Jadwal gagal diupdate']);
        }
    }
    public function delete()
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
        $id = $input['id'] ?? "";
        if (empty($id)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }
        $wawancara = new Wawancara(
            0,
            0,
            0,
            0
        );
        if ($wawancara->deleteWawancara($id)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Jadwal wawancara berhasil dihapus']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Jadwal gagal dihapus']);
        }
    }

}