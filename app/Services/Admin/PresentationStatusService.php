<?php
namespace App\Services\Admin;

/**
 * Presentation Status Service
 * Handles presentation status badge formatting
 */
class PresentationStatusService
{
    /**
     * Get presentation status badge
     *
     * @param bool $isAccepted Whether presentation is accepted
     * @param bool $isRejected Whether presentation is rejected
     * @param bool $hasSchedule Whether presentation has schedule
     * @return array Badge class and text
     */
    public static function getPresentationStatusBadge($isAccepted, $isRejected, $hasSchedule)
    {
        if ($hasSchedule) {
            return [
                'class' => 'bg-primary text-white',
                'text' => 'Terjadwal'
            ];
        } elseif ($isRejected) {
            return [
                'class' => 'bg-danger text-white',
                'text' => 'Ditolak'
            ];
        } elseif ($isAccepted) {
            return [
                'class' => 'bg-success text-white',
                'text' => 'Diterima'
            ];
        } else {
            return [
                'class' => 'bg-secondary text-white',
                'text' => 'Menunggu'
            ];
        }
    }

    /**
     * Format mahasiswa list with presentation status badges
     *
     * @param array $mahasiswaList Raw mahasiswa data
     * @return array Formatted mahasiswa data with status badges
     */
    public static function formatMahasiswaListForView($mahasiswaList)
    {
        $formatted = [];

        foreach ($mahasiswaList as $mahasiswa) {
            $isAccepted = isset($mahasiswa['is_accepted']) && $mahasiswa['is_accepted'] == 1;
            $isRejected = isset($mahasiswa['is_accepted']) && $mahasiswa['is_accepted'] == 2;
            $hasSchedule = isset($mahasiswa['has_schedule']) && $mahasiswa['has_schedule'];

            $statusBadge = self::getPresentationStatusBadge($isAccepted, $isRejected, $hasSchedule);

            $mahasiswa['statusBadge'] = $statusBadge;
            $formatted[] = $mahasiswa;
        }

        return $formatted;
    }
}
