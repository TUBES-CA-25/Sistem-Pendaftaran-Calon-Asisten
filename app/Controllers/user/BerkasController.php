<?php
namespace App\Controllers\User;

use App\Core\Controller;
use App\Model\BerkasUser;
use App\Model\Mahasiswa;

class BerkasController extends Controller
{
    public static function isEmptyBerkas()
    {
        $berkas = new BerkasUser(0, 0, 0, 0, 0, 0, 0, 0, 0);
        $isEmpty = $berkas->isEmpty($_SESSION['user']['id']);
        if (!$isEmpty) {
            return false;
        }
        return true;
    }
    public function updateAcceptedStatus()
    {
        try {
            // Ensure no previous output contaminates JSON
            if (ob_get_length()) ob_clean();
            
            header('Content-Type: application/json');
            $id = $_POST['id'] ?? null;
            if (!$id) {
                http_response_code(400);
                self::jsonError('ID tidak diberikan');
            }

            $status = $_POST['status'] ?? 1;
            
            $berkas = new BerkasUser();
            $isAccepted = $berkas->updateAccepted($id, $status);

            if ($isAccepted) {
                self::jsonSuccess([], 'Status berhasil diperbarui');
            } else {
                http_response_code(500);
                self::jsonError('Gagal memperbarui status');
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            self::jsonError($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    public function saveBerkas()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }

        $idUser = $_SESSION['user']['id'];
        $foto = $_FILES['foto']['tmp_name'] ?? '';
        $cv = $_FILES['cv']['tmp_name'] ?? '';
        $transkrip = $_FILES['transkrip']['tmp_name'] ?? '';
        $suratPernyataan = $_FILES['suratpernyataan']['tmp_name'] ?? '';

        try {
             // Clean output buffer to remove any warning/notices
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json');

            if (!$foto || !$cv || !$transkrip || !$suratPernyataan) {
                self::jsonError('Semua file wajib diupload');
            }

            $imgSize = $_FILES['foto']['size'] ?? 0;
            $cvSize = $_FILES['cv']['size'] ?? 0;
            $transkripSize = $_FILES['transkrip']['size'] ?? 0;
            $suratPernyataanSize = $_FILES['suratpernyataan']['size'] ?? 0;

            $berkasUser = new BerkasUser(
                $idUser,
                $foto,
                $cv,
                $transkrip,
                $suratPernyataan,
                $imgSize,
                $cvSize,
                $transkripSize,
                $suratPernyataanSize
            );
            
            // error_log('Session ID User: ' . ($_SESSION['user']['id'] ?? 'Tidak ada'));
            // error_log('File Foto: ' . print_r($_FILES['foto'], true));
            // error_log('File CV: ' . print_r($_FILES['cv'], true));

            if ($berkasUser->save($berkasUser)) {
                self::jsonSuccess([], 'Berkas berhasil diupload');
            } else {
                self::jsonError('Berkas gagal diupload');
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            self::jsonError($e->getMessage());
        }
    }
    public static function viewBerkas()
    {
        $user = new BerkasUser();
        $berkas = $user->getBerkas($_SESSION['user']['id']);
        if (!$berkas) {
            return null;
        }
        return $berkas;
    }


    public static function getBerkasAdmin()
    {
        $user = new BerkasUser();
        $berkas = $user->getBerkasAdmin();
        if (!$berkas) {
            return null;
        }
        return $berkas;
    }
    public function downloadBerkas()
    {
        try {
            if (!isset($_GET['type'])) {
                throw new \Exception('Jenis berkas tidak disediakan');
            }

            $type = $_GET['type'];
            $allowedTypes = ['foto', 'cv', 'transkrip_nilai', 'surat_pernyataan'];
            if (!in_array($type, $allowedTypes)) {
                throw new \Exception('Jenis berkas tidak valid');
            }

            $user = new Mahasiswa();
            $mahasiswaId = $user->getMahasiswaId(['id_user' => $_SESSION['user']['id']])['id'];

            if (!$mahasiswaId) {
                throw new \Exception('Mahasiswa tidak ditemukan');
            }

            $berkas = $user->getBerkasMahasiswa($mahasiswaId);

            if (!$berkas || !$berkas[$type]) {
                throw new \Exception('Berkas tidak tersedia');
            }

            $basePath = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/';
            $filePath = ($type === 'foto')
                ? $basePath . 'imageUser/' . $berkas[$type]
                : $basePath . 'berkasUser/' . $berkas[$type];

            if (!file_exists($filePath)) {
                throw new \Exception('File tidak ditemukan');
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
