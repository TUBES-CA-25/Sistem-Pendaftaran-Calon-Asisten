<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
class LogoutController extends Controller {
    public function logout() {
        $_SESSION = [];
        session_destroy();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Logout berhasil']);
        exit;
    }
}
