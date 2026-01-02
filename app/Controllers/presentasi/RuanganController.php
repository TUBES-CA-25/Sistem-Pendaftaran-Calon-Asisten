<?php

namespace App\Controllers\presentasi;
use App\Core\Controller;
use App\Model\presentasi\Ruangan;
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
}