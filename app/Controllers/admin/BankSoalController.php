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
            $imageUrl = null;

            // Debug logging
            error_log("POST data: " . print_r($_POST, true));
            error_log("FILES data: " . print_r($_FILES, true));

            // Handle Image Upload
            if (isset($_FILES['soal_image']) && $_FILES['soal_image']['error'] === 0) {
                // Get absolute path to project root
                $projectRoot = dirname(dirname(dirname(__DIR__)));
                $uploadDir = $projectRoot . DIRECTORY_SEPARATOR . 'res' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'soal' . DIRECTORY_SEPARATOR;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileInfo = pathinfo($_FILES['soal_image']['name']);
                $ext = strtolower($fileInfo['extension']);
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($ext, $allowed)) {
                    $newFilename = uniqid('soal_') . '.' . $ext;
                    $destPath = $uploadDir . $newFilename;

                    if (move_uploaded_file($_FILES['soal_image']['tmp_name'], $destPath)) {
                        // Use relative path from web root
                        $imageUrl = 'res/uploads/soal/' . $newFilename;
                        error_log("Image uploaded successfully to: " . $destPath);
                    } else {
                        error_log("Failed to move uploaded file. Tmp: " . $_FILES['soal_image']['tmp_name'] . ", Dest: " . $destPath);
                    }
                } else {
                    error_log("File extension not allowed: " . $ext);
                }
            } elseif (isset($_FILES['soal_image'])) {
                error_log("Image upload error code: " . $_FILES['soal_image']['error']);
            }

            if (empty($deskripsi)) {
                throw new \Exception('Deskripsi soal harus diisi');
            }

            if (empty($bankId)) {
                throw new \Exception('Bank soal tidak ditemukan');
            }

            $soalExam = new SoalExam(
                $deskripsi,
                $pilihan,
                $jawaban,
                $tipeJawaban,
                $imageUrl
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

    public function updateSoal()
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

            $id = $_POST['id'] ?? null;
            $deskripsi = $_POST['deskripsi'] ?? '';
            $tipeJawaban = $_POST['status_soal'] ?? '';
            $pilihan = $_POST['pilihan'] ?? 'bukan soal pilihan';
            $jawaban = $_POST['jawaban'] ?? null;
            $existingImage = $_POST['existing_image'] ?? null;
            $imageUrl = $existingImage; // Keep existing image by default

            if (!$id) {
                throw new \Exception('ID soal tidak ditemukan');
            }

            // Handle Image Upload - only if new file uploaded
            if (isset($_FILES['soal_image_edit']) && $_FILES['soal_image_edit']['error'] === 0) {
                // Get absolute path to project root
                $projectRoot = dirname(dirname(dirname(__DIR__)));
                $uploadDir = $projectRoot . DIRECTORY_SEPARATOR . 'res' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'soal' . DIRECTORY_SEPARATOR;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileInfo = pathinfo($_FILES['soal_image_edit']['name']);
                $ext = strtolower($fileInfo['extension']);
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($ext, $allowed)) {
                    $newFilename = uniqid('soal_') . '.' . $ext;
                    $destPath = $uploadDir . $newFilename;

                    if (move_uploaded_file($_FILES['soal_image_edit']['tmp_name'], $destPath)) {
                        // Use relative path from web root
                        $imageUrl = 'res/uploads/soal/' . $newFilename;
                        error_log("Image uploaded successfully to: " . $destPath);
                    }
                }
            }

            if (empty($deskripsi)) {
                throw new \Exception('Deskripsi soal harus diisi');
            }

            // Update query
            $sql = "UPDATE soal SET deskripsi = :deskripsi, image_url = :image_url, pilihan = :pilihan, jawaban = :jawaban, status_soal = :status_soal, modified = NOW() WHERE id = :id";
            $stmt = \App\Core\Model::getDB()->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->bindParam(':deskripsi', $deskripsi);
            $stmt->bindParam(':image_url', $imageUrl);
            $stmt->bindParam(':pilihan', $pilihan);
            $stmt->bindParam(':jawaban', $jawaban);
            $stmt->bindParam(':status_soal', $tipeJawaban);

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Soal berhasil diupdate'
                ]);
            } else {
                throw new \Exception('Gagal mengupdate soal');
            }

            http_response_code(200);
            exit();

        } catch (\Exception $e) {
            error_log("Error in updateSoal: " . $e->getMessage());
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
                // Use relative path from web root
                $webPath = 'res/uploads/soal_content/' . $newFilename;

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

    public function getBankQuestions()
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

            $bankId = $_POST['bank_id'] ?? null;
            
            if (!$bankId) {
                throw new \Exception('Bank ID tidak ditemukan');
            }

            $soalExam = new SoalExam();
            $questions = $soalExam->getSoalByBankId($bankId);
            
            echo json_encode([
                'status' => 'success',
                'data' => $questions
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function deleteSoal()
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

            $soalId = $_POST['id'] ?? null;

            if (!$soalId) {
                throw new \Exception('ID soal tidak ditemukan');
            }

            // Get soal data first to retrieve image path
            $db = \App\Core\Model::getDB();
            $stmt = $db->prepare("SELECT image_url FROM soal WHERE id = :id");
            $stmt->bindParam(':id', $soalId, \PDO::PARAM_INT);
            $stmt->execute();
            $soal = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$soal) {
                throw new \Exception('Soal tidak ditemukan');
            }

            // Delete image file if exists
            if (!empty($soal['image_url'])) {
                $projectRoot = dirname(dirname(dirname(__DIR__)));
                $imagePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $soal['image_url']);

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    error_log("Deleted image file: " . $imagePath);
                }
            }

            // Delete soal from database
            $deleteStmt = $db->prepare("DELETE FROM soal WHERE id = :id");
            $deleteStmt->bindParam(':id', $soalId, \PDO::PARAM_INT);

            if ($deleteStmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Soal berhasil dihapus'
                ]);
            } else {
                throw new \Exception('Gagal menghapus soal');
            }

        } catch (\Exception $e) {
            error_log("Error in deleteSoal: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
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
    public function createBank()
    {
        ob_clean();
        header('Content-Type: application/json');
        
        try {
            if (empty($_SESSION['user'])) throw new \Exception('Unauthorized');
            
            $nama = $_POST['nama'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $token = $_POST['token'] ?? '';
            
            if (empty($nama)) throw new \Exception('Nama bank soal wajib diisi');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->save($nama, $deskripsi, $token)) {
                $newId = $bankSoal->getLastInsertId();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Bank soal berhasil dibuat',
                    'id' => $newId
                ]);
            } else {
                throw new \Exception('Gagal menyimpan bank soal');
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateBank()
    {
        ob_clean();
        header('Content-Type: application/json');
        
        try {
            if (empty($_SESSION['user'])) throw new \Exception('Unauthorized');
            
            $id = $_POST['id'] ?? null;
            $nama = $_POST['nama'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $token = $_POST['token'] ?? '';
            
            if (!$id) throw new \Exception('ID Bank tidak valid');
            if (empty($nama)) throw new \Exception('Nama bank soal wajib diisi');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->updateBank($id, $nama, $deskripsi, $token)) {
                echo json_encode(['status' => 'success', 'message' => 'Bank soal berhasil diperbarui']);
            } else {
                throw new \Exception('Gagal memperbarui bank soal');
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteBank()
    {
        ob_clean();
        header('Content-Type: application/json');
        
        try {
            if (empty($_SESSION['user'])) throw new \Exception('Unauthorized');
            
            $id = $_POST['id'] ?? null;
            if (!$id) throw new \Exception('ID Bank tidak valid');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->deleteBank($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Bank soal berhasil dihapus']);
            } else {
                throw new \Exception('Gagal menghapus bank soal');
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function activateBank()
    {
        ob_clean();
        header('Content-Type: application/json');
        
        try {
            if (empty($_SESSION['user'])) throw new \Exception('Unauthorized');
            
            $id = $_POST['id'] ?? $_POST['bank_id'] ?? null;
            if (!$id) throw new \Exception('ID Bank tidak valid');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->setActiveBank($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Bank soal berhasil diaktifkan']);
            } else {
                throw new \Exception('Gagal mengaktifkan bank soal');
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deactivateBank()
    {
        ob_clean();
        header('Content-Type: application/json');
        
        try {
            if (empty($_SESSION['user'])) throw new \Exception('Unauthorized');
            
            $id = $_POST['id'] ?? $_POST['bank_id'] ?? null;
            if (!$id) throw new \Exception('ID Bank tidak valid');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->deactivateBank($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Bank soal berhasil dinonaktifkan']);
            } else {
                throw new \Exception('Gagal menonaktifkan bank soal');
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
