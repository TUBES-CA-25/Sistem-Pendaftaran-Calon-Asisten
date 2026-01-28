<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Model\JawabanExam;
use App\Model\NilaiAkhir;

class AnswersController extends Controller {
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
}
