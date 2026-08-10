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

    /**
     * Menegakkan urutan pengisian kehadiran:
     * tes tertulis -> presentasi -> wawancara I -> wawancara II.
     *
     * Satu tahap hanya boleh diisi kalau tahap sebelumnya bernilai 'Hadir'.
     * Peserta yang tidak hadir (atau belum diabsen) di satu tahap tidak
     * mungkin mengikuti tahap sesudahnya, jadi mengisinya pasti keliru.
     *
     * 'Izin' sengaja TIDAK membuka tahap berikutnya - peserta memang tidak
     * hadir. Kalau tetap harus dilanjutkan, admin mengubah tahap itu ke
     * 'Hadir' lebih dulu supaya keputusannya tercatat.
     *
     * Dropdown di antarmuka sudah dikunci, tetapi endpoint bisa dipanggil
     * langsung sehingga aturannya diulang di sini.
     */
    private static function requireUrutanAbsensi(?string ...$nilaiTahap): void
    {
        $label = ['Tes Tertulis', 'Presentasi', 'Wawancara I', 'Wawancara II'];

        foreach ($nilaiTahap as $i => $nilai) {
            if ($i === 0) {
                continue; // tahap pertama selalu boleh diisi
            }

            // Kosong / '-' berarti tidak diisi - itu selalu sah.
            $diisi = $nilai !== null && $nilai !== '' && $nilai !== '-';
            if (!$diisi) {
                continue;
            }

            if (($nilaiTahap[$i - 1] ?? '') !== 'Hadir') {
                self::jsonError(
                    $label[$i] . ' belum bisa diisi: ' . $label[$i - 1] .
                    ' harus bernilai "Hadir" terlebih dahulu'
                );
            }
        }
    }

    /**
     * Menolak penandaan 'Hadir' untuk tahap yang peserta belum dijadwalkan.
     *
     * Peserta tidak mungkin hadir di kegiatan yang tidak pernah dijadwalkan
     * untuknya, jadi 'Hadir' tanpa jadwal hampir pasti salah entri.
     *
     * 'Izin' dan 'Alpha' TIDAK dibatasi: keduanya justru menandai peserta yang
     * tidak mengikuti kegiatan, dan admin kadang perlu mencatatnya lebih dulu
     * (mis. peserta mengundurkan diri sebelum jadwal dibuat).
     *
     * Sumber jadwal berbeda per tahap:
     *  - Tes Tertulis  : tabel `wawancara`, jenis_wawancara LIKE 'Tes Tertulis%'
     *  - Presentasi    : `jadwal_presentasi` yang ditautkan lewat `presentasi`
     *  - Wawancara I/II: tabel `wawancara`, dibedakan sufiks 'lab I' / 'lab II'
     */
    private static function requirePunyaJadwal(
        int $idMahasiswa,
        ?string $tesTertulis,
        ?string $presentasi,
        ?string $wawancaraI,
        ?string $wawancaraII
    ): void {
        $db = \App\Core\Model::getDB();

        $hitung = function (string $sql) use ($db, $idMahasiswa): int {
            $stmt = $db->prepare($sql);
            $stmt->execute([$idMahasiswa]);
            return (int) $stmt->fetchColumn();
        };

        $cek = [
            'Tes Tertulis' => [
                $tesTertulis,
                "SELECT COUNT(*) FROM wawancara
                  WHERE id_mahasiswa = ? AND jenis_wawancara LIKE 'Tes Tertulis%'",
                'Penjadwalan > Tes Tertulis',
            ],
            'Presentasi' => [
                $presentasi,
                "SELECT COUNT(*) FROM jadwal_presentasi jp
                    JOIN presentasi p ON jp.id_presentasi = p.id
                  WHERE p.id_mahasiswa = ?",
                'Penjadwalan > Presentasi',
            ],
            'Wawancara I' => [
                $wawancaraI,
                "SELECT COUNT(*) FROM wawancara
                  WHERE id_mahasiswa = ? AND jenis_wawancara LIKE '%lab I'",
                'Penjadwalan > Wawancara',
            ],
            'Wawancara II' => [
                $wawancaraII,
                "SELECT COUNT(*) FROM wawancara
                  WHERE id_mahasiswa = ? AND jenis_wawancara LIKE '%lab II'",
                'Penjadwalan > Wawancara',
            ],
        ];

        foreach ($cek as $label => [$nilai, $sql, $menu]) {
            if ($nilai !== 'Hadir') {
                continue; // hanya 'Hadir' yang memerlukan jadwal
            }
            if ($hitung($sql) === 0) {
                self::jsonError(
                    $label . ' belum bisa ditandai "Hadir": peserta belum dijadwalkan. '
                    . 'Buat jadwalnya lebih dulu di menu ' . $menu . '. '
                    . 'Gunakan "Izin" atau "Alpha" bila peserta memang tidak mengikuti.'
                );
            }
        }
    }

    /**
     * Menolak status akhir "Lulus" bila peserta belum menuntaskan seluruh
     * tahapan seleksi.
     *
     * Peserta hanya dinyatakan lulus setelah hadir di keempat tahap: tes
     * tertulis, presentasi, wawancara I, dan wawancara II. Meluluskan peserta
     * yang melewatkan salah satunya berarti hasil seleksi tidak mencerminkan
     * proses yang sebenarnya.
     *
     * "Tidak Lulus" dan "Pending" sengaja TIDAK dibatasi. Peserta yang gagal
     * atau mengundurkan diri di tengah jalan justru tidak akan pernah
     * menyelesaikan semua tahap - kalau dibatasi, statusnya tidak akan pernah
     * bisa ditetapkan dan mereka tertahan sebagai "Pending" selamanya.
     */
    private static function requireTahapanTuntasUntukLulus(
        ?string $statusAkhir,
        ?string $tesTertulis,
        ?string $presentasi,
        ?string $wawancaraI,
        ?string $wawancaraII
    ): void {
        if ($statusAkhir !== 'Lulus') {
            return;
        }

        $tahap = [
            'Tes Tertulis' => $tesTertulis,
            'Presentasi'   => $presentasi,
            'Wawancara I'  => $wawancaraI,
            'Wawancara II' => $wawancaraII,
        ];

        $belum = [];
        foreach ($tahap as $label => $nilai) {
            if ($nilai !== 'Hadir') {
                $belum[] = $label;
            }
        }

        if ($belum) {
            self::jsonError(
                'Status "Lulus" belum bisa ditetapkan: ' . implode(', ', $belum)
                . ' belum ditandai "Hadir". Lengkapi seluruh tahapan terlebih dahulu, '
                . 'atau pilih "Tidak Lulus" bila peserta tidak melanjutkan seleksi.'
            );
        }
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

        self::requireUrutanAbsensi($tesTertulis, $presentasi, $wawancaraI, $wawancaraII);
        self::requirePunyaJadwal((int) $mhsId, $tesTertulis, $presentasi, $wawancaraI, $wawancaraII);
        self::requireTahapanTuntasUntukLulus($statusAkhir, $tesTertulis, $presentasi, $wawancaraI, $wawancaraII);

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