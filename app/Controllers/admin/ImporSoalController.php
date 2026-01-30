<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\SoalExam;
use App\Model\BankSoal;

class ImporSoalController extends Controller
{
    public function downloadTemplate()
    {
        // Try to serve physical file if exists
        $rootPath = dirname(__DIR__, 3);
        $physicalFilePath = $rootPath . '/public/Assets/Downloads/template_soal.csv';

        if (file_exists($physicalFilePath)) {
            // Clean ALL output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="template_soal.csv"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($physicalFilePath));

            readfile($physicalFilePath);
            exit;
        }

        // Fallback: Headers for the template
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

        // Clean ALL output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="template_soal.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $output = fopen('php://output', 'w');

        // Add BOM for Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, $headers);

        // Add example rows (pilihan ganda and essay)
        $examplePG = [
            'Contoh Soal Pilihan Ganda: Berapa hasil dari 2 + 2?',
            'pilihan_ganda',
            '2',
            '3',
            '4',
            '5',
            '6',
            'C'
        ];
        fputcsv($output, $examplePG);

        $exampleEssay = [
            'Contoh Soal Essay: Jelaskan pengertian MVC (Model-View-Controller)',
            'essay',
            '',
            '',
            '',
            '',
            '',
            'MVC adalah pola arsitektur software yang memisahkan aplikasi menjadi tiga komponen utama: Model (data), View (tampilan), dan Controller (logika)'
        ];
        fputcsv($output, $exampleEssay);

        fclose($output);
        exit;
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

        // Helper function to clean and normalize text to ASCII only
        $cleanText = function($text) {
            // Remove BOM
            $text = str_replace("\xEF\xBB\xBF", '', $text);
            // Convert to ASCII, removing all non-ASCII characters (including Unicode quotes, etc.)
            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            // Remove any remaining control characters
            $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
            // Trim and normalize spaces
            $text = trim(preg_replace('/\s+/', ' ', $text));
            return strtolower($text);
        };

        // Check each header name with flexible matching
        for ($i = 0; $i < 8; $i++) {
            $expected = $expectedHeaders[$i];
            $actual = $headers[$i] ?? '';

            // Clean both expected and actual
            $expectedClean = $cleanText($expected);
            $actualClean = $cleanText($actual);

            // Log for debugging
            error_log("Header validation [{$i}]: Expected='{$expectedClean}' (len=" . strlen($expectedClean) . "), Actual='{$actualClean}' (len=" . strlen($actualClean) . ")");
            error_log("  Raw bytes - Expected: " . bin2hex($expected) . ", Actual: " . bin2hex($actual));

            // More flexible matching - check if actual contains expected keywords
            $isValid = false;

            if ($i === 0) {
                // Column 1: "Deskripsi Soal"
                $isValid = (strpos($actualClean, 'deskripsi') !== false && strpos($actualClean, 'soal') !== false);
            } elseif ($i === 1) {
                // Column 2: "Tipe Soal"
                $isValid = (strpos($actualClean, 'tipe') !== false && strpos($actualClean, 'soal') !== false);
            } elseif ($i >= 2 && $i <= 5) {
                // Columns 3-6: "Pilihan A/B/C/D"
                $letter = chr(97 + ($i - 2)); // a, b, c, d (lowercase)
                $isValid = (strpos($actualClean, 'pilihan') !== false && strpos($actualClean, $letter) !== false);
            } elseif ($i === 6) {
                // Column 7: "Pilihan E"
                $isValid = (strpos($actualClean, 'pilihan') !== false && strpos($actualClean, 'e') !== false);
            } elseif ($i === 7) {
                // Column 8: "Jawaban Benar" or "Jawaban"
                $isValid = (strpos($actualClean, 'jawaban') !== false);
            }

            if (!$isValid) {
                $errors[] = "Kolom ke-" . ($i + 1) . " tidak sesuai. Diharapkan: '{$expected}', Ditemukan: '{$actual}'";
            }
        }

        if (!empty($errors)) {
            error_log("Header validation failed: " . implode('; ', $errors));
            return ['valid' => false, 'errors' => $errors];
        }

        error_log("Header validation passed!");
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
        // Clean any previous output FIRST
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                error_log("Import failed: User not authenticated");
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi. Silakan login kembali.'
                ]);
                http_response_code(403);
                exit;
            }

            $bankId = $_POST['bank_id'] ?? null;
            if (!$bankId) {
                error_log("Import failed: No bank_id provided");
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Bank Soal ID tidak valid'
                ]);
                http_response_code(400);
                exit;
            }

            // Validate that bank_id exists in database
            $bankSoalModel = new BankSoal();
            $bank = $bankSoalModel->getBankById($bankId);

            if (!$bank) {
                error_log("Import failed: Bank ID {$bankId} not found in database");
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => "Bank Soal dengan ID {$bankId} tidak ditemukan. Silakan refresh halaman dan coba lagi."
                ]);
                http_response_code(404);
                exit;
            }

            error_log("Import: Bank validated - ID: {$bankId}, Name: {$bank['nama']}");


            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $errorMsg = 'File tidak ditemukan atau terjadi kesalahan upload';
                if (isset($_FILES['file']['error'])) {
                    $errorMsg .= ' (Error code: ' . $_FILES['file']['error'] . ')';
                }
                error_log("Import failed: " . $errorMsg);
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => $errorMsg
                ]);
                http_response_code(400);
                exit;
            }

            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileType = mime_content_type($fileTmpPath);

            error_log("Import attempt: file={$fileName}, type={$fileType}, bank_id={$bankId}");

            // Validate file type
            $fileValidation = $this->validateFileType($fileName, $fileType);
            if (!$fileValidation['valid']) {
                error_log("Import failed: Invalid file type - " . $fileValidation['error']);
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => $fileValidation['error']
                ]);
                http_response_code(400);
                exit;
            }

            // Determine file extension
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Parse file based on extension
            $data = [];
            $headers = [];

            if ($fileExtension === 'csv') {
                // Parse CSV file with better encoding handling
                if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
                    $isHeader = true;
                    $rowCount = 0;
                    $delimiter = ",";
                    // Detect delimiter from first line
                    $firstLine = fgets($handle);
                    if ($firstLine !== FALSE) {
                        $commaCount = substr_count($firstLine, ',');
                        $semicolonCount = substr_count($firstLine, ';');
                        if ($semicolonCount > $commaCount) {
                            $delimiter = ";";
                        }
                        rewind($handle);
                    }

                    while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                        $rowCount++;

                        // Skip empty rows
                        if (empty(array_filter($row))) {
                            continue;
                        }

                        if ($isHeader) {
                            // Remove BOM if present
                            if (!empty($row[0])) {
                                $row[0] = str_replace("\xEF\xBB\xBF", '', $row[0]);
                            }
                            $headers = array_map('trim', $row);
                            $isHeader = false;
                            error_log("CSV Headers (delimiter: {$delimiter}): " . implode(', ', $headers));
                            continue;
                        }
                        if (count($row) >= 8) {
                            $data[] = array_map('trim', $row);
                        } else {
                            error_log("Skipping row {$rowCount}: insufficient columns (" . count($row) . ")");
                        }
                    }
                    fclose($handle);
                    error_log("CSV parsed: {$rowCount} total rows, " . count($data) . " data rows");
                }
            } elseif ($fileExtension === 'xls' || $fileExtension === 'xlsx') {
                // Try to parse as HTML-based Excel first
                $content = file_get_contents($fileTmpPath);

                // Check if it's HTML-based Excel
                if (strpos($content, '<html') !== false || strpos($content, '<table') !== false) {
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

                        if ($cols->length === 0) continue;

                        $rowData = [];
                        foreach ($cols as $col) {
                            $rowData[] = trim($col->textContent);
                        }

                        if ($isHeader) {
                            $headers = $rowData;
                            $isHeader = false;
                            error_log("Excel Headers: " . implode(', ', $headers));
                            continue;
                        }

                        if (count($rowData) >= 8) {
                            $data[] = $rowData;
                        }
                    }
                    error_log("Excel parsed: " . count($data) . " data rows");
                } else {
                    // For real Excel files, we need a library like PhpSpreadsheet
                    error_log("Import failed: Real Excel file detected, not supported");
                    echo json_encode([
                        'success' => false,
                        'status' => 'error',
                        'message' => 'File Excel asli (.xlsx) belum didukung. Silakan gunakan file CSV atau export dari sistem ini.'
                    ]);
                    http_response_code(400);
                    exit;
                }
            }

            // Validate headers
            $headerValidation = $this->validateTemplateHeaders($headers);
            if (!$headerValidation['valid']) {
                error_log("Import failed: Invalid headers - " . implode(', ', $headerValidation['errors']));
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Format template tidak sesuai',
                    'validation_errors' => $headerValidation['errors']
                ]);
                http_response_code(400);
                exit;
            }

            if (empty($data)) {
                error_log("Import failed: No data rows found");
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'File kosong atau tidak ada data untuk diimport'
                ]);
                http_response_code(400);
                exit;
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
                error_log("Import failed: Validation errors - " . implode('; ', $validationErrors));
                echo json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Terdapat kesalahan pada data yang diimport',
                    'validation_errors' => $validationErrors
                ]);
                http_response_code(400);
                exit;
            }

            $successCount = 0;
            $soalModel = new SoalExam();

            foreach ($data as $row) {
                // Row structure based on template:
                // 0: Deskripsi, 1: Tipe, 2: A, 3: B, 4: C, 5: D, 6: E, 7: Jawaban

                $deskripsi = trim($row[0]);
                $tipeRaw = strtolower(trim($row[1]));

                // Normalize type
                $tipe = 'essay';
                if (strpos($tipeRaw, 'ganda') !== false || $tipeRaw === 'pg' || strpos($tipeRaw, 'pilihan') !== false) {
                    $tipe = 'pilihan_ganda';
                }

                $pilihan = '';
                if ($tipe === 'pilihan_ganda') {
                    $pilihan = "A. {$row[2]}, B. {$row[3]}, C. {$row[4]}, D. {$row[5]}";
                    if (!empty(trim($row[6]))) {
                        $pilihan .= ", E. {$row[6]}";
                    }
                }

                $jawaban = trim($row[7]);

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

            error_log("Import success: {$successCount} questions imported to bank {$bankId}");
            echo json_encode([
                'success' => true,
                'status' => 'success',
                'message' => "Berhasil mengimport {$successCount} soal",
                'count' => $successCount
            ]);
            http_response_code(200);
            exit;

        } catch (\Exception $e) {
            error_log("Import exception: " . $e->getMessage());
            error_log("Import trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            http_response_code(500);
            exit;
        }
    }

    public function exportSoal() {
        try {
            // Clean output buffer FIRST before any output
            while (ob_get_level()) {
                ob_end_clean();
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                error_log("Export failed: User not authenticated");
                header('HTTP/1.1 403 Forbidden');
                die('User tidak terautentikasi. Silakan login kembali.');
            }

            $bankId = $_GET['bank_id'] ?? null;
            if (!$bankId) {
                error_log("Export failed: No bank_id provided");
                header('HTTP/1.1 400 Bad Request');
                die('Bank Soal ID tidak valid');
            }

            // Get Bank Info
            $bankModel = new BankSoal();
            $bank = $bankModel->getBankById($bankId);
            if (!$bank) {
                error_log("Export failed: Bank not found for ID: " . $bankId);
                header('HTTP/1.1 404 Not Found');
                die('Bank soal tidak ditemukan');
            }

            // Get Soal
            $soalModel = new SoalExam();
            $soalList = $soalModel->getSoalByBankId($bankId);

            if (empty($soalList)) {
                error_log("Export warning: Bank {$bankId} has no questions");
                // Still allow export of empty bank with just headers
            }

            // Use CSV extension
            $filename = "Export_Bank_" . preg_replace('/[^a-zA-Z0-9]/', '_', $bank['nama']) . "_" . date('Ymd') . ".csv";

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

            // Open output stream
            $output = fopen('php://output', 'w');

            if ($output === false) {
                error_log("Export failed: Could not open output stream");
                die('Gagal membuat file export');
            }

            // Add BOM for Excel UTF-8 compatibility
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

                // 1. Deskripsi - strip HTML tags for CSV
                $deskripsi = strip_tags($soal['deskripsi']);

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

                // Write row to CSV
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

                fputcsv($output, $row);
            }

            fclose($output);
            exit();

        } catch (\Exception $e) {
            error_log("Export error: " . $e->getMessage());
            error_log("Export trace: " . $e->getTraceAsString());
            header('HTTP/1.1 500 Internal Server Error');
            die("Error export: " . $e->getMessage());
        }
    }
}
