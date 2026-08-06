<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\DashboardAdmin;


class DashboardAdminController extends Controller
{
    public static function getTotalPendaftar(): int
    {
        return DashboardAdmin::getTotalPendaftar();
    }

    /**
     * Jumlah pendaftar per angkatan (dibaca dari digit 4-7 stambuk).
     * @return array<int, array{angkatan: string, jumlah: int}>
     */
    public static function getPendaftarPerAngkatan(): array
    {
        return DashboardAdmin::getPendaftarPerAngkatan();
    }

    public static function getPendaftarLulus(): int
    {
        return DashboardAdmin::getPendaftarLulus();
    }

    public static function getPendaftarPending(): int
    {
        return DashboardAdmin::getPendaftarPending();
    }

    public static function getPendaftarGagal(): int
    {
        return DashboardAdmin::getPendaftarGagal();
    }

    /**
     * @return array<int, array{tanggal: string}>
     */
    public static function getKegiatanByMonth(?int $year = null, ?int $month = null): array
    {
        $year ??= (int) date('Y');
        $month ??= (int) date('m');

        return DashboardAdmin::getKegiatanByMonth($year, $month);
    }

    /**
     * Kegiatan terdekat (mulai hari ini) untuk panel ringkas di dashboard.
     * @return array<int, array{id: int, judul: string, tanggal: string, deskripsi: string}>
     */
    public static function getKegiatanMendatang(int $limit = 4): array
    {
        return DashboardAdmin::getKegiatanMendatang($limit);
    }

    /**
     * @return array<string, array{jumlah: int}>
     */
    public static function getStatusKegiatan(): array
    {
        $statusKegiatan = DashboardAdmin::getStatusKegiatan();
        // Format activities with badge classes
        return self::formatActivitiesForView($statusKegiatan);
    }

    public static function storeKegiatan(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['judul']) || !isset($data['tanggal'])) {
            http_response_code(400);
            self::jsonError('Invalid input');
        }

        $success = DashboardAdmin::addKegiatan($data);

        if ($success) {
            self::jsonSuccess([], 'Kegiatan berhasil ditambahkan');
        } else {
            http_response_code(500);
            self::jsonError('Gagal menambahkan kegiatan');
        }
    }

    public static function updateKegiatan(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['id']) || !isset($data['judul']) || !isset($data['tanggal'])) {
            http_response_code(400);
            self::jsonError('Invalid input');
        }

        $success = DashboardAdmin::updateKegiatan($data);

        if ($success) {
            self::jsonSuccess([], 'Kegiatan berhasil diperbarui');
        } else {
            http_response_code(500);
            self::jsonError('Gagal memperbarui kegiatan');
        }
    }

    public static function destroyKegiatan(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['id'])) {
            http_response_code(400);
            self::jsonError('Invalid input');
        }

        $success = DashboardAdmin::deleteKegiatan((int)$data['id']);

        if ($success) {
            self::jsonSuccess([], 'Kegiatan berhasil dihapus');
        } else {
            http_response_code(500);
            self::jsonError('Gagal menghapus kegiatan');
        }
    }

    public static function saveDeadline(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['jenis']) || !isset($data['tanggal'])) {
            http_response_code(400);
            self::jsonError('Invalid input');
        }

        $success = DashboardAdmin::updateDeadline($data['jenis'], $data['tanggal']);

        if ($success) {
            self::jsonSuccess([], 'Deadline updated');
        } else {
            http_response_code(500);
            self::jsonError('Failed to update deadline');
        }
    }
    public static function getStats(): void
    {
        header('Content-Type: application/json');
        
        try {
            $total = self::getTotalPendaftar();
            $lulus = self::getPendaftarLulus();
            $pending = self::getPendaftarPending();
            $gagal = self::getPendaftarGagal();

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'total' => $total,
                    'lulus' => $lulus,
                    'pending' => $pending,
                    'gagal' => $gagal
                ]
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            self::jsonError($e->getMessage());
        }
    }

    public static function getAdminActivities(): void
    {
        header('Content-Type: application/json');
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Default to current date if no data passed
        $year = isset($data['year']) ? (int)$data['year'] : (int)date('Y');
        $month = isset($data['month']) ? (int)$data['month'] : (int)date('m');
        
        try {
            $eventsData = DashboardAdmin::getKegiatanByMonth($year, $month);
            $calendarWeeks = self::generateCalendarData($year, $month, $eventsData);
            
            ob_start();
            include __DIR__ . '/../../View/admin/dashboard/partials/calendar_table.php';
            $calendarHtml = ob_get_clean();
            
            self::jsonSuccess(['data' => $eventsData, 'calendarHtml' => $calendarHtml]);
        } catch (\Exception $e) {
            http_response_code(500);
            self::jsonError($e->getMessage());
        }
    }

    public static function getPresentationStats(): array
    {
        return DashboardAdmin::getPresentationStats();
    }

    // ==================== HELPER METHODS (menggantikan Services) ====================

    /**
     * Get badge class based on activity status
     */
    private static function getActivityStatusBadge($status)
    {
        $badgeClass = 'bg-slate-50 text-slate-500 border border-slate-200';

        if ($status === 'Selesai') {
            $badgeClass = 'bg-emerald-500 text-white';
        } elseif ($status === 'Sedang Berlangsung') {
            $badgeClass = 'bg-amber-50 text-amber-600 border border-amber-200';
        }

        return $badgeClass;
    }

    /**
     * Get activity status metadata
     */
    public static function getStatusMetadata()
    {
        return [
            'kelengkapan_berkas' => ['no' => 1, 'color' => 'danger'],
            'tes_tertulis' => ['no' => 2, 'color' => 'warning'],
            'tahap_wawancara' => ['no' => 3, 'color' => 'success'],
            'pengumuman' => ['no' => 4, 'color' => 'info']
        ];
    }

    /**
     * Format activity data for view display
     */
    private static function formatActivitiesForView($statusKegiatan)
    {
        $formatted = [];

        foreach ($statusKegiatan as $key => $status) {
            $formatted[$key] = $status;
            $formatted[$key]['badgeClass'] = self::getActivityStatusBadge($status['status']);
        }

        return $formatted;
    }

    /**
     * Generate calendar data for view
     */
    public static function generateCalendarData(int $year, int $month, array $eventsData): array
    {
        $firstDay = (int)date('w', mktime(0, 0, 0, $month, 1, $year));
        $daysInMonth = (int)date('t', mktime(0, 0, 0, $month, 1, $year));
        $adjustedStart = $firstDay === 0 ? 6 : $firstDay - 1; // Mon=0, Sun=6

        $todayYear = (int)date('Y');
        $todayMonth = (int)date('n');
        $todayDate = (int)date('j');

        $calendar = [];
        $date = 1;

        for ($i = 0; $i < 6; $i++) {
            $week = [];
            $hasDateInRow = false;
            
            for ($j = 0; $j < 7; $j++) {
                if ($i === 0 && $j < $adjustedStart) {
                    $week[] = null;
                } else if ($date > $daysInMonth) {
                    $week[] = null;
                } else {
                    $hasDateInRow = true;
                    $isToday = ($date === $todayDate && $month === $todayMonth && $year === $todayYear);
                    $formattedDate = sprintf('%04d-%02d-%02d', $year, $month, $date);
                    
                    $daysEvents = array_filter($eventsData, function($e) use ($formattedDate) {
                        return $e['tanggal'] === $formattedDate;
                    });
                    
                    $week[] = [
                        'date' => $date,
                        'isToday' => $isToday,
                        'events' => array_values($daysEvents)
                    ];
                    $date++;
                }
            }
            if ($hasDateInRow || $i === 0) {
                $calendar[] = $week;
            }
            if ($date > $daysInMonth) break;
        }

        return $calendar;
    }
}
