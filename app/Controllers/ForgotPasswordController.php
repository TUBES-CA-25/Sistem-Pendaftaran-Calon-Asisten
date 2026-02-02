<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
<<<<<<< HEAD
use App\Core\Database;
use App\Model\PasswordReset;
=======
use App\Core\Mailer;
use App\Core\Env;
>>>>>>> 30acf3bbc860d283f5ee93b12c43dfdaf24b6057
use App\Model\UserModel;

class ForgotPasswordController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
        // Render the forgot password view
        require_once __DIR__ . '/../View/auth/forgotPassword.php';
    }

    public function process()
    {
        // Start output buffering to capture any unwanted output/warnings
        ob_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
=======
        View::render('forgot_password', 'auth');
    }

    public function sendResetLink()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
>>>>>>> 30acf3bbc860d283f5ee93b12c43dfdaf24b6057
            return;
        }

        $email = $_POST['email'] ?? '';
<<<<<<< HEAD

        if (empty($email)) {
            ob_end_clean();
            header('Content-Type: application/json');
=======
        if (empty($email)) {
>>>>>>> 30acf3bbc860d283f5ee93b12c43dfdaf24b6057
            echo json_encode(['status' => 'error', 'message' => 'Email wajib diisi']);
            return;
        }

<<<<<<< HEAD
        try {
            // Verify if user exists
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM user WHERE username = :email");
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch();

            if (!$user) {
                ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan']);
                return;
            }

            // Generate Token
            $token = bin2hex(random_bytes(32));
            
            $passwordReset = new PasswordReset();
            if (!$passwordReset->createToken($email, $token)) {
                throw new \Exception("Gagal menyimpan token ke database");
            }

            $resetLink = APP_URL . "/resetPassword?token=" . $token . "&email=" . urlencode($email);
            
            // Check files exist before requiring
            $phpMailerPath = __DIR__ . '/../Core/PHPMailer/src/PHPMailer.php';
            if (!file_exists($phpMailerPath)) {
                throw new \Exception("File PHPMailer tidak ditemukan di: $phpMailerPath");
            }

            // Send email using PHPMailer
            require_once __DIR__ . '/../Core/PHPMailer/src/Exception.php';
            require_once __DIR__ . '/../Core/PHPMailer/src/PHPMailer.php';
            require_once __DIR__ . '/../Core/PHPMailer/src/SMTP.php';

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            // Server settings
            $mail->SMTPDebug = 2; 
            $mail->Debugoutput = function($str, $level) use (&$smtp_debug_log) {
                $smtp_debug_log .= "$level: $str\n";
            };
            $smtp_debug_log = ""; 

            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USERNAME');
            $mail->Password   = getenv('MAIL_PASSWORD');
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION') ?: \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = getenv('MAIL_PORT') ?: 465;
            
            // Bypass SSL verification for XAMPP/Localhost
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom(getenv('MAIL_FROM') ?: 'noreply@iclabs.ac.id', getenv('APP_NAME') ?: 'IC-ASSIST');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password - Sistem Pendaftaran';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f6f9;'>
                    <div style='background-color: white; border-radius: 10px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                        <div style='text-align: center; margin-bottom: 30px;'>
                            <h2 style='color: #0097d9; margin: 0; font-size: 24px;'>" . (getenv('APP_NAME') ?: 'IC-ASSIST') . "</h2>
                            <p style='color: #666; font-size: 14px; margin-top: 5px;'>Sistem Pendaftaran Calon Asisten</p>
                        </div>
                        
                        <p style='color: #333; font-size: 16px; line-height: 1.6;'>Halo,</p>
                        
                        <p style='color: #555; font-size: 16px; line-height: 1.6;'>
                            Kami menerima permintaan untuk mereset password akun Anda. 
                            Jika ini benar Anda, silakan klik tombol di bawah ini:
                        </p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$resetLink}' style='background-color: #0097d9; color: white; padding: 12px 25px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block; box-shadow: 0 4px 15px rgba(0,151,217,0.3);'>Reset Password Saya</a>
                        </div>
                        
                        <p style='color: #999; font-size: 14px; line-height: 1.6; margin-bottom: 30px;'>
                            Link ini hanya berlaku selama 1 jam.
                        </p>
                        
                        <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                        
                        <p style='color: #aaa; font-size: 12px; text-align: center;'>
                            Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini. Akun Anda tetap aman.<br>
                            <br>
                            &copy; " . date('Y') . " " . (getenv('APP_NAME') ?: 'IC-ASSIST') . ". All rights reserved.
                        </p>
                    </div>
                </div>
            ";
            $mail->AltBody = "Klik link berikut untuk reset password: {$resetLink}";

            $mail->send();
            
            // Clear buffer before sending JSON
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success', 
                'message' => 'Link reset password telah dikirim ke email Anda.'
            ]);

        } catch (\Throwable $e) {
            // Log detailed error
            $errorMessage = $e->getMessage();
            $smtpLog = isset($smtp_debug_log) ? $smtp_debug_log : '';
            
            // Verify if error comes from Mailer
            if (isset($mail) && $mail instanceof \PHPMailer\PHPMailer\PHPMailer) {
                $errorMessage .= " " . $mail->ErrorInfo;
            }

            ob_end_clean(); // Clean any garbage output
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error', 
                'message' => 'Gagal memproses permintaan.',
                'debug_error' => $errorMessage, 
                'smtp_log' => $smtpLog,
                'debug_link' => isset($resetLink) ? $resetLink : null
            ]);
        }
    }

    public function resetIndex()
    {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';
        
        // Pass variables to view
        // In native PHP view, we can just use $token and $email directly from $_GET or define them here
        require_once __DIR__ . '/../View/auth/resetPassword.php';
    }

    public function resetProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
=======
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
>>>>>>> 30acf3bbc860d283f5ee93b12c43dfdaf24b6057
            return;
        }

        $token = $_POST['token'] ?? '';
<<<<<<< HEAD
        $email = $_POST['email'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        if (empty($token) || empty($email) || empty($newPassword)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Konfirmasi password tidak cocok']);
            return;
        }

        // Validate Token
        $passwordReset = new PasswordReset();
        $record = $passwordReset->findByEmailAndToken($email, $token);

        if (!$record) {
            header('Content-Type: application/json');
=======
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
>>>>>>> 30acf3bbc860d283f5ee93b12c43dfdaf24b6057
            echo json_encode(['status' => 'error', 'message' => 'Token tidak valid atau kadaluarsa']);
            return;
        }

        // Update Password
<<<<<<< HEAD
        // Use UserModel to update
        $userModel = new UserModel();
        // First get user ID
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM user WHERE username = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            // Updating password directly via query to avoid complexity of UserModel methods if they are strict
            $updateStmt = $db->prepare("UPDATE user SET password = :password WHERE id = :id");
            $updateStmt->bindValue(':password', $hashedPassword);
            $updateStmt->bindValue(':id', $user['id']);
            
            if ($updateStmt->execute()) {
                // Delete Token
                $passwordReset->deleteByEmail($email);
                
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah', 'redirect' => APP_URL]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate password']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'User tidak ditemukan']);
=======
        $userModel = new UserModel();
        // updateUser method hashes password automatically if provided
        if ($userModel->updateUser($user['id'], $user['username'], $password)) {
            // Clear token
            $userModel->updateResetToken($user['id'], null);
            echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah. Silakan login.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah password.']);
>>>>>>> 30acf3bbc860d283f5ee93b12c43dfdaf24b6057
        }
    }
}
