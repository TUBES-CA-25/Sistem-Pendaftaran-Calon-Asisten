<?php

namespace App\Controllers\admin;

use App\Core\Controller;
use App\Model\admin\DashboardAdmin;


class DashboardAdminController extends Controller
{
    public static function getTotalPendaftar(): int
    {
        return DashboardAdmin::getTotalPendaftar();
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
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $success = DashboardAdmin::addKegiatan($data);

        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Kegiatan berhasil ditambahkan']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan kegiatan']);
        }
    }

    public static function updateKegiatan(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['id']) || !isset($data['judul']) || !isset($data['tanggal'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $success = DashboardAdmin::updateKegiatan($data);

        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Kegiatan berhasil diperbarui']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui kegiatan']);
        }
    }

    public static function destroyKegiatan(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $success = DashboardAdmin::deleteKegiatan((int)$data['id']);

        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Kegiatan berhasil dihapus']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kegiatan']);
        }
    }

    public static function saveDeadline(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['jenis']) || !isset($data['tanggal'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $success = DashboardAdmin::updateDeadline($data['jenis'], $data['tanggal']);

        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Deadline updated']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update deadline']);
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
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
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
        $badgeClass = 'bg-light text-secondary border';

        if ($status === 'Selesai') {
            $badgeClass = 'bg-success text-white border-0';
        } elseif ($status === 'Sedang Berlangsung') {
            $badgeClass = 'bg-warning-subtle text-warning border border-warning';
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
}
