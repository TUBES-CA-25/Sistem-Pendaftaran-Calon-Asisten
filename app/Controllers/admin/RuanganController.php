<?php

namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Model\Ruangan;

/**
 * Pengelolaan ruangan (sisi admin).
 *
 * CATATAN KEAMANAN: sebelumnya SELURUH endpoint di controller ini bisa dipanggil
 * tanpa sesi sama sekali — mengirim POST ke /tambahruangan tanpa login sudah
 * cukup untuk membuat ruangan baru. Project ini tidak punya middleware auth
 * global, jadi tiap method publik memanggil requireAuth() sendiri.
 */
class RuanganController extends Controller {

    public static function viewAllRuangan() {
        $ruangan = new Ruangan();
        $ruangan = $ruangan->getAll();
        return $ruangan == null ? [] : $ruangan;
    }
    public function addRuangan() {
        self::requireAuth();

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }
        if(!isset($_POST['namaRuangan'])) {
            self::jsonError('Nama ruangan harus diisi');
        }

        $gambar = 'default_room.png';
        if (isset($_FILES['gambarRuangan']) && $_FILES['gambarRuangan']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['gambarRuangan']['error'] !== UPLOAD_ERR_OK) {
                self::jsonError('Gagal mengunggah gambar. Ukuran file mungkin terlalu besar (Maks 1MB).');
            }
            if ($_FILES['gambarRuangan']['size'] > 1024 * 1024) {
                self::jsonError('Ukuran file gambar terlalu besar. Maksimal 1MB.');
            }
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['gambarRuangan']['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                self::jsonError('Format file tidak didukung. Harap unggah gambar (JPEG, PNG, GIF, WEBP)');
            }

            $uploadDir = __DIR__ . '/../../../res/ruangan_lab/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['gambarRuangan']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['gambarRuangan']['tmp_name'], $targetPath)) {
                $gambar = $fileName;
            }
        }

        $ruangan = new Ruangan();
        try {
            $newId = $ruangan->insertRuangan($_POST['namaRuangan'], $gambar);
            self::jsonSuccess(['id' => $newId, 'nama' => htmlspecialchars($_POST['namaRuangan']), 'gambar' => $gambar], 'Ruangan berhasil ditambahkan');
        } catch(\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
    public function deleteRuangan() {
        self::requireAuth();

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }
        if(!isset($_POST['id'])) {
            self::jsonError('ID ruangan harus diisi');
        }
        $ruangan = new Ruangan();
        try {
            $roomData = $ruangan->getById($_POST['id']);
            if ($roomData && !empty($roomData['gambar']) && $roomData['gambar'] !== 'default_room.png') {
                $filePath = __DIR__ . '/../../../res/ruangan_lab/' . $roomData['gambar'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $ruangan->deleteRuangan($_POST['id']);
            self::jsonSuccess([], 'Ruangan berhasil dihapus');
        } catch(\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
    public function updateRuangan() {
        self::requireAuth();

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }
        if(!isset($_POST['id']) || !isset($_POST['namaRuangan'])) {
            self::jsonError('ID dan nama ruangan harus diisi');
        }

        $gambar = null;
        if (isset($_FILES['updateGambarRuangan']) && $_FILES['updateGambarRuangan']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['updateGambarRuangan']['error'] !== UPLOAD_ERR_OK) {
                self::jsonError('Gagal mengunggah gambar. Ukuran file mungkin terlalu besar (Maks 1MB).');
            }
            if ($_FILES['updateGambarRuangan']['size'] > 1024 * 1024) {
                self::jsonError('Ukuran file gambar terlalu besar. Maksimal 1MB.');
            }
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['updateGambarRuangan']['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                self::jsonError('Format file tidak didukung. Harap unggah gambar (JPEG, PNG, GIF, WEBP)');
            }

            $uploadDir = __DIR__ . '/../../../res/ruangan_lab/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['updateGambarRuangan']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['updateGambarRuangan']['tmp_name'], $targetPath)) {
                $gambar = $fileName;
                
                $ruanganModel = new Ruangan();
                $oldData = $ruanganModel->getById($_POST['id']);
                if ($oldData && !empty($oldData['gambar']) && $oldData['gambar'] !== 'default_room.png') {
                    $oldFile = $uploadDir . $oldData['gambar'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            }
        }

        $ruangan = new Ruangan();
        try {
            $ruangan->updateRuangan($_POST['id'], $_POST['namaRuangan'], $gambar);
            self::jsonSuccess(['gambar' => $gambar], 'Ruangan berhasil diupdate');
        } catch(\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function getRoomParticipants() {
        self::requireAuth();

        if(!isset($_POST['id']) || !isset($_POST['type'])) {
            self::jsonError('Invalid params');
        }
        $ruangan = new Ruangan();
        try {
            $assigned = $ruangan->getUsersByRoom($_POST['id'], $_POST['type']);
            $available = $ruangan->getAvailableUsers($_POST['type']);
            
            self::jsonSuccess(['assigned' => $assigned, 'available' => $available]);
        } catch(\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function assignParticipant() {
        self::requireAuth();

        header('Content-Type: application/json');
        if(!isset($_POST['userId']) || !isset($_POST['roomId']) || !isset($_POST['type'])) {
            self::jsonError('Missing params');
        }
        $ruangan = new Ruangan();
        try {
            $result = $ruangan->assignUserToRoom($_POST['userId'], $_POST['roomId'], $_POST['type']);
            if($result) {
                self::jsonSuccess([], 'Peserta berhasil ditambahkan');
            } else {
                self::jsonError('Gagal menambahkan peserta');
            }
        } catch(\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function removeParticipant() {
        self::requireAuth();

        header('Content-Type: application/json');
        if(!isset($_POST['userId']) || !isset($_POST['type'])) {
            self::jsonError('Missing params');
        }
        $ruangan = new Ruangan();
        try {
            $result = $ruangan->removeUserFromRoom($_POST['userId'], $_POST['type']);
            if($result) {
                self::jsonSuccess([], 'Peserta berhasil dihapus');
            } else {
                self::jsonError('Gagal menghapus peserta');
            }
        } catch(\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function getRoomOccupants() {
        self::requireAuth();

        if(!isset($_POST['id'])) {
            self::jsonError('Invalid params');
        }
        $ruangan = new Ruangan();
        try {
            $occupants = $ruangan->getAllRoomOccupants($_POST['id']);
            
            self::jsonSuccess(['data' => $occupants]);
        } catch(\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
}