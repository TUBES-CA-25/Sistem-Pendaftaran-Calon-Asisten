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
    
        if (!$idMahasiswa || !$message) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
            return;
        }
    
        $notification = new NotificationUser($idMahasiswa, $message);
    
        try {
            if ($notification->insert($notification)) {
                header('Content-Type: application/json');
                ob_clean(); 
                echo json_encode(['status' => 'success', 'message' => 'Pesan berhasil dikirim']);
                return;
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            ob_clean(); 
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            return;
        }
    
        ob_end_clean();
    }
    public function sendAllMessage() {
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
        $input = json_decode(file_get_contents('php://input'), true);
        $idMahasiswa = $input['mahasiswaIds'] ?? '';
        $message = $input['message'] ?? '';
       
    
        if (!$idMahasiswa || !$message) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
            return;
        }
        foreach($idMahasiswa as $id) {
            $notification = new NotificationUser($id, $message);
            try {
                if ($notification->insert($notification)) {
                    continue;
                }
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                ob_clean(); 
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                return;
            }
        }
        
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Pesan berhasil dikirim']);
        return;
    
        ob_end_clean();
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