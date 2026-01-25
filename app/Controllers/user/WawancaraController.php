<?php
namespace App\Controllers\user;
use App\Core\Controller;
use App\Model\Wawancara\Wawancara;
class WawancaraController extends Controller
{
    public static function getAll()
    {
        $absensiModel = new \App\Model\User\Absensi();
        $absensiData = $absensiModel->getAbsensi(); // Data from 'absensi' table
        
        // Fetch Schedules from 'wawancara' table
        $wawancaraModel = new \App\Model\Wawancara\Wawancara();
        $schedules = $wawancaraModel->getAll(); // Fetches all schedules with student info

        // Index Absensi by ID Mahasiswa
        $mergedData = [];
        foreach ($absensiData as $row) {
            // We need a way to link by id_mahasiswa. 
            // Absensi query returns 'id' as absensi id. 
            // We need to check if Absensi model returns id_mahasiswa.
            // Let's assume we can get it or we have to modify Absensi model too.
            // Wait, Absensi::getAbsensi() (lines 29-44) returns: id, nama_lengkap, stambuk... 
            // It does NOT return id_mahasiswa explicitly in select! 
            // We need to add a.id_mahasiswa to select in Absensi model first.
            // For now, let's index by stambuk or name, which is risky. 
            // BETTER: Modify Absensi model to include id_mahasiswa.
            
            // Assuming we fix Absensi model first.
            $mergedData[$row['stambuk']] = $row; 
        }

        // Process Schedules to add "Terjadwal" status if Absensi is missing
        foreach ($schedules as $sch) {
            $stambuk = $sch['stambuk'];
            if (!isset($mergedData[$stambuk])) {
                // If no absensi record, create a placeholder entry
                $mergedData[$stambuk] = [
                    'id' => null, // No absensi ID
                    'nama_lengkap' => $sch['nama_lengkap'],
                    'stambuk' => $stambuk,
                    'absensi_wawancara_I' => null, // Will be interpreted as check schedule
                    'absensi_wawancara_II' => null,
                    'wawancara_I_schedule' => strpos($sch['jenis_wawancara'], 'I') !== false,
                    'wawancara_II_schedule' => strpos($sch['jenis_wawancara'], 'II') !== false,
                ];
            } else {
                // If exists, just mark schedule existence flag
                $mergedData[$stambuk]['wawancara_I_schedule'] = strpos($sch['jenis_wawancara'], 'I') !== false;
                $mergedData[$stambuk]['wawancara_II_schedule'] = strpos($sch['jenis_wawancara'], 'II') !== false;
            }
        }

        return array_values($mergedData);
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
            // Updated: Fetch all activities instead of just interviews
            $data = $wawancara->getJadwalKegiatanById($id);
            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            error_log("Error in getAllById (Jadwal Kegiatan): " . $e->getMessage());
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