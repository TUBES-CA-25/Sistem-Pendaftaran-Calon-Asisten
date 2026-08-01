<?php
namespace App\Controllers\Admin;
use App\Model\Absensi;
use App\Model\Mahasiswa;
use App\Core\Controller;
class RekapKehadiranController extends Controller
{
    public static function viewAbsensi()
    {
        $absensi = new Absensi();
        $data = $absensi->getAbsensi();
        return $data;

    }

    public function saveData()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                self::jsonError('Invalid request method');
            }
            if (!isset($_SESSION['user']['id'])) {
                self::jsonError('User not logged in');
            }
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['mahasiswa'] ?? null;
            $wawancaraI = !empty($input['wawancara1']) ? $input['wawancara1'] : '-';
            $wawancaraII = !empty($input['wawancara2']) ? $input['wawancara2'] : '-';
            $wawancaraIII = '-'; // Removed Wawancara III
            $tesTertulis = !empty($input['tesTertulis']) ? $input['tesTertulis'] : '-';
            $presentasi = !empty($input['presentasi']) ? $input['presentasi'] : '-';

            if (empty($id)) {
                self::jsonError('Mahasiswa belum dipilih');
            }
            
            $absensi = new Absensi(
                null,
                $wawancaraI,
                $wawancaraII,
                $wawancaraIII,
                $tesTertulis,
                $presentasi
            );
            if ($absensi->addMahasiswa($absensi, $id)) {
                self::jsonSuccess([], 'Absensi berhasil disimpan');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
    public function updateData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }
        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? null;
        $mhsId = $input['mhsId'] ?? null;
        $wawancaraI = $input['wawancaraI'] ?? '-';
        $wawancaraII = $input['wawancaraII'] ?? '-';
        $wawancaraIII = '-'; 
        $tesTertulis = $input['tesTertulis'] ?? '-';
        $tesTertulis = $input['tesTertulis'] ?? '-';
        $presentasi = $input['presentasi'] ?? '-';
        $statusAkhir = $input['statusAkhir'] ?? null;

        if (!$mhsId) {
            self::jsonError('ID Mahasiswa tidak valid');
        }

        $absensi = new Absensi(
            $id,
            $wawancaraI,
            $wawancaraII,
            $wawancaraIII,
            $tesTertulis,
            $presentasi
        );

        // If ID is null, we need to insert a NEW record for this mahasiswa
        if (!$id || $id === '') {
            if ($absensi->addMahasiswa($absensi, [$mhsId])) {
                self::jsonSuccess([], 'Absensi berhasil dibuat');
            } else {
                self::jsonError('Gagal membuat absensi');
            }
        } else {
            // Update existing record
            if($absensi->updateAbsensi()) {
                // Update Status Akhir if provided
                if ($statusAkhir && $mhsId) {
                    $mahasiswa = new Mahasiswa();
                    $mahasiswa->updateStatusAkhir($mhsId, $statusAkhir);
                }

                self::jsonSuccess([], 'Absensi berhasil diupdate');
            } else {
                self::jsonError('Gagal mengupdate absensi');
            }
        }
    }

    public function deleteData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('Invalid request method');
        }

        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            self::jsonError('ID is required');
        }

        try {
            $absensi = new Absensi();
            if ($absensi->deleteAbsensi($id)) {
                self::jsonSuccess([], 'Absensi berhasil dihapus');
            } else {
                self::jsonError('Gagal menghapus absensi');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
    
    /**
     * Backfill absensi for existing mahasiswa (one-time operation)
     */
    public function backfillData() {
        if (!isset($_SESSION['user']['id'])) {
            self::jsonError('User not logged in');
        }
        
        try {
            $absensi = new Absensi();
            $count = $absensi->backfillAbsensi();
            
            self::jsonSuccess(['count' => $count], "Berhasil menambahkan $count mahasiswa ke rekap");
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
}