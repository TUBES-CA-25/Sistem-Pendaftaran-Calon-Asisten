<?php

namespace App\Controllers\Concerns;

/**
 * Pemformatan & penyusunan data untuk tampilan (tanggal, badge status, progres).
 *
 * Dipisah dari HomeController agar tiap berkas tetap pendek dan mudah dirawat.
 * Isi method TIDAK diubah - hanya dipindahkan.
 */
trait FormatsViewData
{
    private function formatDate($date, $format = 'd F Y')
    {
        if (empty($date)) {
            return '-';
        }
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Format time from string
     */

    private function formatTime($time, $format = 'H:i')
    {
        if (empty($time)) {
            return '-';
        }
        $timestamp = strtotime($time);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Get full path for user photo
     */

    private function formatProfileDisplay($biodata, $user, $photo)
    {
        $nama = $biodata['namaLengkap'] ?? $user['username'] ?? 'User';
        $hasValidPhoto = $this->hasValidPhoto($photo);

        return [
            'hasValidPhoto' => $hasValidPhoto,
            'photoPath' => $this->getUserPhotoPath($photo),
            'initials' => $this->generateInitials($nama),
            'displayName' => $nama
        ];
    }

    /**
     * Calculate progress from tahapan selesai
     */

    private function calculateProgress($tahapanSelesai, $maxSteps = 4)
    {
        $percentage = min(($tahapanSelesai / $maxSteps) * 100, 100);

        return [
            'completed' => $tahapanSelesai,
            'total' => $maxSteps,
            'percentage' => $percentage
        ];
    }

    /**
     * Check if user can access exam
     */

    private function canAccessExam($absensiTesTertulis, $berkasStatus, $biodataStatus)
    {
        if ($absensiTesTertulis) {
            return [
                'allowed' => false,
                'reason' => 'completed',
                'message' => 'Anda sudah mengikuti tes tertulis'
            ];
        }

        if (!$biodataStatus) {
            return [
                'allowed' => false,
                'reason' => 'biodata_incomplete',
                'message' => 'Lengkapi biodata terlebih dahulu'
            ];
        }

        if (!$berkasStatus) {
            return [
                'allowed' => false,
                'reason' => 'berkas_incomplete',
                'message' => 'Lengkapi berkas terlebih dahulu'
            ];
        }

        return [
            'allowed' => true,
            'reason' => 'ok',
            'message' => ''
        ];
    }

    /**
     * Get badge style for berkas status
     */

    private function getBerkasStatusBadge($acceptedStatus)
    {
        $class = 'bg-slate-50 text-slate-500 border border-slate-200';
        $text = 'Belum Upload';

        if (isset($acceptedStatus)) {
            if ($acceptedStatus == 1) {
                $class = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                $text = 'Disetujui';
            } elseif ($acceptedStatus == 2) {
                $class = 'bg-red-50 text-red-700 border border-red-200';
                $text = 'Ditolak';
            } elseif ($acceptedStatus == 0) {
                $class = 'bg-blue-50 text-blue-700 border border-blue-200';
                $text = 'Proses';
            }
        }

        return ['class' => $class, 'text' => $text];
    }

    /**
     * Format participant data for view display
     */

    private function formatParticipantForView($rawData)
    {
        $formatted = $rawData;

        $photoName = $rawData['berkas']['foto'] ?? 'default.png';
        $formatted['photoPath'] = $this->getUserPhotoPath($photoName);

        $acceptedStatus = $rawData['berkas']['accepted'] ?? null;
        $formatted['statusBadge'] = $this->getBerkasStatusBadge($acceptedStatus);

        return $formatted;
    }

    /**
     * Get presentation status badge
     */

    private function getPresentationStatusBadge($isAccepted, $isRejected, $hasSchedule)
    {
        if ($hasSchedule) {
            return [
                'class' => 'bg-blue-50 text-blue-700 border border-blue-200',
                'text' => 'Terjadwal'
            ];
        } elseif ($isRejected) {
            return [
                'class' => 'bg-red-50 text-red-700 border border-red-200',
                'text' => 'Ditolak'
            ];
        } elseif ($isAccepted) {
            return [
                'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'text' => 'Diterima'
            ];
        } else {
            return [
                'class' => 'bg-slate-50 text-slate-500 border border-slate-200',
                'text' => 'Menunggu'
            ];
        }
    }

    /**
     * Format mahasiswa list with presentation status badges
     */

    private function formatMahasiswaListForView($mahasiswaList)
    {
        $formatted = [];

        foreach ($mahasiswaList as $mahasiswa) {
            $isAccepted = isset($mahasiswa['is_accepted']) && $mahasiswa['is_accepted'] == 1;
            $isRejected = isset($mahasiswa['is_accepted']) && $mahasiswa['is_accepted'] == 2;
            $hasSchedule = isset($mahasiswa['has_schedule']) && $mahasiswa['has_schedule'];

            $statusBadge = $this->getPresentationStatusBadge($isAccepted, $isRejected, $hasSchedule);

            $mahasiswa['statusBadge'] = $statusBadge;
            $formatted[] = $mahasiswa;
        }

        return $formatted;
    }

    /**
     * Get admin photo path
     * Returns custom photo if exists, otherwise returns default iclabs logo
     */
}
