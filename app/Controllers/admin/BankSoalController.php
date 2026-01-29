<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\SoalExam;
use App\Model\BankSoal;

class BankSoalController extends Controller
{
    public function saveSoal()
    {
        // Clean any previous output
        if (ob_get_level()) ob_end_clean();
        
        header('Content-Type: application/json');
        try {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }

            $deskripsi = $_POST['deskripsi'] ?? '';
            $tipeJawaban = $_POST['status_soal'] ?? $_POST['tipeJawaban'] ?? '';
            $pilihan = $_POST['pilihan'] ?? 'bukan soal pilihan';
            $jawaban = $_POST['jawaban'] ?? null;
            $bankId = $_POST['bank_id'] ?? null;

            // Handle Image Upload
            if (isset($_FILES['soal_image']) && $_FILES['soal_image']['error'] === 0) {
                $uploadDir = __DIR__ . '/../../../../res/uploads/soal/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileInfo = pathinfo($_FILES['soal_image']['name']);
                $ext = strtolower($fileInfo['extension']);
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($ext, $allowed)) {
                    $newFilename = uniqid('soal_') . '.' . $ext;
                    $destPath = $uploadDir . $newFilename;
                    
                    if (move_uploaded_file($_FILES['soal_image']['tmp_name'], $destPath)) {
                        $webPath = '/Sistem-Pendaftaran-Calon-Asisten/res/uploads/soal/' . $newFilename;
                        // Append image to description
                        $deskripsi .= '<br><br><img src="' . $webPath . '" class="img-fluid rounded shadow-sm border" style="max-height: 300px;">';
                    }
                }
            }

            if (empty($deskripsi)) {
                throw new \Exception('Deskripsi soal harus diisi');
            }

            $soalExam = new SoalExam(
                $deskripsi,
                $pilihan,
                $jawaban,
                $tipeJawaban
            );

            if ($soalExam->getJawaban() === null) {
                $soalExam->saveWithoutAnswer($soalExam, $bankId);
                echo json_encode([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Soal berhasil disimpan'
                ]);
            } else {
                $soalExam->save($soalExam, $bankId);
                echo json_encode([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Soal berhasil disimpan'
                ]);
            }

            http_response_code(200);
            exit();

        } catch (\Exception $e) {
            error_log("Error in saveSoal: " . $e->getMessage());

            echo json_encode([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            http_response_code(500);
        }
    }

    public function uploadImage()
    {
        // Clean any previous output
        if (ob_get_level()) ob_end_clean();
        
        header('Content-Type: application/json');
        
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }

            if (!isset($_FILES['image'])) {
                throw new \Exception('No image uploaded');
            }

            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Upload error: ' . $file['error']);
            }

            // Validate type
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            
            if (!in_array($mime, $allowed)) {
                throw new \Exception('Invalid file type. Only JPG, PNG, GIF, WEBP allowed.');
            }

            // Create directory if not exists
            $uploadDir = __DIR__ . '/../../../res/uploads/soal_content/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFilename = 'img_' . time() . '_' . uniqid() . '.' . $ext;
            $destPath = $uploadDir . $newFilename;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $webPath = '/Sistem-Pendaftaran-Calon-Asisten/res/uploads/soal_content/' . $newFilename;
                
                echo json_encode([
                    'data' => [
                        'filePath' => $webPath
                    ]
                ]);
            } else {
                throw new \Exception('Failed to move uploaded file');
            }

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function getBankDetails()
    {
        // Clean any previous output
        if (ob_get_level()) ob_end_clean();
        
        header('Content-Type: application/json');
        try {
            $bankId = $_GET['id'] ?? null;
            
            if (!$bankId) {
                throw new \Exception('Bank ID tidak ditemukan');
            }
            
            $bankSoal = new BankSoal();
            $bank = $bankSoal->getBankById($bankId);
            
            if (!$bank) {
                throw new \Exception('Bank soal tidak ditemukan');
            }
            
            echo json_encode([
                'status' => 'success',
                'bank' => [
                    'id' => $bank['id'],
                    'nama' => $bank['nama'],
                    'jumlah_soal' => $bank['jumlah_soal'] ?? 0,
                    'jumlah_pg' => $bank['jumlah_pg'] ?? 0,
                    'jumlah_essay' => $bank['jumlah_essay'] ?? 0
                ]
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
