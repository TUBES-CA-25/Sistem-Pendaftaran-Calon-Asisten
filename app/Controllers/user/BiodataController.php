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
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
                exit; // <-- Tambahkan exit setelah echo
            }

            if (!isset($_SESSION['user']['id'])) {
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
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
                echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
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

            if ($biodata->save($biodata)) {
                echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
            } 
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Data gagal disimpan: ' . $e->getMessage()]);
            exit; 
        }
    }
}
