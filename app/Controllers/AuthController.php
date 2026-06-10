<?php
namespace App\Controllers;
session_start();
use App\Core\Controller;
use App\Core\View;
use App\Model\UserModel;
use App\Model\Mahasiswa;
use App\Model\Absensi;
use App\Core\Mailer;

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
        View::render('index', 'auth');
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

                if (!filter_var($name, FILTER_VALIDATE_EMAIL)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
                    return;
                }

                $parts = explode('@', $name);
                $domain = end($parts);
                if ($domain !== 'student.umi.ac.id') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Email harus menggunakan domain student.umi.ac.id.']);
                    return;
                }

                if (strlen($stambuk) !== 11) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Stambuk/NIM harus memiliki panjang 11 karakter.']);
                    return;
                }

                $prefix = substr($stambuk, 0, 3);
                if ($prefix !== '130' && $prefix !== '131') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Stambuk harus diawali dengan 130 atau 131.']);
                    return;
                }

                $yearStr = substr($stambuk, 3, 4);
                if (!is_numeric($yearStr)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Format Stambuk/NIM tidak valid.']);
                    return;
                }

                $yearOfNim = (int) $yearStr;
                $currentYear = (int) date('Y');
                $minYear = $currentYear - 3;
                $maxYear = $currentYear;

                if ($yearOfNim < $minYear || $yearOfNim > $maxYear) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Pendaftaran hanya terbuka untuk mahasiswa angkatan ' . $minYear . ' sampai ' . $maxYear . '.'
                    ]);
                    return;
                }

                $suffix = substr($stambuk, 7);
                if (!ctype_digit($suffix) || strlen($suffix) !== 4) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Format Stambuk/NIM tidak valid pada 4 digit terakhir.']);
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

                // Generate secure 6-digit OTP
                $otp = sprintf("%06d", mt_rand(100000, 999999));
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $_SESSION['pending_register'] = [
                    'email' => $name,
                    'stambuk' => $stambuk,
                    'password' => $hashedPassword,
                    'otp' => $otp,
                    'expires' => time() + 300 // 5 minutes expiration
                ];

                // Send email
                $mailer = new Mailer();
                $subject = "Kode OTP Registrasi - IC-ASSIST";
                $body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
                        <div style='background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center;'>
                            <h2 style='color: #0097d9; margin: 0 0 5px 0;'>IC-ASSIST</h2>
                            <p style='color: #6c757d; margin: 0 0 30px 0; font-size: 14px;'>Sistem Pendaftaran Calon Asisten</p>
                            
                            <div style='text-align: left; color: #333333;'>
                                <p>Halo,</p>
                                <p>Terima kasih telah mendaftar di IC-ASSIST. Gunakan kode OTP di bawah ini untuk memverifikasi pendaftaran akun baru Anda:</p>
                            </div>

                            <div style='margin: 30px 0;'>
                                <div style='background-color: #f1f3f5; color: #0f172a; padding: 15px 30px; border-radius: 10px; font-weight: bold; font-size: 32px; display: inline-block; letter-spacing: 5px;'>{$otp}</div>
                            </div>
                            
                            <div style='text-align: left; color: #6c757d; font-size: 13px; border-top: 1px solid #eeeeee; padding-top: 20px;'>
                                <p>Kode OTP ini hanya berlaku selama 5 menit.</p>
                                <p style='font-size: 12px; color: #adb5bd;'>Jika Anda tidak merasa melakukan pendaftaran ini, abaikan saja email ini.</p>
                            </div>
                            
                            <div style='margin-top: 30px; font-size: 11px; color: #adb5bd;'>
                                &copy; 2026 IC-ASSIST All rights reserved
                            </div>
                        </div>
                    </div>
                ";

                $sent = $mailer->send($name, $subject, $body);

                if ($sent === true) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'otp_required',
                        'email' => htmlspecialchars($name),
                        'message' => 'Kode OTP verifikasi telah dikirim ke email Anda.'
                    ]);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim email OTP: ' . $sent]);
                }
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function verifyOTP()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                return;
            }

            $otpInput = $_POST['otp'] ?? '';

            if (empty($otpInput)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Kode OTP wajib diisi.']);
                return;
            }

            if (!isset($_SESSION['pending_register'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Data registrasi tidak ditemukan. Silakan isi form daftar kembali.']);
                return;
            }

            $pending = $_SESSION['pending_register'];

            // Check expiration
            if (time() > $pending['expires']) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Kode OTP sudah kadaluarsa (lebih dari 5 menit). Silakan kirim ulang OTP.']);
                return;
            }

            // Verify code
            if ($pending['otp'] !== $otpInput) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Kode OTP yang dimasukkan salah.']);
                return;
            }

            // Save user to DB
            $user = new UserModel();
            if ($user->isStambukExists($pending['stambuk'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Stambuk/NIM sudah terdaftar.']);
                unset($_SESSION['pending_register']);
                return;
            }

            $user->__construct2($pending['email'], $pending['stambuk'], $pending['password']);
            $userId = $user->save();

            if ($userId) {
                unset($_SESSION['pending_register']);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil! Silakan masuk dengan akun Anda.']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Gagal membuat akun baru.']);
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function resendOTP()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                return;
            }

            if (!isset($_SESSION['pending_register'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Sesi registrasi tidak ditemukan. Silakan isi form daftar kembali.']);
                return;
            }

            $otp = sprintf("%06d", mt_rand(100000, 999999));
            $_SESSION['pending_register']['otp'] = $otp;
            $_SESSION['pending_register']['expires'] = time() + 300; // Reset expiry (5 mins)

            $email = $_SESSION['pending_register']['email'];

            // Send email
            $mailer = new Mailer();
            $subject = "Kode OTP Registrasi Baru - IC-ASSIST";
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
                    <div style='background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center;'>
                        <h2 style='color: #0097d9; margin: 0 0 5px 0;'>IC-ASSIST</h2>
                        <p style='color: #6c757d; margin: 0 0 30px 0; font-size: 14px;'>Sistem Pendaftaran Calon Asisten</p>
                        
                        <div style='text-align: left; color: #333333;'>
                            <p>Halo,</p>
                            <p>Berikut adalah kode OTP registrasi Anda yang baru untuk memverifikasi pembuatan akun:</p>
                        </div>

                        <div style='margin: 30px 0;'>
                            <div style='background-color: #f1f3f5; color: #0f172a; padding: 15px 30px; border-radius: 10px; font-weight: bold; font-size: 32px; display: inline-block; letter-spacing: 5px;'>{$otp}</div>
                        </div>
                        
                        <div style='text-align: left; color: #6c757d; font-size: 13px; border-top: 1px solid #eeeeee; padding-top: 20px;'>
                            <p>Kode OTP ini berlaku selama 5 menit.</p>
                        </div>
                        
                        <div style='margin-top: 30px; font-size: 11px; color: #adb5bd;'>
                            &copy; 2026 IC-ASSIST All rights reserved
                        </div>
                    </div>
                </div>
            ";

            $sent = $mailer->send($email, $subject, $body);

            if ($sent === true) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Kode OTP verifikasi baru telah dikirim ke email Anda.']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim email OTP baru: ' . $sent]);
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
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