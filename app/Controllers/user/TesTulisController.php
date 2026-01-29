<?php
namespace App\Controllers\User;

use App\Core\Controller;
use App\Core\View;
use App\Model\SoalExam;
use App\Model\JawabanExam;
use App\Model\NilaiAkhir;
class TesTulisController extends Controller {
    public function index() {
        try {
            if (!isset($_GET['nomorMeja'])) {
                throw new \Exception('Nomor meja tidak disediakan');
            }

            $nomorMeja = intval($_GET['nomorMeja']);
            if ($nomorMeja <= 0) {
                throw new \Exception('Nomor meja tidak valid');
            }

            $isGanjil = $nomorMeja % 2 !== 0;

            $soalExam = new SoalExam();
            
            // Get active bank first
            $bankModel = new \App\Model\BankSoal();
            $activeBank = $bankModel->getActiveBank();
            
            if (!$activeBank) {
                 throw new \Exception('Belum ada ujian yang aktif saat ini.');
            }
            
            // Check if user has verified token for this session
            // Note: In a real app we might want to tie this to the specific bank ID
            if (!isset($_SESSION['exam_token_verified']) || $_SESSION['exam_token_verified'] !== $activeBank['token']) {
                // Redirect back to start page if not verified
                header('Location: ' . APP_URL . '/tes-tulis');
                exit;
            }
            
            // Get questions for active bank
            $tesSoal = $soalExam->getSoalByBankId($activeBank['id']);
            
            if (empty($tesSoal)) {
                throw new \Exception('Belum ada soal untuk ujian ini.');
            }

            shuffle($tesSoal);
            $soal = $tesSoal;

            View::render('index', 'ujian', ['results' => $soal, 'bank' => $activeBank]);

        } catch (\Exception $e) {
            View::render('error', 'ujian', ['message' => $e->getMessage()]);
        }
    }

    public function verifyToken() {
        header('Content-Type: application/json');
        try {
            $inputToken = $_POST['token'] ?? '';

            $bankModel = new \App\Model\BankSoal();
            $activeBank = $bankModel->getActiveBank();

            if (!$activeBank) {
                echo json_encode(['status' => 'error', 'message' => 'Tidak ada ujian aktif']);
                return;
            }

            if ($inputToken === $activeBank['token']) {
                // Set session to allow access
                $_SESSION['exam_token_verified'] = $activeBank['token'];

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Token valid',
                    'bank_id' => $activeBank['id']
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Token salah']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function saveAnswer() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Metode tidak diizinkan');
            }

            $data = json_decode(file_get_contents("php://input"), true);
            if (empty($data)) {
                throw new \Exception('Data jawaban kosong');
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }

            $id_user = $_SESSION['user']['id'];
            $jawabanExam = new JawabanExam();
            $errors = [];

            foreach ($data as $answer) {
                if (!isset($answer['id_soal'], $answer['jawaban'])) {
                    $errors[] = 'Data tidak lengkap untuk soal ID: ' . ($answer['id_soal'] ?? 'unknown');
                    continue;
                }

                $id_soal = $answer['id_soal'];
                $jawaban = $answer['jawaban'];

                if (!$jawabanExam->saveJawaban($id_soal, $id_user, $jawaban)) {
                    $errors[] = "Gagal menyimpan jawaban untuk soal ID: $id_soal";
                }
            }

            $nilaiAkhir = new NilaiAkhir();
            $score = $nilaiAkhir->saveNilai($id_user);

            $response = [
                'status' => empty($errors) ? 'success' : 'error',
                'message' => empty($errors) ? 'Semua jawaban berhasil disimpan dan nilai telah dihitung' : 'Gagal menyimpan beberapa jawaban',
                'errors' => $errors,
                'score' => $score,
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
            error_log("Respons backend: " . json_encode($response));
            http_response_code(200);

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
            error_log("Error di backend: " . $e->getMessage());
            http_response_code(500);
        }
    }
    

    public static function viewAllSoal() {
        $soalExam = new SoalExam();
        $soal = $soalExam->getAll();
        return $soal == null ? [] : $soal;
    
    }

    /**
     * Get all bank soal with statistics
     */
    public static function getAllBankSoal() {
        $bankSoal = new \App\Model\BankSoal();
        $banks = $bankSoal->getAllBanks();
        return $banks == null ? [] : $banks;
    }

    public static function getActiveBank() {
        $bankSoal = new \App\Model\BankSoal();
        return $bankSoal->getActiveBank();
    }

    /**
     * Get soal by bank ID
     */
    public static function getSoalByBank($bankId) {
        $soalExam = new SoalExam();
        $soal = $soalExam->getSoalByBankId($bankId);
        return $soal == null ? [] : $soal;
    }

    /**
     * Prepare all data for admin exam management page
     * Controller acts as coordinator between Model and View
     */
    public static function getAdminExamPageData(): array {
        $bankModel = new \App\Model\BankSoal();
        $soalModel = new SoalExam();
        
        // Get data from Models
        $bankSoalList = $bankModel->getAllBanks() ?? [];
        $allSoal = $soalModel->getAll() ?? [];
        $stats = $bankModel->getExamStatistics();
        
        return [
            'bankSoalList' => $bankSoalList,
            'allSoal' => $allSoal,
            'stats' => $stats
        ];
    }
}