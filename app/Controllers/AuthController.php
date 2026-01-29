<?php
namespace App\Controllers;
session_start();
use App\Core\Controller;
use App\Core\View;
use App\Model\UserModel;
use App\Model\Mahasiswa;

class AuthController extends Controller
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

    public function register()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['email'] ?? '';
                $stambuk = $_POST['stambuk'] ?? '';
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['konfirmasiPassword'] ?? '';

                if (empty($name) || empty($stambuk) || empty($password) || empty($confirmPassword)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
                    return;
                }

                if ($password !== $confirmPassword) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
                    return;
                }

                $user = new UserModel();
                if ($user->isStambukExists($stambuk)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => "Gunakan stambuk lain '$stambuk' telah digunakan."]);
                    return;
                }

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $user->__construct2($name, $stambuk, $hashedPassword);

                $userId = $user->save();

                if ($userId) {
                    // Create Mahasiswa Record
                    Mahasiswa::create($userId, $stambuk, $name);

                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'message' => 'Registration successful. Please log in.']);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
                }
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Logout berhasil']);
        exit;
    }
}
?>