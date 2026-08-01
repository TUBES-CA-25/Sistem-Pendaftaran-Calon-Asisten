<?php
namespace App\Controllers\User;
use App\Core\Controller;
use App\Model\BiodataUser;

class BiodataController extends Controller
{
    public static function isEmpty()
    {
        $biodata = new BiodataUser();
        $isEmpty = $biodata->isEmpty($_SESSION['user']['id']);
        return $isEmpty;
    }
    public function saveBiodata()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            ob_clean();
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                self::jsonError('Invalid request method');
                exit; // <-- Tambahkan exit setelah echo
            }

            if (!isset($_SESSION['user']['id'])) {
                self::jsonError('User not logged in');
                exit;
            }

            $idUser = $_SESSION['user']['id'];
            $jurusan = $_POST['jurusan'] ?? '';
            $kelas = $_POST['kelas'] ?? '';
            $nama = $_POST['nama'] ?? '';
            $stambuk = $_SESSION['user']['stambuk'];
            $gender = $_POST['gender'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $tempatLahir = $_POST['tempatlahir'] ?? '';
            $tanggalLahir = $_POST['tanggallahir'] ?? '';
            $noHp = $_POST['telephone'] ?? '';

            if (empty($jurusan) || empty($kelas) || empty($nama) || empty($gender) || empty($alamat) || empty($tempatLahir) || empty($tanggalLahir) || empty($noHp)) {
                self::jsonError('All fields are required');
                exit;
            }

            $genderLower = strtolower($gender);
            $kelas = strtoupper(trim($kelas));
            
            if ($genderLower === 'wanita' && !preg_match('/^B[0-9]+$/', $kelas)) {
                self::jsonError('Kelas untuk wanita harus diawali dengan karakter B lalu diikuti angka.');
                exit;
            }
            if (($genderLower === 'pria' || $genderLower === 'laki-laki' || $genderLower === 'pria') && !preg_match('/^A[0-9]+$/', $kelas)) {
                self::jsonError('Kelas untuk pria harus diawali dengan karakter A lalu diikuti angka.');
                exit;
            }

            if (!preg_match('/^[A-Za-z\s]+$/', trim($nama))) {
                self::jsonError('Nama Lengkap tidak boleh kosong dan tidak boleh mengandung angka atau karakter spesial.');
                exit;
            }

            if (!preg_match('/^[A-Za-z\s]+$/', trim($tempatLahir))) {
                self::jsonError('Kota Asal/Tempat Lahir tidak boleh kosong dan tidak boleh mengandung angka atau karakter spesial.');
                exit;
            }

            if (!preg_match('/^[A-Za-z0-9\s]+$/', trim($alamat))) {
                self::jsonError('Alamat tidak boleh kosong dan tidak boleh mengandung karakter spesial.');
                exit;
            }

            $biodata = new BiodataUser(
                $idUser,
                $jurusan,
                $stambuk,
                $kelas,
                $nama,
                $alamat,
                $gender,
                $tempatLahir,
                $tanggalLahir,
                $noHp
            );

            // Check if biodata already exists to decide between save (insert) or update
            if (!$biodata->isExist($idUser)) {
                // Insert new data (Create Mahasiswa Record)
                if ($biodata->save($biodata)) {
                    // Logic Tambahan: Create Default Absensi
                    // Ambil ID Mahasiswa yang baru saja dibuat
                    $mahasiswaModel = new \App\Model\Mahasiswa();
                    $mhs = $mahasiswaModel->getMahasiswaId($idUser);
                    
                    if ($mhs) {
                        $absensiModel = new \App\Model\Absensi();
                        $absensiModel->createDefaultAbsensi($mhs['id']);
                    }

                    self::jsonSuccess([], 'Data berhasil disimpan');
                } else {
                    self::jsonError('Gagal menyimpan data baru');
                }
            } else {
                // Update existing data
                if ($biodata->updateBiodata($biodata)) {
                    self::jsonSuccess([], 'Data berhasil diperbarui');
                } else {
                    self::jsonError('Gagal memperbarui data');
                }
            } 
        } catch (\Exception $e) {
            self::jsonError('Data gagal disimpan: ' . $e->getMessage());
            exit; 
        }
    }
}
