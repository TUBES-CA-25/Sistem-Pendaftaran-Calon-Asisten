<?php

namespace App\Controllers\Concerns;

use App\Controllers\Admin\DashboardAdminController;
use App\Controllers\Admin\JadwalPresentasiController;
use App\Controllers\Admin\JadwalWawancaraController;
use App\Controllers\Admin\NilaiController;
use App\Controllers\Admin\PesertaController;
use App\Controllers\User\PresentasiUserController;
use App\Controllers\Admin\RekapKehadiranController;
use App\Controllers\Admin\RuanganController;
use App\Controllers\User\TesTulisController;

/**
 * Penyedia data untuk halaman sisi ADMIN (dashboard, peserta, ruangan, jadwal, nilai).
 *
 * Dipisah dari HomeController agar tiap berkas tetap pendek dan mudah dirawat.
 * Isi method TIDAK diubah - hanya dipindahkan.
 */
trait ProvidesAdminData
{
    private function getDashboardAdminData(): array
    {
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');
        $kegiatanBulanIni = DashboardAdminController::getKegiatanByMonth($currentYear, $currentMonth) ?? [];

        return [
            'totalPendaftar' => DashboardAdminController::getTotalPendaftar(),
            'pendaftarPerAngkatan' => DashboardAdminController::getPendaftarPerAngkatan(),
            'pendaftarLulus' => DashboardAdminController::getPendaftarLulus(),
            'pendaftarPending' => DashboardAdminController::getPendaftarPending(),
            'pendaftarGagal' => DashboardAdminController::getPendaftarGagal(),
            'statusKegiatan' => DashboardAdminController::getStatusKegiatan(),
            'kegiatanBulanIni' => $kegiatanBulanIni,
            'kegiatanMendatang' => DashboardAdminController::getKegiatanMendatang(4),
            'jadwalPresentasiMendatang' => JadwalPresentasiController::getUpcomingJadwal(5),
            'presentationStats' => DashboardAdminController::getPresentationStats(),
            'statusMeta' => DashboardAdminController::getStatusMetadata(),
            'calendarWeeks' => DashboardAdminController::generateCalendarData($currentYear, $currentMonth, $kegiatanBulanIni)
        ];
    }

    /**
     * Data untuk ruangan view
     */

    private function getRuanganData(): array
    {
        return [
            'ruanganList' => RuanganController::viewAllRuangan() ?? []
        ];
    }

    /**
     * Data untuk daftar peserta view
     */

    private function getDaftarPesertaData(): array
    {
        $mahasiswa = PesertaController::viewAllMahasiswa() ?? [];

        // Format each participant
        $formattedMahasiswa = [];
        foreach ($mahasiswa as $mhs) {
            // Format participant data with photoPath and statusBadge
            $formattedMahasiswa[] = $this->formatParticipantForView($mhs);
        }

        return [
            'mahasiswaList' => $formattedMahasiswa,
            'result' => $formattedMahasiswa
        ];
    }

    /**
     * Data untuk daftar hadir view
     */

    private function getDaftarHadirData(): array
    {
        return [
            'absensiList' => RekapKehadiranController::viewAbsensi() ?? [],
            'mahasiswaList' => PesertaController::viewAllMahasiswa() ?? []
        ];
    }

    /**
     * Data untuk presentasi admin view (Legacy/Combined)
     */

    private function getPresentasiAdminData(): array
    {
        $mahasiswaList = PresentasiUserController::viewAllForAdmin() ?? [];

        // Format mahasiswa list with status badges
        $formattedMahasiswaList = $this->formatMahasiswaListForView($mahasiswaList);

        return [
            'mahasiswaList' => $formattedMahasiswaList,
            'mahasiswaAccStatus' => PresentasiUserController::viewAllAccStatusForAdmin() ?? [],
            'ruanganList' => RuanganController::viewAllRuangan() ?? [],
            'jadwalPresentasi' => JadwalPresentasiController::getJadwalPresentasi() ?? []
        ];
    }

    /**
     * Data untuk halaman pengajuan judul
     */

    private function getPengajuanJudulData(): array
    {
        return [
            'mahasiswaList' => PresentasiUserController::viewAllForAdmin() ?? [],
            'mahasiswaAccStatus' => PresentasiUserController::viewAllAccStatusForAdmin() ?? []
        ];
    }

    /**
     * Data untuk halaman jadwal presentasi
     */

    private function getJadwalPresentasiData(): array
    {
        return [
            'ruanganList' => RuanganController::viewAllRuangan() ?? [],
            'jadwalPresentasi' => JadwalPresentasiController::getJadwalPresentasi() ?? []
        ];
    }

    /**
     * Data untuk tes tulis admin view
     */

    private function getTesTulisAdminData(): array
    {
        $examData = TesTulisController::getAdminExamPageData();
        return [
            'allSoal' => $examData['allSoal'] ?? [],
            'bankSoalList' => $examData['bankSoalList'] ?? [],
            'stats' => $examData['stats'] ?? []
        ];
    }

    /**
     * Data untuk wawancara admin view
     * Only show mahasiswa who have completed Presentasi
     */

    private function getWawancaraAdminData(): array
    {
        return [
            'wawancara' => JadwalWawancaraController::getAll() ?? [],
            'mahasiswaList' => \App\Model\Mahasiswa::getAvailableForWawancara() ?? [],
            'ruanganList' => RuanganController::viewAllRuangan() ?? []
        ];
    }

    /**
     * Data untuk nilai admin view
     */

    private function getNilaiAdminData(): array
    {
        return [
            'nilai' => NilaiController::getAllNilaiAkhirMahasiswa() ?? []
        ];
    }

    // ==================== HELPER METHODS (menggantikan Services) ====================

    /**
     * Format date from string
     */
}
