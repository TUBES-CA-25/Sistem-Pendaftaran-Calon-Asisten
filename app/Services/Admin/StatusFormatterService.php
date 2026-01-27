<?php

namespace App\Services\Admin;

class StatusFormatterService
{
    /**
     * Get badge style for berkas status
     *
     * @param int|null $acceptedStatus Accepted status (null, 0, 1, 2)
     * @return array ['class' => string, 'text' => string]
     */
    public static function getBerkasStatusBadge($acceptedStatus)
    {
        $class = 'badge rounded-pill bg-secondary bg-opacity-10 text-secondary fw-semibold px-3 py-2';
        $text = 'Belum Upload';

        if (isset($acceptedStatus)) {
            if ($acceptedStatus == 1) {
                $class = 'badge rounded-pill bg-success bg-opacity-10 text-success fw-semibold px-3 py-2';
                $text = 'Disetujui';
            } elseif ($acceptedStatus == 2) {
                $class = 'badge rounded-pill bg-danger bg-opacity-10 text-danger fw-semibold px-3 py-2';
                $text = 'Ditolak';
            } elseif ($acceptedStatus == 0) {
                $class = 'badge rounded-pill bg-info bg-opacity-10 text-info fw-semibold px-3 py-2';
                $text = 'Proses';
            }
        }

        return ['class' => $class, 'text' => $text];
    }

    /**
     * Get badge style for presentation status
     *
     * @param bool $accepted Is accepted
     * @param bool $rejected Is rejected
     * @param bool $hasSchedule Has schedule
     * @return array ['class' => string, 'text' => string]
     */
    public static function getPresentationStatusBadge($accepted, $rejected, $hasSchedule)
    {
        if ($hasSchedule) {
            return ['class' => 'bg-primary text-white', 'text' => 'Terjadwal'];
        } elseif ($rejected) {
            return ['class' => 'bg-danger text-white', 'text' => 'Ditolak'];
        } elseif ($accepted) {
            return ['class' => 'bg-success text-white', 'text' => 'Diterima'];
        } else {
            return ['class' => 'bg-secondary text-white', 'text' => 'Menunggu'];
        }
    }

    /**
     * Get badge style for activity status
     *
     * @param string $status Activity status
     * @return array ['class' => string, 'text' => string]
     */
    public static function getActivityStatusBadge($status)
    {
        switch ($status) {
            case 'Selesai':
                return [
                    'class' => 'bg-success text-white border-0',
                    'text' => 'Selesai'
                ];
            case 'Sedang Berlangsung':
                return [
                    'class' => 'bg-warning text-dark border-0',
                    'text' => 'Berlangsung'
                ];
            case 'Akan Datang':
                return [
                    'class' => 'bg-info text-white border-0',
                    'text' => 'Akan Datang'
                ];
            default:
                return [
                    'class' => 'bg-light text-secondary border',
                    'text' => $status
                ];
        }
    }
}
