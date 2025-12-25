<?php

namespace App\Controllers\Profile;
use App\Core\Controller;
use App\Model\User\BiodataUser;
use App\Model\User\UserModel;

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
    
}