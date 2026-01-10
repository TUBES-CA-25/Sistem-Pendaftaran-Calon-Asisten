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
}