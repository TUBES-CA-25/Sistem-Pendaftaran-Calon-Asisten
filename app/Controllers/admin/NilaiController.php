<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\NilaiAkhir;
use App\Model\NotificationUser;
use App\Model\Mahasiswa;

class NilaiController extends Controller
{
    public function saveNilai()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }

            $id_user = $_SESSION['user']['id'];

            $nilaiAkhir = new NilaiAkhir();
            $score = $nilaiAkhir->saveNilai($id_user);
            
            // Send Notification
            $mahasiswaModel = new Mahasiswa();
            $mhsData = $mahasiswaModel->getMahasiswaId($id_user);
            if ($mhsData) {
                $this->sendResultNotification($mhsData['id'], $score);
            }

            error_log("Nilai akhir dihitung: " . $score);
            echo json_encode([
                'status' => 'success',
                'message' => 'Nilai berhasil disimpan',
                'score' => $score
            ]);
            http_response_code(200);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            http_response_code(500);
        }
    }

    public static function getAllNilaiAkhirMahasiswa()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }
            $nilai = new NilaiAkhir();
            return $nilai->getAllNilai();
        } catch (\Exception $e) {
            error_log("Error in getAllNilaiAkhirMahasiswa: " . $e->getMessage());
            return [];
        }
    }

    public function updateTotalNilai()
    {
        try {
            ob_clean();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi'
                ]);
                return;
            }

            $id = $_POST['id'] ?? null;
            $nilai = $_POST['nilai'] ?? null;
            
            // Allow 0 but require ID
            if (!$id || ($nilai === null)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ID mahasiswa dan nilai harus diisi'
                ]);
                return;
            }

            // Convert empty string to NULL for database
            if ($nilai === '') {
                $nilai = null;
            }
            $nilaiAkhir = new NilaiAkhir();
            if ($nilaiAkhir->updateTotalNilai($id, $nilai)) {
                
                // Send Notification
                $this->sendResultNotification($id, $nilai);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Nilai berhasil diupdate'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal mengupdate nilai'
                ]);
            }
        } catch (\Exception $e) {
            error_log("Error in updateTotalNilai: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getSoalAndJawabanMahasiswa()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']['id'])) {
                throw new \Exception('User tidak terautentikasi');
            }

            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID mahasiswa tidak ditemukan');
            }

            error_log("Fetching soal & jawaban for Mahasiswa ID: " . $id);
            $nilai = new NilaiAkhir();
            $result = $nilai->getSoalAndJawaban($id);
            
            error_log("Result info: " . json_encode($result));

            if (empty($result)) {
                echo json_encode([
                    'status' => 'success',
                    'data' => [],
                    'message' => 'Tidak ada data soal dan jawaban untuk mahasiswa ini.'
                ]);
                return;
            }

            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            error_log("Error in getSoalAndJawabanMahasiswa: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function sendResultNotification($mahasiswaId, $score)
    {
        try {
            if ($score === null) return;

            $status = ($score >= 70) ? "LULUS" : "TIDAK LULUS";
            $message = "Nilai Tes Tertulis Anda telah keluar. Skor: {$score}. Status: {$status}.";
            
            if ($score >= 70) {
                $message .= " Selamat! Silahkan pantau jadwal interview selanjutnya.";
            } else {
                $message .= " Jangan berkecil hati, tetap semangat!";
            }

            $notification = new NotificationUser($mahasiswaId, $message);
            $notification->insert($notification);
        } catch (\Exception $e) {
            error_log("Failed to send notification: " . $e->getMessage());
        }
    }

    public function resetUjian()
    {
        header('Content-Type: application/json');
        
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $idMahasiswa = $input['id'] ?? null;
            
            if (!$idMahasiswa) {
                echo json_encode(['status' => 'error', 'message' => 'ID Mahasiswa tidak valid']);
                return;
            }

            // 1. Reset Absensi (Set ke Alpha/Belum Hadir)
            $absensiModel = new \App\Model\Absensi();
            $absenReset = $absensiModel->resetAbsensiTesTertulis($idMahasiswa);
            
            // 2. Hapus Jawaban
            $jawabanModel = new \App\Model\JawabanExam();
            $jawabanReset = $jawabanModel->deleteJawabanByMahasiswa($idMahasiswa); // Ensure this method exists
            
            // 3. Hapus Nilai Akhir
            $nilaiModel = new \App\Model\NilaiAkhir();
            $nilaiReset = $nilaiModel->deleteNilaiByMahasiswa($idMahasiswa); // Ensure this method exists
            
            if ($absenReset || $jawabanReset || $nilaiReset) {
                 echo json_encode([
                    'status' => 'success', 
                    'message' => "Berhasil mereset pengerjaan tes mahasiswa."
                ]);
            } else {
                 echo json_encode(['status' => 'warning', 'message' => 'Tidak ada data pengerjaan yang aktif (Sudah kosong).']);
            }

        } catch (\Throwable $e) {
            error_log("Error reset ujian: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Server Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
            ]);
        }
    }

}