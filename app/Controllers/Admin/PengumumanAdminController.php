<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
// Import Model yang baru kita buat
use App\Model\Pengumuman\PengumumanAdmin;

class PengumumanAdminController extends Controller {
    
    public function index() {
        // Instansiasi Model langsung (Gaya Anda)
        // Kita isi parameter null karena cuma mau panggil getAll()
        $model = new PengumumanAdmin();
        $dataPengumuman = $model->getAll();

        $data['judul'] = 'Kelola Pengumuman';
        $data['pengumuman'] = $dataPengumuman;
        
        // Logika AJAX vs Full Page (Tetap kita pertahankan agar Sidebar jalan)
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->view('Templates/pengumumanAdmin', $data);
        } else {
            $this->view('Templates/mainAdmin', $data); 
        }
    }

    public function tambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $judul = $_POST['judul'];
            $isi = $_POST['isi']; // Sesuai name di form view

            // Buat object Pengumuman baru dengan data
            $pengumumanBaru = new PengumumanAdmin($judul, $isi);

            // Panggil method insert
            if ($pengumumanBaru->insert($pengumumanBaru)) {
                header('Location: ' . APP_URL . '/pengumuman-admin');
                exit;
            }
        }
    }
    
    public function hapus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            
            $model = new PengumumanAdmin();
            if ($model->delete($id)) {
                header('Location: ' . APP_URL . '/pengumuman-admin');
                exit;
            }
        }
    }


    // --- TAMBAHAN BARU ---

    // 1. Menampilkan Form Edit
    public function edit() {
        // Kita gunakan POST agar ID tidak terlihat di URL dan lebih aman
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            
            $model = new PengumumanAdmin();
            $dataEdit = $model->getById($id);
            
            $data['judul'] = 'Edit Pengumuman';
            $data['p'] = $dataEdit; // Data pengumuman yang mau diedit

            // Logic AJAX agar tetap SPA
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // Kita akan buat file view baru: Templates/editPengumuman.php
                $this->view('Templates/editPengumuman', $data);
            } else {
                $this->view('Templates/mainAdmin', $data); 
            }
        }
    }

    // 2. Proses Simpan Perubahan
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $judul = $_POST['judul'];
            $isi = $_POST['isi'];

            // Buat objek baru
            $pengumuman = new PengumumanAdmin($judul, $isi);
            $pengumuman->setId($id); // Set ID yang mau diedit

            if ($pengumuman->update($pengumuman)) {
                // Redirect kembali ke halaman list
                header('Location: ' . APP_URL . '/pengumuman-admin');
                exit;
            }
        }
    }
}