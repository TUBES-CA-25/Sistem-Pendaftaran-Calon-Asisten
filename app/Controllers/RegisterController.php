<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Model\UserModel;
use App\Model\Mahasiswa;

class RegisterController extends Controller
{
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
    
}
?>