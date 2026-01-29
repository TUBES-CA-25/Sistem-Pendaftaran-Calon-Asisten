<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\UserModel;

class ProfilAdminController extends Controller {

    public function updateProfile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id']) || $_SESSION['user']['role'] !== 'Admin') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $username = $_POST['username'] ?? '';

        if (empty($username)) {
            echo json_encode(['status' => 'error', 'message' => 'Username cannot be empty']);
            return;
        }

        // 1. Update Username in DB
        $userModel = new UserModel();
        $updateSuccess = $userModel->updateUser($userId, $username);

        if (!$updateSuccess) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update username']);
            return;
        }

        // Update Session
        $_SESSION['user']['username'] = $username;

        // 2. Handle Photo Upload (If exists)
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            try {
                $this->handlePhotoUpload($userId, $_FILES['photo']);
            } catch (\Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                return;
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    }

    private function handlePhotoUpload($userId, $file) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception('Invalid file type. Only JPG/PNG allowed.');
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            throw new \Exception('File size too large (Max 5MB).');
        }

        // Naming Convention: admin_{id}.jpg (Normalize to .jpg for simplicity or accept orig)
        // Let's use original extension but prefix with admin_{id}
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = "admin_{$userId}.{$ext}";
        $targetPath = $uploadDir . $filename;

        // Remove old files matching pattern admin_{id}.* to avoid duplicates
        $files = glob($uploadDir . "admin_{$userId}.*");
        foreach ($files as $f) {
            if (is_file($f)) unlink($f);
        }

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \Exception('Failed to save photo.');
        }
    }

    public static function getAdminPhoto($userId) {
        $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/';
        $webPath = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/';
        
        $extensions = ['png', 'jpg', 'jpeg'];
        
        clearstatcache();
        
        foreach ($extensions as $ext) {
            $filename = "admin_{$userId}.{$ext}";
            if (file_exists($baseDir . $filename)) {
                return $webPath . $filename . '?v=' . time();
            }
        }
        
        return '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png';
    }
}
