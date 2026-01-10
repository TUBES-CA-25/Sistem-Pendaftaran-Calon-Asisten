<?php

namespace App\Controllers\user;

use App\Core\Controller;
use App\Model\User\PengumumanUser;

class PengumumanController extends Controller {

    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // 1. Panggil Model
        $model = new PengumumanUser();
        $dataPengumuman = $model->getAll();

        // 2. Masukkan data ke array $data
        $data['pengumuman'] = $dataPengumuman;
        $data['judul'] = 'Informasi & Pengumuman';

        // 3. Panggil View (Tanpa var_dump/die lagi)
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->view('Templates/pengumuman', $data);
        } else {
            $this->view('Templates/main', $data); 
        }
    }
}