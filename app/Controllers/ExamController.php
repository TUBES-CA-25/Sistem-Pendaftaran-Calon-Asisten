<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Model\SoalExam;
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

            View::render('index', 'exam', ['results' => $soal, 'bank' => $activeBank]);

        } catch (\Exception $e) {
            View::render('error', 'exam', ['message' => $e->getMessage()]);
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