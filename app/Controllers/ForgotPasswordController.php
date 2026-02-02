<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Core\Mailer;
use App\Core\Env;
use App\Model\UserModel;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        View::render('forgot_password', 'auth');
    }

    public function sendResetLink()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $email = $_POST['email'] ?? '';
        if (empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Email wajib diisi']);
            return;
        }

        $user = UserModel::findByEmail($email);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan']);
            return;
        }

        // Generate Token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        // Save to DB
        $userModel = new UserModel();
        if ($userModel->updateResetToken($user['id'], $tokenHash)) {
            // Send Email
            $mailer = new Mailer();
            $resetLink = Env::get('APP_URL') . '/reset-password?token=' . $token;
            
            $subject = "Reset Password - IC-ASSIST";
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
                    <div style='background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center;'>
                        <h2 style='color: #0097d9; margin: 0 0 5px 0;'>IC-ASSIST</h2>
                        <p style='color: #6c757d; margin: 0 0 30px 0; font-size: 14px;'>Sistem Pendaftaran Calon Asisten</p>
                        
                        <div style='text-align: left; color: #333333;'>
                            <p>Halo,</p>
                            <p>Kami menerima permintaan untuk mereset password akun Anda. Jika ini benar Anda, silakan klik tombol di bawah ini:</p>
                        </div>

                        <div style='margin: 30px 0;'>
                            <a href='{$resetLink}' style='background-color: #0097d9; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block;'>Reset Password Saya</a>
                        </div>
                        
                        <div style='text-align: left; color: #6c757d; font-size: 13px; border-top: 1px solid #eeeeee; padding-top: 20px;'>
                            <p>Link ini hanya berlaku selama 1 jam.</p>
                            <p style='font-size: 12px; color: #adb5bd;'>Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini. Akun Anda tetap aman.</p>
                        </div>
                        
                        <div style='margin-top: 30px; font-size: 11px; color: #adb5bd;'>
                            &copy; 2026 IC-ASSIST All rights reserved
                        </div>
                    </div>
                </div>
            ";

            $sent = $mailer->send($email, $subject, $body);

            if ($sent === true) {
                echo json_encode(['status' => 'success', 'message' => 'Link reset password telah dikirim ke email Anda. Cek inbox/spam.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim email. Silakan coba lagi. ' . $sent]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memproses permintaan.']);
        }
    }

    public function reset()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            echo "Token invalid.";
            return;
        }
        
        // Verify token existence (optional here, mostly done in update)
        // Check if token valid before showing form?
        $tokenHash = hash('sha256', $token);
        $user = UserModel::findByResetToken($tokenHash);

        if (!$user) {
            echo "Link reset password tidak valid atau sudah kadaluarsa.";
            return;
        }

        View::render('reset_password', 'auth');
    }

    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password) || empty($confirmPassword)) {
            echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi']);
            return;
        }

        if ($password !== $confirmPassword) {
            echo json_encode(['status' => 'error', 'message' => 'Password tidak cocok']);
            return;
        }

        $tokenHash = hash('sha256', $token);
        $user = UserModel::findByResetToken($tokenHash);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'Token tidak valid atau kadaluarsa']);
            return;
        }

        // Update Password
        $userModel = new UserModel();
        // updateUser method hashes password automatically if provided
        if ($userModel->updateUser($user['id'], $user['username'], $password)) {
            // Clear token
            $userModel->updateResetToken($user['id'], null);
            echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah. Silakan login.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah password.']);
        }
    }
}
