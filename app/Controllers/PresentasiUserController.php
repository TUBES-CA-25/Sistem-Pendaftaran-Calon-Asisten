<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Model\PresentasiUser;
use App\Model\Presentasi;
use App\Model\NotificationUser;
class PresentasiUserController extends Controller
{
    public function saveJudul()
    {
        $presentasi = new PresentasiUser();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
        $idUser = $_SESSION['user']['id'];
        $judul = $_POST['judul'] ?? '';

        if (empty($judul)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }
        $presentasiUser = new PresentasiUser($idUser, $judul);
        if ($presentasi->saveJudul($presentasiUser)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Judul berhasil disimpan']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Judul gagal disimpan']);
        }
    }

    public function saveMakalahAndPpt()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        ob_start(); // Start output buffering
    
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                ob_clean(); // Clear previous output
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
                return;
            }
    
            if (!isset($_SESSION['user']['id'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
                return;
            }
    
            if (!isset($_FILES['makalah']) || !isset($_FILES['ppt'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'File uploads are invalid']);
                return;
            }
    
            if ($_FILES['makalah']['error'] !== UPLOAD_ERR_OK || $_FILES['ppt']['error'] !== UPLOAD_ERR_OK) {
                $errors = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds maximum size',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit',
                    UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
                ];
                $errorCode = $_FILES['makalah']['error'] !== UPLOAD_ERR_OK ? $_FILES['makalah']['error'] : $_FILES['ppt']['error'];
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $errors[$errorCode] ?? 'Unknown error']);
                return;
            }
    
            $idUser = $_SESSION['user']['id'];
            $presentasiUser = new PresentasiUser(
                id_mahasiswa: $idUser,
                makalah: $_FILES['makalah']['tmp_name'],
                ppt: $_FILES['ppt']['tmp_name'],
                makalahSize: $_FILES['makalah']['size'],
                pptSize: $_FILES['ppt']['size']
            );
    
            if ($presentasiUser->updateMakalahAndPpt($presentasiUser)) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Makalah dan PPT berhasil disimpan']);
                return;
            } else {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Makalah dan PPT gagal disimpan']);
                return;
            }
        } catch (\Exception $e) {
            ob_clean(); 
            header('Content-Type: application/json');
            error_log("Error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            return;
        }
    }
    
    
    
    


    public static function viewAll()
    {
        $presentasi = new PresentasiUser();
        $id = $_SESSION['user']['id'];
        $presentasiUser = $presentasi->getValueForTable($id);
        return $presentasiUser ?? [];
    }
    public static function viewAllForAdmin()
    {
        $presentasi = new Presentasi();
        $data = $presentasi->getAll();
        return $data;
    }

    public static function viewAllAccStatusForAdmin()
    {
        $presentasi = new Presentasi();
        $data = $presentasi->getAllAccStatus();
        return $data;
    }
    public function updateStatusJudul()
    {
        header('Content-Type: application/json');
        $presentasi = new Presentasi();
        $id = $_POST['id'] ?? '';
        $status = $_POST['status'] ?? 1; // Default: 1 = accepted, 2 = rejected

        if (!empty($id)) {
            try {
                $presentasi->updateJudulStatus($id, $status);

                $messageText = $status == 1 ? 'Judul presentasi Anda telah DITERIMA.' : 'Judul presentasi Anda DITOLAK. Silakan cek revisi.';
                $notification = new NotificationUser($id, $messageText); // $id here is id_mahasiswa
                $notification->insert($notification);

                $message = $status == 1 ? 'Judul berhasil diterima.' : 'Judul ditolak. Mahasiswa akan diminta revisi.';
                echo json_encode([
                    'status' => 'success',
                    'message' => $message
                ]);
            } catch (\Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui status judul: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID tidak ditemukan atau kosong.'
            ]);
        }
    }
    public function sendKeteranganAndRevisi()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
        $keterangan = $_POST['message'] ?? '';

        if (empty($keterangan) || empty($id)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }
        try {
            $presentasi = new Presentasi();
            $presentasi->updateIsRevisiAndKeterangan($id, $keterangan);
            
            // Send Notification
            $mahasiswaId = $_POST['userid'] ?? null;
            if ($mahasiswaId) {
                // The NotificationUser model expects `id_mahasiswa` in constructor.
                // However, `NotificationUser::insert` uses the property `id_mahasiswa`.
                // Note: The `NotificationUser` model logic seems to rely on its own `getIdMahasiswaByIdUser` if we pass a user ID, or we pass the direct ID.
                // In `NotificationControllers`, it passes `$idMahasiswa` directly.
                // Let's assume `$mahasiswaId` passed from JS is the correct ID to use (id_mahasiswa from table).
                $notification = new NotificationUser($mahasiswaId, "Pesan Revisi/Keterangan: " . $keterangan);
                $notification->insert($notification);
            }

            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Keterangan berhasil disimpan']);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
