<?php

namespace App\Controllers;
use App\Core\Controller;
use App\Model\BiodataUser;
use App\Model\UserModel;

class ProfileController extends Controller {

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
    
        if (empty($nama) || empty($jurusan) || empty($kelas) || empty($jenisKelamin) || empty($tempatLahir) || empty($tanggalLahir) || empty($noHp)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Semua field harus diisi.'
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
            // 1. Update Username & Password
            if (!empty($username)) {
                $userModel = new UserModel();
                $userModel->updateUser($userId, $username, !empty($password) ? $password : null);
                $_SESSION['user']['username'] = $username; // Update session
            }

            // 2. Update Biodata
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

            // 3. Update Profile Picture
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['image'];
                $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png'];
                
                if (in_array($ext, $allowed) && $image['size'] <= 2 * 1024 * 1024) {
                    $newName = uniqid() . '.' . $ext;
                    $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/profile/';
                    
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    
                    if (move_uploaded_file($image['tmp_name'], $targetDir . $newName)) {
                        $mahasiswaModel = new \App\Model\User\Mahasiswa();
                        $mahasiswaModel->updateProfilePhoto($userId, $newName);
                        $photoUrl = '/Sistem-Pendaftaran-Calon-Asisten/res/profile/' . $newName;
                    }
                }
            }

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