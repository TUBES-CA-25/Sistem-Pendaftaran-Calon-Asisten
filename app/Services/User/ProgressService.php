<?php

namespace App\Services\User;

class ProgressService
{
    /**
     * Calculate progress from tahapan selesai
     *
     * @param int $tahapanSelesai Number of completed stages
     * @param int $maxSteps Total number of steps (default: 4)
     * @return array ['completed' => int, 'total' => int, 'percentage' => float]
     */
    public static function calculateProgress($tahapanSelesai, $maxSteps = 4)
    {
        $percentage = min(($tahapanSelesai / $maxSteps) * 100, 100);

        return [
            'completed' => $tahapanSelesai,
            'total' => $maxSteps,
            'percentage' => $percentage
        ];
    }
}
