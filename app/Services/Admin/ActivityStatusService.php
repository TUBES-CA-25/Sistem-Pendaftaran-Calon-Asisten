<?php
namespace App\Services\Admin;

/**
 * Activity Status Service
 * Handles activity status badge formatting and metadata
 */
class ActivityStatusService
{
    /**
     * Get badge class based on activity status
     *
     * @param string $status Activity status (Selesai, Sedang Berlangsung, Akan Datang)
     * @return string Badge CSS class
     */
    public static function getActivityStatusBadge($status)
    {
        // Default: Akan Datang
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
     *
     * @return array Status metadata with colors and numbers
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
     *
     * @param array $statusKegiatan Raw activity status data
     * @return array Formatted activity data with badge classes
     */
    public static function formatActivitiesForView($statusKegiatan)
    {
        $formatted = [];

        foreach ($statusKegiatan as $key => $status) {
            $formatted[$key] = $status;
            $formatted[$key]['badgeClass'] = self::getActivityStatusBadge($status['status']);
        }

        return $formatted;
    }
}
