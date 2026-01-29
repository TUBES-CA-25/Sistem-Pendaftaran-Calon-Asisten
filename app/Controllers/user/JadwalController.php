<?php
namespace App\Controllers\User;

use App\Core\Controller;

/**
 * Controller untuk halaman Jadwal User (Wawancara dan kegiatan lainnya)
 */
class JadwalController extends Controller
{
    public function index()
    {
        // Render halaman jadwal untuk user
        // Menampilkan jadwal wawancara, tes, presentasi, dll
    }

    public static function getJadwalById()
    {
        if (!isset($_SESSION['user']['id'])) {
            return [];
        }

        $id = $_SESSION['user']['id'];

        // Ambil jadwal wawancara
        $wawancara = new \App\Model\Wawancara(0, 0, 0, 0);
        $jadwalWawancara = $wawancara->getJadwalKegiatanById($id);

        // Ambil jadwal presentasi jika ada
        // $presentasi = new \App\Model\Presentasi();
        // $jadwalPresentasi = $presentasi->getByMahasiswaId($id);

        return is_array($jadwalWawancara) ? $jadwalWawancara : [];
    }
}
