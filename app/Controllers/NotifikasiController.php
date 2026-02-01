<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Model\NotificationUser;


class NotifikasiController extends Controller {

    public function sendMessage() {
        header('Content-Type: application/json');
        ob_start(); 
    
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
    
        if (!isset($_SESSION['user']['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }
    
        $idMahasiswa = $_POST['id'] ?? '';
        $message = $_POST['message'] ?? '';
        
        // Debug logging
        error_log("sendMessage called - idMahasiswa: $idMahasiswa, message: $message");
    
        if (!$idMahasiswa || !$message) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
            return;
        }
    
        $notification = new NotificationUser($idMahasiswa, $message);
    
        try {
            $result = $notification->insert($notification);
            if ($result) {
                ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Pesan berhasil dikirim']);
                return;
            } else {
                ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesan ke database']);
                return;
            }
        } catch (\Exception $e) {
            ob_end_clean();
            error_log("sendMessage Exception: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            return;
        }
    }
    public function sendAllMessage() {
        header('Content-Type: application/json');
        ob_start(); 
    
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
    
        if (!isset($_SESSION['user']['id'])) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $idMahasiswaList = $input['mahasiswaIds'] ?? [];
        $message = $input['message'] ?? '';
    
        if (empty($idMahasiswaList) || !$message) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
            return;
        }

        if (!is_array($idMahasiswaList)) {
            $idMahasiswaList = [$idMahasiswaList];
        }

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        foreach($idMahasiswaList as $id) {
            try {
                // NotifikasiUser model expects id_mahasiswa and message in constructor
                // But insert method uses properties
                $notification = new NotificationUser($id, $message);
                
                // We assume insert throws exception on failure or returns true/false
                $result = $notification->insert($notification);
                
                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                    $errors[] = "Gagal kirim ke ID $id: Unknown error";
                }
            } catch (\Exception $e) {
                $failCount++;
                $errors[] = "Gagal kirim ke ID $id: " . $e->getMessage();
            }
        }
        
        ob_end_clean();
        
        if ($successCount > 0 && $failCount === 0) {
            echo json_encode([
                'status' => 'success', 
                'message' => "Berhasil mengirim notifikasi ke $successCount peserta.",
                'sent' => $successCount,
                'failed' => $failCount
            ]);
        } else if ($successCount > 0 && $failCount > 0) {
            echo json_encode([
                'status' => 'partial', 
                'message' => "Terkirim ke $successCount peserta. Gagal ke $failCount peserta.",
                'sent' => $successCount,
                'failed' => $failCount,
                'errors' => $errors
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => "Gagal mengirim ke semua peserta ($failCount gagal).",
                'sent' => $successCount,
                'failed' => $failCount,
                'errors' => $errors
            ]);
        }
    }
    public static function getMessageById() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION['user']['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }
        $id = $_SESSION['user']['id'];
        $notifikasi = new NotificationUser($id,'');
        return $notifikasi->getById($notifikasi);
    }

    public function fetchNotifications() {
        header('Content-Type: application/json');
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        $id = $_SESSION['user']['id'];
        $notifikasiModel = new NotificationUser($id, '');
        $notifications = $notifikasiModel->getById($notifikasiModel);
        $unreadCount = $notifikasiModel->getUnreadCount($notifikasiModel);

        if ($notifications === false) {
             $notifications = [];
        }

        echo json_encode([
            'status' => 'success',
            'data' => $notifications,
            'count' => $unreadCount
        ]);
    }

    public function markRead() {
        header('Content-Type: application/json');
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }

        $id = $_SESSION['user']['id'];
        $notifikasiModel = new NotificationUser($id, '');
        
        try {
            $notifikasiModel->markAllAsRead($notifikasiModel);
            echo json_encode(['status' => 'success']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}