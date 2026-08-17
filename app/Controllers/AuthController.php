<?php
namespace App\Controllers;
session_start();
use App\Core\Controller;
use App\Core\View;
use App\Model\UserModel;
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

                // Remember Me logic
                $remember = isset($_POST['check']) && $_POST['check'] === 'on';
                if ($remember) {
                    setcookie('remember_stambuk', $stambuk, time() + (86400 * 30), "/");
                    setcookie('remember_password', $password, time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_stambuk', '', time() - 3600, "/");
                    setcookie('remember_password', '', time() - 3600, "/");
                }

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

                // Email kampus WAJIB memakai stambuk sendiri sebagai bagian
                // sebelum '@'. Ini mencegah satu orang mendaftarkan stambuk
                // milik orang lain memakai emailnya sendiri.
                // Dicek setelah format email & stambuk valid agar pesan errornya
                // berurutan dari yang paling mendasar.
                $lokalEmail = substr($name, 0, strrpos($name, '@'));
                if (strcasecmp($lokalEmail, $stambuk) !== 0) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'Email harus memakai stambuk Anda sendiri, yaitu ' . $stambuk . '@student.umi.ac.id.'
                    ]);
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
                $body = Mailer::buildHtml(
                    'Terima kasih telah mendaftar di IC-ASSIST. Gunakan kode OTP di bawah ini untuk memverifikasi pendaftaran akun baru Anda:',
                    Mailer::otpBox($otp),
                    'Kode OTP ini hanya berlaku selama 5 menit.',
                    'Jika Anda tidak merasa melakukan pendaftaran ini, abaikan saja email ini.'
                );

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
            $body = Mailer::buildHtml(
                'Berikut adalah kode OTP registrasi Anda yang baru untuk memverifikasi pembuatan akun:',
                Mailer::otpBox($otp),
                'Kode OTP ini berlaku selama 5 menit.'
            );

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