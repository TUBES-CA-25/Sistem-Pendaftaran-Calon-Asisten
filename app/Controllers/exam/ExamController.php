<?php
namespace App\Controllers\exam;

use App\Core\Controller;
use App\Core\View;
use App\Model\exam\SoalExam;
class ExamController extends Controller {
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
            $bankModel = new \App\Model\Exam\BankSoal();
            $activeBank = $bankModel->getActiveBank();
            
            error_log("ExamController: Active Bank result: " . print_r($activeBank, true));

            if (!$activeBank) {
                 throw new \Exception('Belum ada ujian yang aktif saat ini.');
            }
            
            // Check if user has verified token for this session
            // Note: In a real app we might want to tie this to the specific bank ID
            if (!isset($_SESSION['exam_token_verified']) || $_SESSION['exam_token_verified'] !== $activeBank['token']) {
                error_log("ExamController: Token not verified. Session token: " . ($_SESSION['exam_token_verified'] ?? 'None') . ", Bank token: " . $activeBank['token']);
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

            View::render('index', 'exam', ['results' => $soal, 'bank' => $activeBank]);

        } catch (\Throwable $e) {
            View::render('error', 'exam', ['message' => $e->getMessage()]);
        }
    }

    public function verifyToken() {
        header('Content-Type: application/json');
        try {
            $inputToken = $_POST['token'] ?? '';
            
            $bankModel = new \App\Model\Exam\BankSoal();
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
        } catch (\Throwable $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
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
        $bankSoal = new \App\Model\Exam\BankSoal();
        $banks = $bankSoal->getAllBanks();
        return $banks == null ? [] : $banks;
    }

    public static function getActiveBank() {
        $bankSoal = new \App\Model\Exam\BankSoal();
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
}