<?php

namespace App\Controllers;
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
        $ruangan = new Ruangan();
        try {
            $ruangan->insertRuangan($_POST['namaRuangan']);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Ruangan berhasil ditambahkan']);
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
        $ruangan = new Ruangan();
        try {
            $ruangan->updateRuangan($_POST['id'], $_POST['namaRuangan']);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Ruangan berhasil diupdate']);
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