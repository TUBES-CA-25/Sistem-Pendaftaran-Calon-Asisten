<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Model\Wawancara;
class JadwalWawancaraController extends Controller
{
    public static function getAll()
    {
        // Simply fetch all schedules without merging by stambuk
        $wawancaraModel = new \App\Model\Wawancara();
        return $wawancaraModel->getAllWawancaraOnly(); 
    }

    public function getAllFilterByIdRuangan()
    {
        self::requireAuth();
        header('Content-Type: application/json');
        ob_clean();

        try {
            if (!isset($_SESSION['user']['id'])) {
                error_log("Error: User not logged in");
                self::jsonError('User not logged in');
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['id']) || !is_numeric($input['id'])) {
                self::jsonError('ID ruangan tidak valid');
                exit;
            }

            $id = (int) $input['id']; 

        
            $wawancara = new Wawancara(0, 0, 0, 0);
            if($id === 0) {
                $data = $wawancara->getAll();
            } else {
                $data = $wawancara->getAllFilterByRuangan($id);
            }

            // Resolve photo path for each row
            foreach ($data as &$row) {
                $row['photoPath'] = \App\Controllers\HomeController::getUserPhotoPath($row['foto'] ?? 'default.png');
            }

            if (empty($data)) {
                self::jsonError('Data tidak ditemukan');
                exit;
            }

            self::jsonSuccess(['data' => $data]);
            exit;

        } catch (\Exception $e) {
            http_response_code(500);
            self::jsonError($e->getMessage());
            exit;
        }
    }
    public static function getAllById()
    {
        if (!isset($_SESSION['user']['id'])) {
            error_log("Error: User not logged in");
            return [];
        }

        $id = $_SESSION['user']['id'];
        $wawancara = new Wawancara(0, 0, 0, 0);

        try {
            // Updated: Fetch all activities instead of just interviews
            $data = $wawancara->getJadwalKegiatanById($id);
            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            error_log("Error in getAllById (Jadwal Kegiatan): " . $e->getMessage());
            return [];
        }
    }

    public function save()
    {
        self::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $selectedMahasiswa = $input['id'] ?? [];
        $id_ruangan = $input['ruangan'] ?? "";
        $jenis_wawancara = $input['wawancara'] ?? "";
        $waktu = $input['waktu'] ?? "";
        $tanggal = $input['tanggal'] ?? "";

        if (empty($selectedMahasiswa) || empty($id_ruangan) || empty($jenis_wawancara) || empty($waktu) || empty($tanggal)) {
            self::jsonError('All fields are required');
        }

        self::requireTanggalTidakLampau($tanggal, 'Tanggal wawancara');

        // Wawancara adalah tahap ketiga: tes tertulis dan presentasi harus
        // sudah dihadiri. Dropdown sudah menyaring, tetapi endpoint bisa
        // dipanggil langsung sehingga aturannya ditegakkan ulang di sini.
        // Berbeda dari presentasi, payload ini berisi id MAHASISWA langsung.
        foreach ((array) $selectedMahasiswa as $idMahasiswa) {
            $alasan = \App\Model\Mahasiswa::alasanBelumBolehTahap((int) $idMahasiswa, 'wawancara');
            if ($alasan !== null) {
                self::jsonError($alasan);
            }
        }

        // Satu ruangan tidak bisa dipakai dua kegiatan pada jam yang sama.
        // Aturan yang sama sudah berlaku di penjadwalan tes tertulis; dulu
        // alur wawancara sama sekali tidak memeriksanya meski menulis ke tabel
        // yang sama, sehingga dua peserta bisa terjadwal di slot identik.
        $bentrok = \App\Model\Wawancara::cariBentrokJadwal($id_ruangan, $tanggal, $waktu);
        if ($bentrok !== null) {
            self::jsonError('Ruangan sudah dipakai pada jam tersebut oleh ' . $bentrok);
        }

        try {
            $wawancara = new Wawancara(
                $id_ruangan,
                $jenis_wawancara,
                $waktu,
                $tanggal
            );
            if ($wawancara->save($wawancara, $selectedMahasiswa)) {
                self::jsonSuccess([], 'Jadwal wawancara berhasil disimpan');
            } else {
                self::jsonError('Jadwal gagal disimpan');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
    public function update()
    {
        self::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }
        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? "";
        $id_ruangan = $input['ruangan'] ?? "";
        $jenis_wawancara = $input['jenisWawancara'] ?? "";
        $waktu = $input['waktu'] ?? "";
        $tanggal = $input['tanggal'] ?? "";
        if (empty($id) || empty($id_ruangan) || empty($jenis_wawancara) || empty($waktu) || empty($tanggal)) {
            self::jsonError('All fields are required');
        }

        self::requireTanggalTidakLampau($tanggal, 'Tanggal wawancara');

        // $id dikecualikan supaya baris yang sedang diubah tidak dianggap
        // bentrok dengan dirinya sendiri.
        $bentrok = \App\Model\Wawancara::cariBentrokJadwal($id_ruangan, $tanggal, $waktu, $id);
        if ($bentrok !== null) {
            self::jsonError('Ruangan sudah dipakai pada jam tersebut oleh ' . $bentrok);
        }

        $wawancara = new Wawancara(
            $id_ruangan,
            $jenis_wawancara,
            $waktu,
            $tanggal
        );
        if ($wawancara->updateWawancara($id, $wawancara)) {
            self::jsonSuccess([], 'Jadwal wawancara berhasil diupdate');
        } else {
            self::jsonError('Jadwal gagal diupdate');
        }
    }
    public function delete()
    {
        self::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }
        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? "";
        if (empty($id)) {
            self::jsonError('All fields are required');
        }
        $wawancara = new Wawancara(
            0,
            0,
            0,
            0
        );
        if ($wawancara->deleteWawancara($id)) {
            self::jsonSuccess([], 'Jadwal wawancara berhasil dihapus');
        } else {
            self::jsonError('Jadwal gagal dihapus');
        }
    }

}