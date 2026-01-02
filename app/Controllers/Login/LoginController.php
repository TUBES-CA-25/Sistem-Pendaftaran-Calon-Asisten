<?php
namespace App\Controllers\Login;
session_start();
use App\Core\Controller;
use App\Core\View;
use App\Model\User\UserModel;
class LoginController extends Controller
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        View::render('index', 'login');
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stambuk = $_POST['stambuk'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($stambuk) || empty($password)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Stambuk and password are required.']);
                return;
            }

            $user = UserModel::findByStambuk($stambuk);

            if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Login successful.', 'redirect' => APP_URL . "/", 'role' => $user['role']]);
                return;


            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Stambuk or password is incorrect.']);
                return;
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }
    }
}
?>