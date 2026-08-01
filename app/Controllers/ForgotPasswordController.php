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
            self::jsonError('Method not allowed');
        }

        $email = $_POST['email'] ?? '';
        if (empty($email)) {
            self::jsonError('Email wajib diisi');
        }

        $user = UserModel::findByEmail($email);

        if (!$user) {
            self::jsonError('Email tidak ditemukan');
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
            $body = Mailer::buildHtml(
                'Kami menerima permintaan untuk mereset password akun Anda. Jika ini benar Anda, silakan klik tombol di bawah ini:',
                Mailer::linkButton($resetLink, 'Reset Password Saya'),
                'Link ini hanya berlaku selama 1 jam.',
                'Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini. Akun Anda tetap aman.'
            );

            $sent = $mailer->send($email, $subject, $body);

            if ($sent === true) {
                self::jsonSuccess([], 'Link reset password telah dikirim ke email Anda. Cek inbox/spam.');
            } else {
                self::jsonError('Gagal mengirim email. Silakan coba lagi. ' . $sent);
            }
        } else {
            self::jsonError('Gagal memproses permintaan.');
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
            self::jsonError('Method not allowed');
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password) || empty($confirmPassword)) {
            self::jsonError('Semua kolom wajib diisi');
        }

        if ($password !== $confirmPassword) {
            self::jsonError('Password tidak cocok');
        }

        $tokenHash = hash('sha256', $token);
        $user = UserModel::findByResetToken($tokenHash);

        if (!$user) {
            self::jsonError('Token tidak valid atau kadaluarsa');
        }

        // Update Password
        $userModel = new UserModel();
        // updateUser method hashes password automatically if provided
        if ($userModel->updateUser($user['id'], $user['username'], $password)) {
            // Clear token
            $userModel->updateResetToken($user['id'], null);
            self::jsonSuccess([], 'Password berhasil diubah. Silakan login.');
        } else {
            self::jsonError('Gagal mengubah password.');
        }
    }
}
