<?php
namespace App\Controllers\exam;

use App\Core\Controller;
use App\Model\exam\SoalExam;

class SoalController extends Controller
{
    public function saveSoal()
    {
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

    public function deleteSoal()
    {
        header('Content-Type: application/json');
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }
            $id = $_POST['id'] ?? '';
            $soal = new SoalExam(
                null,
                null,
                null,
                null
            );
            $soal->deleteSoal($id);
            echo json_encode([
                'success' => true,
                'status' => 'success',
                'message' => 'Soal berhasil dihapus'
            ]);
            http_response_code(200);
        } catch (\Exception $e) {
            error_log("Error in deleteSoal: " . $e->getMessage());

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
        header('Content-Type: application/json');
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }

            $id = $_POST['id'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $tipeJawaban = $_POST['status_soal'] ?? $_POST['tipeJawaban'] ?? '';
            $pilihan = $_POST['pilihan'] ?? 'bukan soal pilihan';
            $jawaban = $_POST['jawaban'] ?? 'soal tidak mempunyai jawaban';

            $soalExam = new SoalExam(
                $deskripsi,
                $pilihan,
                $jawaban,
                $tipeJawaban
            );

            $soalExam->updateSoal($id, $soalExam);

            echo json_encode([
                'success' => true,
                'status' => 'success',
                'message' => 'Soal berhasil diupdate'
            ]);
            http_response_code(200);

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

    // Bank Soal Methods
    public function createBank() {
        header('Content-Type: application/json');
        try {
            $nama = $_POST['nama'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $token = $_POST['token'] ?? '';
            
            if (empty($nama)) {
                echo json_encode(['status' => 'error', 'message' => 'Nama bank soal harus diisi']);
                return;
            }
            
            $bankModel = new \App\Model\Exam\BankSoal();
            if ($bankModel->save($nama, $deskripsi, $token)) {
                $newId = $bankModel->getLastInsertId();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Bank soal berhasil dibuat',
                    'data' => [
                        'id' => $newId,
                        'nama' => $nama,
                        'deskripsi' => $deskripsi
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal membuat bank soal']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateBank() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? 0;
            $nama = $_POST['nama'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $token = $_POST['token'] ?? '';
            
            $bankModel = new \App\Model\Exam\BankSoal();
            if ($bankModel->updateBank($id, $nama, $deskripsi, $token)) {
                echo json_encode(['status' => 'success', 'message' => 'Bank soal berhasil diupdate']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate bank soal']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteBank() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? 0;
            $bankModel = new \App\Model\Exam\BankSoal();
            
            if ($bankModel->deleteBank($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Bank soal berhasil dihapus']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus bank soal']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getBankQuestions() {
        header('Content-Type: application/json');
        try {
            $bankId = $_POST['bank_id'] ?? 0;
            $soalModel = new SoalExam();
            $questions = $soalModel->getSoalByBankId($bankId);
            
            echo json_encode(['status' => 'success', 'data' => $questions]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function activateBank() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? 0;
            $bankModel = new \App\Model\Exam\BankSoal();
            
            if ($bankModel->setActiveBank($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Bank soal berhasil diaktifkan']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengaktifkan bank soal']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        // Clean output buffer to remove any prior whitespace or echo
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $filename = "template_import_soal.xls";
        
        // Force download headers
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Transfer-Encoding: binary");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<!--[if gte mso 9]>';
        echo '<xml>';
        echo '<x:ExcelWorkbook>';
        echo '<x:ExcelWorksheets>';
        echo '<x:ExcelWorksheet>';
        echo '<x:Name>Sheet1</x:Name>';
        echo '<x:WorksheetOptions>';
        echo '<x:DisplayGridlines/>';
        echo '</x:WorksheetOptions>';
        echo '</x:ExcelWorksheet>';
        echo '</x:ExcelWorksheets>';
        echo '</x:ExcelWorkbook>';
        echo '</xml>';
        echo '<![endif]-->';
        echo '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/>';
        echo '</head>';
        echo '<body>';
        echo '<table border="1">';
        
        // Headers
        $headers = [
            'Deskripsi Soal',
            'Tipe Soal (pilihan_ganda/essay)',
            'Pilihan A',
            'Pilihan B',
            'Pilihan C',
            'Pilihan D',
            'Pilihan E (Opsional)',
            'Jawaban Benar (A/B/C/D/E atau Kunci Jawaban)'
        ];
        
        echo '<tr>';
        foreach ($headers as $header) {
            echo '<th style="background-color: #f0f0f0; font-weight: bold;">' . $header . '</th>';
        }
        echo '</tr>';

        // Example Data 1 (PG)
        echo '<tr>';
        echo '<td>Siapakah penemu bola lampu?</td>';
        echo '<td>pilihan_ganda</td>';
        echo '<td>Thomas Edison</td>';
        echo '<td>Nikola Tesla</td>';
        echo '<td>Albert Einstein</td>';
        echo '<td>Isaac Newton</td>';
        echo '<td></td>';
        echo '<td>A</td>';
        echo '</tr>';

        // Example Data 2 (Essay)
        echo '<tr>';
        echo '<td>Jelaskan pengertian fotosintesis!</td>';
        echo '<td>essay</td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td>Proses tumbuhan membuat makanan dengan bantuan sinar matahari.</td>';
        echo '</tr>';

        echo '</table>';
        echo '</body>';
        echo '</html>';
        
        // Ensure no further output
        exit();
    }
    
    public function importSoal() {
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
                throw new \Exception('Bank Soal ID tidak valid');
            }

            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('File tidak ditemukan atau terjadi kesalahan upload');
            }

            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileType = mime_content_type($fileTmpPath);
            
            // Determine parsing method
            $data = [];
            
            // 1. Try CSV parsing first
            $csvData = [];
            if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
                // Check if starts with HTML tag (indicating it's our fake XLS)
                $firstLine = fgets($handle);
                rewind($handle);
                
                if (strpos($firstLine, '<html') !== false || strpos($firstLine, '<xml') !== false) {
                    // It's the HTML-XLS format
                    $content = file_get_contents($fileTmpPath);
                    $dom = new \DOMDocument();
                    libxml_use_internal_errors(true);
                    $dom->loadHTML($content);
                    libxml_clear_errors();
                    
                    $rows = $dom->getElementsByTagName('tr');
                    $isHeader = true;
                    
                    foreach ($rows as $row) {
                        if ($isHeader) {
                            $isHeader = false; // Skip header row
                            continue;
                        }
                        
                        $cols = $row->getElementsByTagName('td');
                        if ($cols->length >= 8) { // We expect 8 columns
                            $rowData = [];
                            foreach ($cols as $col) {
                                $rowData[] = trim($col->textContent);
                            }
                            $data[] = $rowData;
                        }
                    }
                } else {
                    // It's a regular CSV
                    $isHeader = true;
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if ($isHeader) {
                            $isHeader = false;
                            continue;
                        }
                        $data[] = $row;
                    }
                    fclose($handle);
                }
            }

            if (empty($data)) {
                 throw new \Exception('File kosong atau format tidak dikenali');
            }

            $successCount = 0;
            $soalModel = new SoalExam();

            foreach ($data as $row) {
                // Row structure based on template:
                // 0: Deskripsi, 1: Tipe, 2: A, 3: B, 4: C, 5: D, 6: E, 7: Jawaban
                
                if (count($row) < 8) continue; // Skip invalid rows

                $deskripsi = $row[0];
                $tipeRaw = strtolower($row[1]);
                $tipe = (strpos($tipeRaw, 'ganda') !== false) ? 'pilihan_ganda' : 'essay';
                
                $pilihan = '';
                if ($tipe === 'pilihan_ganda') {
                    $pilihan = "A. {$row[2]}, B. {$row[3]}, C. {$row[4]}, D. {$row[5]}";
                    if (!empty($row[6])) {
                        $pilihan .= ", E. {$row[6]}";
                    }
                }

                $jawaban = $row[7];
                
                // Create Soal Object
                $soal = new SoalExam(
                    $deskripsi,
                    $pilihan,
                    $jawaban,
                    $tipe
                );

                if ($soalModel->save($soal, $bankId)) {
                    $successCount++;
                }
            }

            echo json_encode([
                'success' => true,
                'status' => 'success',
                'message' => "Berhasil mengimport {$successCount} soal",
                'count' => $successCount
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            http_response_code(500);
        }
    }
}