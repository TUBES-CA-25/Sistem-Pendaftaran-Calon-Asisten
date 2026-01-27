<?php

namespace App\Services\User;

class ExamAccessService
{
    /**
     * Check if user can access exam
     *
     * @param bool $absensiTesTertulis Has already taken the exam
     * @param bool $berkasStatus Berkas completion status
     * @param bool $biodataStatus Biodata completion status
     * @return array ['allowed' => bool, 'reason' => string, 'message' => string]
     */
    public static function canAccessExam($absensiTesTertulis, $berkasStatus, $biodataStatus)
    {
        // Check if already completed
        if ($absensiTesTertulis) {
            return [
                'allowed' => false,
                'reason' => 'completed',
                'message' => 'Anda sudah mengikuti tes tertulis'
            ];
        }

        // Check biodata completion
        if (!$biodataStatus) {
            return [
                'allowed' => false,
                'reason' => 'biodata_incomplete',
                'message' => 'Lengkapi biodata terlebih dahulu'
            ];
        }

        // Check berkas completion
        if (!$berkasStatus) {
            return [
                'allowed' => false,
                'reason' => 'berkas_incomplete',
                'message' => 'Lengkapi berkas terlebih dahulu'
            ];
        }

        // All checks passed
        return [
            'allowed' => true,
            'reason' => 'ok',
            'message' => ''
        ];
    }

    /**
     * Get user-friendly access denied message
     *
     * @param string $reason Access denied reason
     * @return string User-friendly message
     */
    public static function getAccessDeniedMessage($reason)
    {
        $messages = [
            'completed' => 'Anda sudah mengikuti tes tertulis',
            'biodata_incomplete' => 'Lengkapi biodata terlebih dahulu',
            'berkas_incomplete' => 'Lengkapi berkas terlebih dahulu',
        ];

        return $messages[$reason] ?? 'Akses ditolak';
    }
}
