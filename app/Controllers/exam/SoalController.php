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
    
    /**
     * Validate file type - only accept CSV and Excel formats
     */
    private function validateFileType($filename, $mimeType) {
        $allowedExtensions = ['csv', 'xls', 'xlsx'];
        $allowedMimeTypes = [
            'text/csv',
            'text/plain',
            'application/csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/octet-stream' // Some browsers send this for Excel files
        ];
        
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            return [
                'valid' => false,
                'error' => "Format file tidak didukung. Hanya menerima file CSV (.csv) atau Excel (.xls, .xlsx). File Anda: .{$fileExtension}"
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Validate template headers match expected format
     */
    private function validateTemplateHeaders($headers) {
        $expectedHeaders = [
            'Deskripsi Soal',
            'Tipe Soal (pilihan_ganda/essay)',
            'Pilihan A',
            'Pilihan B',
            'Pilihan C',
            'Pilihan D',
            'Pilihan E (Opsional)',
            'Jawaban Benar (A/B/C/D/E atau Kunci Jawaban)'
        ];
        
        $errors = [];
        
        // Check column count
        if (count($headers) < 8) {
            $errors[] = "Template harus memiliki 8 kolom. File Anda memiliki " . count($headers) . " kolom.";
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Check each header name (case-insensitive, trim whitespace)
        for ($i = 0; $i < 8; $i++) {
            $expected = trim($expectedHeaders[$i]);
            $actual = trim($headers[$i] ?? '');
            
            // Normalize for comparison (remove extra spaces, case-insensitive)
            $expectedNorm = strtolower(preg_replace('/\s+/', ' ', $expected));
            $actualNorm = strtolower(preg_replace('/\s+/', ' ', $actual));
            
            if ($expectedNorm !== $actualNorm) {
                $errors[] = "Kolom ke-" . ($i + 1) . " tidak sesuai. Diharapkan: '{$expected}', Ditemukan: '{$actual}'";
            }
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Validate individual row data
     */
    private function validateRowData($row, $rowNumber) {
        $errors = [];
        
        // Check minimum column count
        if (count($row) < 8) {
            $errors[] = "Baris {$rowNumber}: Jumlah kolom tidak lengkap (minimal 8 kolom diperlukan)";
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Validate Deskripsi (column 0)
        $deskripsi = trim($row[0] ?? '');
        if (empty($deskripsi)) {
            $errors[] = "Baris {$rowNumber}: Deskripsi soal harus diisi";
        }
        
        // Validate Tipe Soal (column 1)
        $tipeRaw = strtolower(trim($row[1] ?? ''));
        $validTypes = ['pilihan_ganda', 'essay', 'pilihan ganda', 'pg'];
        $isPG = (strpos($tipeRaw, 'ganda') !== false || $tipeRaw === 'pg');
        $isEssay = (strpos($tipeRaw, 'essay') !== false);
        
        if (!$isPG && !$isEssay) {
            $errors[] = "Baris {$rowNumber}: Tipe soal tidak valid. Gunakan 'pilihan_ganda' atau 'essay'. Ditemukan: '{$row[1]}'";
        }
        
        // Validate Pilihan Ganda options (columns 2-5)
        if ($isPG) {
            $requiredOptions = ['A', 'B', 'C', 'D'];
            for ($i = 2; $i <= 5; $i++) {
                $option = trim($row[$i] ?? '');
                if (empty($option)) {
                    $optionLabel = $requiredOptions[$i - 2];
                    $errors[] = "Baris {$rowNumber}: Pilihan {$optionLabel} harus diisi untuk soal pilihan ganda";
                }
            }
            
            // Validate jawaban for PG (should be A/B/C/D/E)
            $jawaban = strtoupper(trim($row[7] ?? ''));
            if (!in_array($jawaban, ['A', 'B', 'C', 'D', 'E'])) {
                $errors[] = "Baris {$rowNumber}: Jawaban untuk pilihan ganda harus berupa huruf A, B, C, D, atau E. Ditemukan: '{$row[7]}'";
            }
        }
        
        // Validate Jawaban (column 7)
        $jawaban = trim($row[7] ?? '');
        if (empty($jawaban)) {
            $errors[] = "Baris {$rowNumber}: Jawaban harus diisi";
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }
        
        return ['valid' => true];
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
            $fileName = $_FILES['file']['name'];
            $fileType = mime_content_type($fileTmpPath);
            
            // Validate file type
            $fileValidation = $this->validateFileType($fileName, $fileType);
            if (!$fileValidation['valid']) {
                throw new \Exception($fileValidation['error']);
            }
            
            // Determine parsing method
            $data = [];
            $headers = [];
            
            // Parse file and extract headers
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
                        $cols = $row->getElementsByTagName('td');
                        if ($cols->length === 0) {
                            $cols = $row->getElementsByTagName('th');
                        }
                        
                        if ($isHeader) {
                            // Extract headers
                            foreach ($cols as $col) {
                                $headers[] = trim($col->textContent);
                            }
                            $isHeader = false;
                            continue;
                        }
                        
                        if ($cols->length >= 8) {
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
                            $headers = $row;
                            $isHeader = false;
                            continue;
                        }
                        $data[] = $row;
                    }
                    fclose($handle);
                }
            }
            
            // Validate headers
            $headerValidation = $this->validateTemplateHeaders($headers);
            if (!$headerValidation['valid']) {
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Format template tidak sesuai',
                    'validation_errors' => $headerValidation['errors']
                ]);
                http_response_code(400);
                return;
            }

            if (empty($data)) {
                 throw new \Exception('File kosong atau tidak ada data untuk diimport');
            }
            
            // Validate all rows first
            $validationErrors = [];
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because: +1 for 1-based indexing, +1 for header row
                $rowValidation = $this->validateRowData($row, $rowNumber);
                
                if (!$rowValidation['valid']) {
                    $validationErrors = array_merge($validationErrors, $rowValidation['errors']);
                }
            }
            
            // If any validation errors, return them all
            if (!empty($validationErrors)) {
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Terdapat kesalahan pada data yang diimport',
                    'validation_errors' => $validationErrors
                ]);
                http_response_code(400);
                return;
            }

            $successCount = 0;
            $soalModel = new SoalExam();

            foreach ($data as $row) {
                // Row structure based on template:
                // 0: Deskripsi, 1: Tipe, 2: A, 3: B, 4: C, 5: D, 6: E, 7: Jawaban

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

    public function exportSoal() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }

            $bankId = $_GET['bank_id'] ?? null;
            if (!$bankId) {
                throw new \Exception('Bank Soal ID tidak valid');
            }

            // Get Bank Info
            $bankModel = new \App\Model\Exam\BankSoal();
            $bank = $bankModel->getBankById($bankId);
            if (!$bank) {
                throw new \Exception('Bank soal tidak ditemukan');
            }

            // Get Soal
            $soalModel = new SoalExam();
            $soalList = $soalModel->getSoalByBankId($bankId);

            // Clean output buffer to ensure clean file download
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Use CSV extension to avoid Excel format warning
            $filename = "Export_Bank_" . preg_replace('/[^a-zA-Z0-9]/', '_', $bank['nama']) . "_" . date('Ymd') . ".csv";
            
            // Force download headers
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            // Open output stream
            $output = fopen('php://output', 'w');
            
            // Add BOM (Byte Order Mark) for Excel to recognize UTF-8 automatically
            fputs($output, "\xEF\xBB\xBF");
            
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
            fputcsv($output, $headers);

            foreach ($soalList as $soal) {
                
                // 1. Deskripsi
                $deskripsi = $soal['deskripsi'];
                
                // 2. Tipe
                $isPG = ($soal['status_soal'] ?? '') === 'pilihan_ganda';
                $tipe = ($isPG ? 'pilihan_ganda' : 'essay');
                
                // Prepare options
                $opts = ['A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''];
                
                if ($isPG && !empty($soal['pilihan'])) {
                    $pilihanRaw = html_entity_decode($soal['pilihan']);
                    
                    // Regex to match "A. content" patterns
                    preg_match_all('/([A-E])\.\s*(.*?)(?=(?:,\s*[A-E]\.)|$)/s', $pilihanRaw, $matches, PREG_SET_ORDER);
                    
                    if (!empty($matches)) {
                        foreach ($matches as $match) {
                            $opts[$match[1]] = trim($match[2]);
                        }
                    } else {
                        // Fallback manual split
                        $parts = explode(',', $pilihanRaw);
                        foreach ($parts as $part) {
                            $part = trim($part);
                            $firstChar = strtoupper(substr($part, 0, 1));
                            if (isset($opts[$firstChar]) && substr($part, 1, 1) === '.') {
                                $opts[$firstChar] = trim(substr($part, 2));
                            }
                        }
                    }
                }
                
                // Construct Row Array
                $row = [
                    $deskripsi,
                    $tipe,
                    $opts['A'],
                    $opts['B'],
                    $opts['C'],
                    $opts['D'],
                    $opts['E'],
                    $soal['jawaban']
                ];
                
                // Write row to CSV
                fputcsv($output, $row);
            }

            fclose($output);
            exit();

        } catch (\Exception $e) {
           die("Error export: " . $e->getMessage());
        }
    }
}