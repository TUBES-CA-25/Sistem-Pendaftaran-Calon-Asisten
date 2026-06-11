<?php

namespace App\Controllers\User;
use App\Core\Controller;
use App\Model\BiodataUser;
use App\Model\UserModel;

class ProfilController extends Controller {

    public static function viewBiodata() : array  {
        $user = new BiodataUser();
        $profile = $user->getBiodata($_SESSION['user']['id']);
        return $profile == null ? [] : $profile;
    }
    public static function viewUser() : array {
        $user = new UserModel();
        $profile = $user->getUser($_SESSION['user']['id']);
        return $profile == null ? [] : $profile;
    }

    public function updateBiodata() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'User tidak terautentikasi'
            ]);
            return;
        }
    
        $nama = $_POST['nama'] ?? '';
        $jurusan = $_POST['jurusan'] ?? '';
        $kelas = $_POST['kelas'] ?? '';
        $alamat = $_POST['alamat'] ?? '';
        $jenisKelamin = $_POST['jenisKelamin'] ?? '';
        $tempatLahir = $_POST['tempatLahir'] ?? '';
        $tanggalLahir = $_POST['tanggalLahir'] ?? '';
        $noHp = $_POST['noHp'] ?? '';
    
        if (empty($nama) || empty($jurusan) || empty($kelas) || empty($jenisKelamin) || empty($tempatLahir) || empty($tanggalLahir) || empty($noHp) || empty($alamat)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Semua field harus diisi.'
            ]);
            return;
        }

        $genderLower = strtolower($jenisKelamin);
        $kelas = strtoupper(trim($kelas));
        
        if ($genderLower === 'wanita' && !preg_match('/^B[0-9]+$/', $kelas)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Kelas untuk wanita harus diawali dengan karakter B lalu diikuti angka.'
            ]);
            return;
        }
        if (($genderLower === 'pria' || $genderLower === 'laki-laki') && !preg_match('/^A[0-9]+$/', $kelas)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Kelas untuk pria harus diawali dengan karakter A lalu diikuti angka.'
            ]);
            return;
        }

        if (!preg_match('/^[A-Za-z\s]+$/', trim($nama))) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Nama Lengkap tidak boleh kosong dan tidak boleh mengandung angka atau karakter spesial.'
            ]);
            return;
        }

        if (!preg_match('/^[A-Za-z\s]+$/', trim($tempatLahir))) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tempat Lahir tidak boleh kosong dan tidak boleh mengandung angka atau karakter spesial.'
            ]);
            return;
        }

        if (!preg_match('/^[A-Za-z0-9\s]+$/', trim($alamat))) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Alamat tidak boleh kosong dan tidak boleh mengandung karakter spesial.'
            ]);
            return;
        }
    
        try {
            $biodata = new BiodataUser(
                idUser: $_SESSION['user']['id'],
                jurusan: $jurusan,
                alamat: $alamat,
                kelas: $kelas,
                namaLengkap: $nama,
                jenisKelamin: $jenisKelamin,
                tempatLahir: $tempatLahir,
                tanggalLahir: $tanggalLahir,
                noHp: $noHp
            );
    
            if($biodata->updateBiodata($biodata)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Data berhasil diperbarui.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui biodata.'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal memperbarui biodata: ' . $e->getMessage()
            ]);
        }
    }

    public function updateProfile() {
        // Prevent HTML error output ruining JSON
        ini_set('display_errors', 0);
        error_reporting(E_ALL);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User tidak terautentikasi']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $nama = $_POST['nama'] ?? '';
        $jurusan = $_POST['jurusan'] ?? '';
        $kelas = $_POST['kelas'] ?? '';
        $alamat = $_POST['alamat'] ?? '';
        $jenisKelamin = $_POST['jenisKelamin'] ?? '';
        $tempatLahir = $_POST['tempatLahir'] ?? '';
        $tanggalLahir = $_POST['tanggalLahir'] ?? '';
        $noHp = $_POST['noHp'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $photoUrl = null;
            header('Content-Type: application/json');

            // 0. Check for Post Size Limit
            if (empty($_FILES) && empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
                 $maxPostSize = ini_get('post_max_size');
                 echo json_encode(['status' => 'error', 'message' => "Ukuran file terlalu besar. Batas upload adalah $maxPostSize."]);
                 return;
            }

            // 1. Update Username & Password
            if (!empty($username)) {
                $userModel = new UserModel();
                $userModel->updateUser($userId, $username, !empty($password) ? $password : null);
                $_SESSION['user']['username'] = $username; // Update session
            }

            // 2. Update Biodata (Only if fields are provided)
            if (!empty($nama)) {
                $genderLower = strtolower($jenisKelamin);
                $kelas = strtoupper(trim($kelas));
                
                if ($genderLower === 'wanita' && !preg_match('/^B[0-9]+$/', $kelas)) {
                    echo json_encode(['status' => 'error', 'message' => 'Kelas untuk wanita harus diawali dengan karakter B lalu diikuti angka.']);
                    return;
                }
                if (($genderLower === 'pria' || $genderLower === 'laki-laki') && !preg_match('/^A[0-9]+$/', $kelas)) {
                    echo json_encode(['status' => 'error', 'message' => 'Kelas untuk pria harus diawali dengan karakter A lalu diikuti angka.']);
                    return;
                }

                if (!preg_match('/^[A-Za-z\s]+$/', trim($nama))) {
                    echo json_encode(['status' => 'error', 'message' => 'Nama Lengkap tidak boleh kosong dan tidak boleh mengandung angka atau karakter spesial.']);
                    return;
                }

                if (!preg_match('/^[A-Za-z\s]+$/', trim($tempatLahir))) {
                    echo json_encode(['status' => 'error', 'message' => 'Tempat Lahir tidak boleh kosong dan tidak boleh mengandung angka atau karakter spesial.']);
                    return;
                }

                if (!preg_match('/^[A-Za-z0-9\s]+$/', trim($alamat))) {
                    echo json_encode(['status' => 'error', 'message' => 'Alamat tidak boleh kosong dan tidak boleh mengandung karakter spesial.']);
                    return;
                }

                $biodata = new BiodataUser(
                    idUser: $userId,
                    jurusan: $jurusan,
                    alamat: $alamat,
                    kelas: $kelas,
                    namaLengkap: $nama,
                    jenisKelamin: $jenisKelamin,
                    tempatLahir: $tempatLahir,
                    tanggalLahir: $tanggalLahir,
                    noHp: $noHp
                );
                $biodata->updateBiodata($biodata);
            }

            // 3. Update Profile Picture
            $debug = [];
            if (isset($_FILES['image'])) {
                 $debug['file_info'] = [
                     'name' => $_FILES['image']['name'],
                     'error' => $_FILES['image']['error'],
                     'size' => $_FILES['image']['size'],
                     'type' => $_FILES['image']['type']
                 ];

                 if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $image = $_FILES['image'];
                    $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png'];
                    
                    if (in_array($ext, $allowed) && $image['size'] <= 5 * 1024 * 1024) { // Increased limit to 5MB
                        $newName = uniqid() . '.' . $ext;
                        $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/profile/';
                        
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0755, true);
                        }
                        
                        if (move_uploaded_file($image['tmp_name'], $targetDir . $newName)) {
                            $mahasiswaModel = new \App\Model\Mahasiswa();
                            
                            // Check existence
                            $mhsData = $mahasiswaModel->getMahasiswaId($userId);
                            if (!$mhsData) {
                                 // Try to recover info (stambuk from session)
                                 $recovStambuk = $_SESSION['user']['stambuk'] ?? null;
                                 $recovNama = $_SESSION['user']['username'] ?? 'User';
                                 
                                 // Use UserID as fallback Stambuk if empty, to ensure uniqueness if needed
                                 if (!$recovStambuk) {
                                     $recovStambuk = 'MHS' . $userId; 
                                 }

                                 try {
                                     \App\Model\Mahasiswa::create($userId, $recovStambuk, $recovNama);
                                     $debug['auto'] = "Created MHS record: $recovStambuk";
                                 } catch (\Exception $e) {
                                     $debug['create_error'] = $e->getMessage();
                                     // If create fails (e.g. duplicate stambuk), we cannot proceed with update
                                     echo json_encode([
                                        'status' => 'error',
                                        'message' => 'Gagal membuat data dasar mahasiswa. Kemungkinan Stambuk sudah terdaftar.',
                                        'debug' => $debug
                                     ]);
                                     return;
                                 }
                            }

                            $rowsAffected = $mahasiswaModel->updateProfilePhoto($userId, $newName);
                            $debug['db_update_rows'] = $rowsAffected;
                            $debug['new_name'] = $newName;

                            if ($rowsAffected > 0) {
                                // Explicit success
                                $photoUrl = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $newName;
                                echo json_encode([
                                    'status' => 'success',
                                    'message' => 'Foto profil berhasil diperbarui.',
                                    'newPhoto' => $photoUrl,
                                    'debug' => $debug
                                ]);
                                return;
                            } else {
                                // DB Update failed even after file move
                                $debug['error'] = 'Rows affected is 0';
                                echo json_encode([
                                    'status' => 'error',
                                    'message' => 'Gagal menyimpan path foto ke database. Silakan coba lagi.',
                                    'debug' => $debug
                                ]);
                                return;
                            }
                        } else {
                             $debug['move_uploaded_file'] = 'Failed to move file to ' . $targetDir . $newName;
                        }
                    } else {
                        $debug['validation'] = 'Invalid extension or size too large. Ext: ' . $ext . ', Size: ' . $image['size'];
                    }
                 } else {
                     $debug['upload_error'] = 'Upload Error Code: ' . $_FILES['image']['error'];
                 }
            } else {
                $debug['no_file'] = '$_FILES["image"] is not set';
            }

            // Removed debug logging to file preventing crashes
            // error_log(print_r($debug, true));

            echo json_encode([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui.',
                'newPhoto' => $photoUrl ?? null
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}