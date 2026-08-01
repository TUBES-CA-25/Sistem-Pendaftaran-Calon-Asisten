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
            self::jsonError('Invalid request method');
        }
    
        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }
    
        $idMahasiswa = $_POST['id'] ?? '';
        $message = $_POST['message'] ?? '';
    
        if (!$idMahasiswa || !$message) {
            self::jsonError('Semua field wajib diisi');
        }
    
        $notification = new NotificationUser($idMahasiswa, $message);
    
        try {
            if ($notification->insert($notification)) {
                header('Content-Type: application/json');
                ob_clean(); 
                self::jsonSuccess([], 'Pesan berhasil dikirim');
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            ob_clean(); 
            self::jsonError($e->getMessage());
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
            self::jsonError('Invalid request method');
        }
    
        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $idMahasiswa = $input['mahasiswaIds'] ?? '';
        $message = $input['message'] ?? '';
       
    
        if (!$idMahasiswa || !$message) {
            self::jsonError('Semua field wajib diisi');
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
                self::jsonError($e->getMessage());
            }
        }
        
        ob_end_clean();
        self::jsonSuccess([], 'Pesan berhasil dikirim');
    
        ob_end_clean();
    }
    /**
     * Ambil notifikasi milik user yang sedang login.
     *
     * Method ini static dan dipakai sebagai pengambil data biasa oleh
     * ProvidesUserData, HomeController, dan view user/notifikasi — ketiganya
     * memakai pola `?? []` dan mengharapkan nilai kembali, bukan respons HTTP.
     * Karena itu saat sesi tidak ada kita cukup mengembalikan array kosong.
     *
     * (Sebelumnya di sini memanggil $this->jsonError() padahal konteksnya
     * static, sehingga melempar Error: "Using $this when not in object
     * context" — fatal, bukan sekadar pesan gagal.)
     */
    public static function getMessageById() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION['user']['id'])) {
            return [];
        }
        $id = $_SESSION['user']['id'];
        $notifikasi = new NotificationUser($id,'');
        return $notifikasi->getById($notifikasi) ?: [];
    }

    public function fetchNotifications() {
        header('Content-Type: application/json');
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }

        $id = $_SESSION['user']['id'];
        $notifikasiModel = new NotificationUser($id, '');
        $notifications = $notifikasiModel->getById($notifikasiModel);
        $unreadCount = $notifikasiModel->getUnreadCount($notifikasiModel);

        if ($notifications === false) {
             $notifications = [];
        }

        ob_start();
        include __DIR__ . '/../View/partials/notification_dropdown.php';
        $html = ob_get_clean();

        self::jsonSuccess(['data' => $notifications, 'count' => $unreadCount, 'html' => $html]);
    }

    public function markRead() {
        header('Content-Type: application/json');
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }

        $id = $_SESSION['user']['id'];
        $notifikasiModel = new NotificationUser($id, '');
        
        try {
            $notifikasiModel->markAllAsRead($notifikasiModel);
            echo json_encode(['status' => 'success']);
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
}