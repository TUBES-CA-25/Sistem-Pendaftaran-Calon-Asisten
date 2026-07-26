<?php

namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Model\Ruangan;
class RuanganController extends Controller {

    public static function viewAllRuangan() {
        $ruangan = new Ruangan();
        $ruangan = $ruangan->getAll();
        return $ruangan == null ? [] : $ruangan;
    }
    public function addRuangan() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
        if(!isset($_POST['namaRuangan'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Nama ruangan harus diisi']);
            return;
        }

        $gambar = 'default_room.png';
        if (isset($_FILES['gambarRuangan']) && $_FILES['gambarRuangan']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['gambarRuangan']['error'] !== UPLOAD_ERR_OK) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah gambar. Ukuran file mungkin terlalu besar (Maks 2MB).']);
                return;
            }
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['gambarRuangan']['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Format file tidak didukung. Harap unggah gambar (JPEG, PNG, GIF, WEBP)']);
                return;
            }

            $uploadDir = __DIR__ . '/../../../res/uploads/ruangan_lab/';
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
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success', 
                'message' => 'Ruangan berhasil ditambahkan',
                'id' => $newId,
                'nama' => htmlspecialchars($_POST['namaRuangan']),
                'gambar' => $gambar
            ]);
        } catch(\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function deleteRuangan() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
        if(!isset($_POST['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'ID ruangan harus diisi']);
            return;
        }
        $ruangan = new Ruangan();
        try {
            $roomData = $ruangan->getById($_POST['id']);
            if ($roomData && !empty($roomData['gambar']) && $roomData['gambar'] !== 'default_room.png') {
                $filePath = __DIR__ . '/../../../res/uploads/ruangan_lab/' . $roomData['gambar'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $ruangan->deleteRuangan($_POST['id']);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Ruangan berhasil dihapus']);
        } catch(\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function updateRuangan() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
        if(!isset($_POST['id']) || !isset($_POST['namaRuangan'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'ID dan nama ruangan harus diisi']);
            return;
        }

        $gambar = null;
        if (isset($_FILES['updateGambarRuangan']) && $_FILES['updateGambarRuangan']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['updateGambarRuangan']['error'] !== UPLOAD_ERR_OK) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah gambar. Ukuran file mungkin terlalu besar (Maks 2MB).']);
                return;
            }
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['updateGambarRuangan']['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Format file tidak didukung. Harap unggah gambar (JPEG, PNG, GIF, WEBP)']);
                return;
            }

            $uploadDir = __DIR__ . '/../../../res/uploads/ruangan_lab/';
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
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Ruangan berhasil diupdate', 'gambar' => $gambar]);
        } catch(\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getRoomParticipants() {
        if(!isset($_POST['id']) || !isset($_POST['type'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid params']);
            return;
        }
        $ruangan = new Ruangan();
        try {
            $assigned = $ruangan->getUsersByRoom($_POST['id'], $_POST['type']);
            $available = $ruangan->getAvailableUsers($_POST['type']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success', 
                'assigned' => $assigned,
                'available' => $available
            ]);
        } catch(\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function assignParticipant() {
        header('Content-Type: application/json');
        if(!isset($_POST['userId']) || !isset($_POST['roomId']) || !isset($_POST['type'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing params']);
            return;
        }
        $ruangan = new Ruangan();
        try {
            $result = $ruangan->assignUserToRoom($_POST['userId'], $_POST['roomId'], $_POST['type']);
            if($result) {
                echo json_encode(['status' => 'success', 'message' => 'Peserta berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan peserta']);
            }
        } catch(\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function removeParticipant() {
        header('Content-Type: application/json');
        if(!isset($_POST['userId']) || !isset($_POST['type'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing params']);
            return;
        }
        $ruangan = new Ruangan();
        try {
            $result = $ruangan->removeUserFromRoom($_POST['userId'], $_POST['type']);
            if($result) {
                echo json_encode(['status' => 'success', 'message' => 'Peserta berhasil dihapus']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus peserta']);
            }
        } catch(\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getRoomOccupants() {
        if(!isset($_POST['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid params']);
            return;
        }
        $ruangan = new Ruangan();
        try {
            $occupants = $ruangan->getAllRoomOccupants($_POST['id']);
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'data' => $occupants]);
        } catch(\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}